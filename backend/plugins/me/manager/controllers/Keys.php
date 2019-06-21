<?php namespace Me\Manager\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Keys extends Controller
{
    public $implement = [        'Backend\Behaviors\ListController',        'Backend\Behaviors\FormController'    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'manager::admin' 
    ];

    public function __construct()
    {
        parent::__construct();
    }
}
