<?php namespace Me\Manager\Models;

use Model;
use Me\Manager\Classes\CryptManager;
use RainLab\User\Models\User;
/**
 * Model
 */
class Project extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'me_manager_project';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];


    public function addUser($user, $password=false, $currentUser=false){
        
        $access = new ProjectAccess;
        $access->project_id = $this->id;
        $access->user_id = $user->id;

        $access->save();

        if ($password && $currentUser){
            $user_key = Key::where('user_id', $user->id)->first();
            $currentUser_key = Key::where('user_id', $currentUser->id)->first();

            $privKey = CryptManager::aesDecrypt($key->private_key, $password, $key->aes_iv, $key->aes_tag);

            foreach (Record::where('project_id', $this->id) as $record) {
                openssl_private_decrypt(base64_decode($record->data), $decrypted, $privKey);

                $record->data = json_decode($decrypted);

                $new_record = new Record;
                $new_record->user_id = $user->id;
                $new_record->project_id = $this->id;
                $new_record->url_hash = $record->url_hash;
                $new_record->project_record_id = $record->project_record_id;

                openssl_public_encrypt(json_encode($record->data), $encrypted, $user_key->public_key);

                $new_record->data = base64_encode($encrypted);

                $new_record->save();
            }
        }


    }

    public function removeUser($user){
        
        ProjectAccess::where('project_id', $this->id)->where('user_id', $user->id)->delete();
        Record::where('project_id', $this->id)->where('user_id', $user->id)->delete();

        return true;
    }

    public static function getUserProjects($user){
        $project_ids = ProjectAccess::where('user_id', $user->id)->lists('project_id');

        return Project::whereIn('id', $project_ids)->get();
    }

    public function hasAccess($user) {
        if (ProjectAccess::where('project_id', $this->id)->where('user_id', $user->id)->first()) {
            return true;
        } else {
            return false;
        }
    }

    public function getMembersIds(){
        return ProjectAccess::where('project_id', $this->id)->lists('user_id');
    }

    public function listMembers(){
        return User::whereIn('id', $this->getMembersIds())->select(['id', 'name', 'surname', 'username'])->get();
    }
}
