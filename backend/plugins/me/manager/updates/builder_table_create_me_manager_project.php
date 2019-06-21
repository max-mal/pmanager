<?php namespace Me\Manager\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateMeManagerProject extends Migration
{
    public function up()
    {
        Schema::create('me_manager_project', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->integer('parent_id')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('me_manager_project');
    }
}
