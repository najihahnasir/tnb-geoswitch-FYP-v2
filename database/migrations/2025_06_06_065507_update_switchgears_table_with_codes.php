<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSwitchgearsTableWithCodes extends Migration
{
    public function up()
    {
        Schema::table('switchgears', function (Blueprint $table) {
            //Rename 'name' to 'panel_name'
            $table->renameColumn('name', 'panel_name');

            //Add panel_code (e.g. F1, BC34)
            $table->string('panel_code')->nullable()->after('panel_name');

            // Add compartment_code (e.g. MB, CBT)
            $table->string('compartment_code')->nullable()->after('compartment');

            //Add unit_code (e.g. F1_CBT)
            $table->string('switchgear_code')->nullable()->after('compartment_code');
        });
    }

    public function down()
    {
        Schema::table('switchgears', function (Blueprint $table) {
            // Rollback: rename column back
            $table->renameColumn('panel_name', 'name');

            // Drop added columns
            $table->dropColumn(['panel_code', 'compartment_code', 'switchgear_code']);
        });
    }
}
