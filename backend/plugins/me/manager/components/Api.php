<?php namespace Me\Manager\Components;

use Cms\Classes\ComponentBase;
use RainLab\User\Facades\Auth;
use Me\Manager\Models\Key;
use Me\Manager\Models\Record;
use Me\Manager\Classes\CryptManager;
use Me\Manager\Models\Project;
use Me\Manager\Models\ProjectAccess;
use RainLab\User\Models\User;

class Api extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'Api Component',
            'description' => 'No description provided yet...'
        ];
    }

    public $user = null;
    public $isAdmin = false;
    public $action = null;

    public $methods = [
        'no_auth' => [
            'Login',
            'CheckLogin',
        ]
    ];

    public function defineProperties()
    {
        return [];
    }

    public function onRun(){

        header("Access-Control-Allow-Origin: http://localhost:3000");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: withcredentials");

        $this->user = Auth::getUser();
        if ($this->user && $this->user->groups()->where('code', 'admin')->first()) {
            $this->isAdmin = true;
        }

        $this->action = $this->param('action');

        if (!$this->user && !in_array($this->action, $this->methods['no_auth'])) {
            return \Response::json([
                'success' => false,
                'message' => 'В доступе отказано'
            ]);
        }

        if (method_exists($this, "action" . $this->action)) {
            return \Response::json($this->{"action" . $this->action}());
        } else {
            return \Response::json($this->actionDefault());
        }

    }

    public function actionDefault(){
        return [
            'success' => false,
            'message' => 'Метод не найден',
        ];
    }

    public function actionLogin(){
        try {
            $user = Auth::authenticate([
                'login' => post('login'),
                'password' => post('password')
            ]); 
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
        

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Неверное имя пользователя или пароль'
            ];
        }

        return [
            'success' => true,
        ];
    }

    public function actionCheckLogin(){
        if ($this->user) {
            return [
                'success' => true,
                'auth' => true,
            ];
        } else {
            return [
                'success' => true,
                'auth' => false,
            ];
        }
    }

    public function actionGetKeyChain(){
        $key = Key::where('user_id', $this->user->id)->first();

        if (!$key) {
            return [
                'success' => true,
                'status' => 0,
                'message' => 'Хранилище ключей отсутствует. Создайте хранилище.'
            ];
        } else {
            return [
                'success' => true,
                'status' => 1,
            ];
        }
    }

    public function actionCreateKeyChain(){
        $key = Key::where('user_id', $this->user->id)->first();
        if ($key) {
            return [
                'success' => false,
                'message' => 'Хранилище уже существует'
            ];
        }

        $master_password = post('master_password', false);
        if (!$master_password) {
            return [
                'success' => false,
                'message' => 'Отсутствует master пароль',
            ];
        }

        $keypair = CryptManager::generateRSAKeypair();

        $key = new Key;
        $key->user_id = $this->user->id;
        $key->public_key = $keypair['public_key'];

        $stored_private_key = CryptManager::aesEncrypt($keypair['private_key'], $master_password);

        $key->private_key = $stored_private_key['ciphertext'];
        $key->aes_iv = $stored_private_key['iv'];
        $key->aes_tag = $stored_private_key['tag'];

        $key->save();

        return [
            'success' => true,
            'message' => 'Хранилище успешно создано',
        ];

    }

    public function actionDeleteKeyChain(){

        Key::where('user_id', $this->user->id)->delete();
        Record::where('user_id', $this->user->id)->delete();

        return [
            'success' => true,
            'message' => 'Хранилище удалено',
        ];

    }

    public function actionChangeMasterPassword(){

        $master_password = post('master_password', false);
        if (!$master_password) {
            return [
                'success' => false,
                'message' => 'Отсутствует master пароль',
            ];
        }

        $new_password = post('new_password', false);

        if (!$new_password) {
            return [
                'success' => false,
                'message' => 'Отсутствует новый master пароль',
            ];
        }

        $key = Key::where('user_id', $this->user->id)->first();

        $privKey = CryptManager::aesDecrypt($key->private_key, $master_password, $key->aes_iv, $key->aes_tag);
        if (!$privKey){
            return [
                'success' => false,
                'message' => 'Неверный master пароль',
            ];
        }

        $stored_private_key = CryptManager::aesEncrypt($privKey, $new_password);

        $key->private_key = $stored_private_key['ciphertext'];
        $key->aes_iv = $stored_private_key['iv'];
        $key->aes_tag = $stored_private_key['tag'];

        $key->save();

        return [
            'success' => true,
            'message' => 'Пароль сменен',
        ];

    }

    public function actionCreateProject(){
        if (!$this->isAdmin) {
            return [
                'success' => false,
                'message' => 'Только администраторы могут создавать проекты',
            ];
        }

        $project = new Project;
        $project->name = post('name');
        $project->description = post('description');
        $project->parent_id = post('parent_id', null);

        $project->save();

        $project->addUser($this->user);

        return [
            'success' => true,
            'message' => 'Проект создан',
        ];

    }

    public function actionUpdateProject(){
        if (!$this->isAdmin) {
            return [
                'success' => false,
                'message' => 'Только администраторы могут изменять проекты',
            ];
        }

        $project = Project::where('id', post('project_id', 0))->first();

        if (!$project) {
           return [
                'success' => false,
                'message' => 'Проект не найден',
            ]; 
        }
        $project->name = post('name');
        $project->description = post('description');
        $project->parent_id = post('parent_id', null);

        $project->save();

        return [
            'success' => true,
            'message' => 'Проект изменен',
        ];

    }

    public function actionGetProjects(){

        if ($this->isAdmin) {
            return Project::all();
        }

        $projects = Project::getUserProjects($this->user);

        if (!$projects) return [];

        return $projects;

    }

    public function actionGetProject(){

        $project_id = post('project_id');

        // if ($this->isAdmin) {
        //     return Project::where('id', $project_id)->first();
        // }

        $project = Project::getUserProjects($this->user)->where('id', $project_id)->first();

        if (!$project) {
            return [
                'success' => false,
            ];
        }

        return $project;

    }

    public function actionProjectGetUsers(){
        $project = Project::whereId(post('project_id', null))->first();

        if (!$project) return [
            'success' => false,
            'message' => 'Проект не найден'
        ];

         if (!$project->hasAccess($this->user)) {
            return [
                'success' => false,
                'message' => 'Вы не являетесь членом этого проекта',
            ]; 
        }


        return $project->listMembers();


    }

    public function actionProjectAddMember(){

        if (!$this->isAdmin) {
            return [
                'success' => false,
                'message' => 'Только администраторы могут изменять проекты',
            ];
        }

        $project = Project::where('id', post('project_id', 0))->first();

        if (!$project) {
           return [
                'success' => false,
                'message' => 'Проект не найден',
            ]; 
        }

        if (!$project->hasAccess($this->user)) {
            return [
                'success' => false,
                'message' => 'Вы не являетесь членом этого проекта',
            ]; 
        }



        $user  = User::where('id', post('user_id'))->first();

        if ($project->hasAccess($user)) {
            return [
                'success' => false,
                'message' => 'Пользователь уже состоит в проекте',
            ]; 
        }

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Пользователь не найден',
            ]; 
        }

        $project->addUser($user, post('master_password', false), $this->user);

        return [
            'success' => true,
            'message' => 'Пользователь добавлен',
        ]; 
    }

    public function actionProjectRemoveMember(){
        if (!$this->isAdmin) {
            return [
                'success' => false,
                'message' => 'Только администраторы могут изменять проекты',
            ];
        }

        $project = Project::where('id', post('project_id', 0))->first();

        if (!$project) {
           return [
                'success' => false,
                'message' => 'Проект не найден',
            ]; 
        }

        if (!$project->hasAccess($this->user)) {
            return [
                'success' => false,
                'message' => 'Вы не являетесь членом этого проекта',
            ]; 
        }

        $user  = User::where('id', post('user_id'))->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Пользователь не найден',
            ]; 
        }

        if ($user->id == $this->user->id) {
             return [
                'success' => false,
                'message' => 'Вы не можете удалить себя из проекта',
            ]; 
        }

        $project->removeUser($user);

        return [
            'success' => true,
            'message' => 'Пользователь удален',
        ];         

    }

    public function actionListUsers(){
        return User::select(['id', 'name', 'surname', 'username'])->get();
    }

    public function actionAddRecord(){

        $key = Key::where('user_id', $this->user->id)->first();

        if (!$key) {
            return [
                'success' => false,
                'status' => 0,
                'message' => 'Хранилище ключей отсутствует. Создайте хранилище.'
            ];
        }

        $data = [
            'label' => post('label', null),
            'email' => post('email', null),
            'username' => post('username', null),
            'login' => post('login', null),
            'password' => post('password', null),
            'note' => post('note', null),
            'url' => post('url', null),
        ];

        $record = new Record;
        $record->user_id = $this->user->id;
        
        $project = false;
        if (post('project_id', null)) {
            $project = Project::where('id', post('project_id', null))->first();
            if (!$project->hasAccess($this->user)) {
                return [
                    'success' => false,
                    'message' => 'Вы не являетесь членом этого проекта',
                ]; 
            }
        }

        $record->project_id = post('project_id', null);
        
        if (post('url', null)) {
            $record->url_hash = md5(explode('/', post('url'))[0]);
        }

        openssl_public_encrypt(json_encode($data), $encrypted, $key->public_key);

        $record->data = base64_encode($encrypted);

        $record->save();


        if (post('project_id', null)) {

            foreach ($project->getMembersIds() as $user_id) {
                if ($user_id == $this->user->id) continue;
                $user_key = Key::where('user_id', $user_id)->first();
                openssl_public_encrypt(json_encode($data), $encrypted, $user_key->public_key);

                $new_record = new Record;
                $new_record->user_id = $user_id;
                $new_record->project_id = $project->id;
                $new_record->url_hash = $record->url_hash;
                $new_record->project_record_id = $record->project_record_id;

                $new_record->data = base64_encode($encrypted);

                $new_record->save();
            }

        }

        return [
            'success' => true,
            'message' => 'Запись сохранена'
        ];
        
    }

    public function actionDeleteRecord(){



        if ($this->isAdmin) {

            $record =  Record::where('user_id', $this->user->id)->where('id', post('record_id', null))->first();

            if (!$record) {
                return [
                    'success' => false,
                ];
            }
            Record::where('project_record_id', $record->project_record_id)->delete();
            $record->delete();

            return [
                'success' => true,
            ];

        }

        return [
            'success' => Record::where('user_id', $this->user->id)->where('id', post('record_id', null))->delete()
        ];
    }

    public function actionUpdateRecord(){
        $key = Key::where('user_id', $this->user->id)->first();

        if (!$key) {
            return [
                'success' => false,
                'status' => 0,
                'message' => 'Хранилище ключей отсутствует. Создайте хранилище.'
            ];
        }

        $record = Record::where('user_id', $this->user->id)->where('id', post('id', null))->first();

        if (!$record) {
            return [
                'success' => false,
                'message' => 'Запись отсутствует'
            ];
        }

        $data = [
            'label' => post('label', null),
            'email' => post('email', null),
            'username' => post('username', null),
            'login' => post('login', null),
            'password' => post('password', null),
            'note' => post('note', null),
            'url' => post('url', null),
        ];


        openssl_public_encrypt(json_encode($data), $encrypted, $key->public_key);

        $record->old_data = $record->data;
        $record->data = base64_encode($encrypted);

        if (post('url', null)) {
            $record->url_hash = md5(explode('/', post('url'))[0]);
        }

        $record->save();

        foreach (Record::where('project_record_id', $record->project_record_id) as $user_record) {
            $user_key = Key::where('user_id', $user_record->user_id)->first();
            if (!$user_key) continue;

            openssl_public_encrypt(json_encode($data), $encrypted, $user_key->public_key);

            $user_record->old_data = $user_record->data;
            $user_record->data = base64_encode($encrypted);
            $user_record->url_hash = $record->url_hash;

            $user_record->save();
        }

        return [
            'success' => true,
            'message' => 'Запись обновлена'
        ];

    }

    public function actionGetRecords(){
        $key = Key::where('user_id', $this->user->id)->first();

        if (!$key) {
            return [
                'success' => false,
                'status' => 0,
                'message' => 'Хранилище ключей отсутствует. Создайте хранилище.'
            ];
        }

        $records = Record::where('user_id', $this->user->id);

        if ($project_id = post('project_id', null)) {
            $records->where('project_id', $project_id);
        }

        if ($url_hash = post('url', null)) {
            $records->where('url_hash', md5($url_hash));
        }

        $records = $records->get();

        if (!post('master_password')) return $records;

        $result = [];

        foreach ($records as $record) {
            $privKey = CryptManager::aesDecrypt($key->private_key, post('master_password'), $key->aes_iv, $key->aes_tag);
            if (!$privKey){
                return [
                    'success' => false,
                    'message' => 'Неверный master пароль',
                ];
            }

            openssl_private_decrypt(base64_decode($record->data), $decrypted, $privKey);

            $record->data = json_decode($decrypted);

            $result[] = $record;
        }

        return $result;
    }

    public function actionDeleteProject(){
        if (!$this->isAdmin) {
            return [
                'success' => false,
                'message' => 'Только администраторы могут удалять проекты',
            ];
        }  

        $project_id = post('project_id');

        Project::where('id', $project_id)->delete();
        ProjectAccess::where('project_id', $project_id)->delete();
        Record::where('project_id', $project_id)->delete();

        return [
            'success' => true,
        ];

    }

    
}
