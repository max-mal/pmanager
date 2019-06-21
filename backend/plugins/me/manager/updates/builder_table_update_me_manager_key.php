<?php namespace Me\Manager\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMeManagerKey extends Migration
{
    public function up()
    {
        Schema::table('me_manager_key', function($table)
        {
            $table->string('aes_iv')->nullable();
            $table->string('aes_tag')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('me_manager_key', function($table)
        {
            $table->dropColumn('aes_iv');
            $table->dropColumn('aes_tag');
        });
    }
}
