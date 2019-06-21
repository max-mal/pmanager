<?php namespace Me\Manager\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Projects extends Controller
{
    public $implement = [        'Backend\Behaviors\ListController'    ];
    
    public $listConfig = 'config_list.yaml';

    public $requiredPermissions = [
        'manager::admin' 
    ];

    public function __construct()
    {
        parent::__construct();
    }
}
