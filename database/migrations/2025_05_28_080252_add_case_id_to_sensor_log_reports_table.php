<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('sensor_log_reports', function (Blueprint $table) {
            $table->string('case_id')->unique()->nullable()->after('id');
        });
    }

    public function down()
    {
        Schema::table('sensor_log_reports', function (Blueprint $table) {
            $table->dropColumn('case_id');
        });
    }
};