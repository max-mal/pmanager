<?php namespace Me\Manager\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateMeManagerRecord2 extends Migration
{
    public function up()
    {
        Schema::create('me_manager_record', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->integer('project_id')->nullable();
            $table->string('url_hash')->nullable();
            $table->text('data')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('me_manager_record');
    }
}
