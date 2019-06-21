<?php namespace Me\Manager\Models;

use Model;

/**
 * Model
 */
class Key extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'me_manager_key';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
}
