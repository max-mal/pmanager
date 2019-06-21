<?php namespace Me\Manager\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMeManagerRecord3 extends Migration
{
    public function up()
    {
        Schema::table('me_manager_record', function($table)
        {
            $table->text('old_data')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('me_manager_record', function($table)
        {
            $table->dropColumn('old_data');
        });
    }
}
