<?php namespace Me\Manager\Models;

use Model;

/**
 * Model
 */
class ProjectAccess extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'me_manager_project_access';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
}
