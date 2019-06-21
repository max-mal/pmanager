<?php namespace Me\Manager\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateMeManagerRecord extends Migration
{
    public function up()
    {
        Schema::create('me_manager_record', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id');
            $table->string('label')->nullable();
            $table->string('username')->nullable();
            $table->string('email')->nullable();
            $table->text('password')->nullable();
            $table->text('note')->nullable();
            $table->string('url')->nullable();
            $table->integer('project_id')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('me_manager_record');
    }
}
