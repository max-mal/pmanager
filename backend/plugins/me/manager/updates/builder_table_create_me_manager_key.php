<?php namespace Me\Manager\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateMeManagerKey extends Migration
{
    public function up()
    {
        Schema::create('me_manager_key', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('id');
            $table->integer('user_id')->nullable();
            $table->text('public_key')->nullable();
            $table->text('private_key')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->primary(['id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('me_manager_key');
    }
}
