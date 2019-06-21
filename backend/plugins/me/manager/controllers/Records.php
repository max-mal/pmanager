<?php namespace Me\Manager\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Records extends Controller
{
    public $implement = [        'Backend\Behaviors\ListController'    ];
    
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Me.Manager', 'manager-records');
    }
}
