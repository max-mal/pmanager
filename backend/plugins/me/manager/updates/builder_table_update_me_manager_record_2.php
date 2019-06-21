<?php namespace Me\Manager\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMeManagerRecord2 extends Migration
{
    public function up()
    {
        Schema::table('me_manager_record', function($table)
        {
            $table->string('project_record_id')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('me_manager_record', function($table)
        {
            $table->dropColumn('project_record_id');
        });
    }
}
