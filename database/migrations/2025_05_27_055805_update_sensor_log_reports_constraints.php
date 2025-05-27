<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSensorLogReportsConstraints extends Migration
{
    public function up()
    {
        Schema::table('sensor_log_reports', function (Blueprint $table) {
            $table->enum('new_status', ['online', 'offline', 'repairing'])->nullable(false)->change();
            $table->timestamp('status_changed_at')->nullable(false)->change();
        });
    }

    public function down()
    {
        Schema::table('sensor_log_reports', function (Blueprint $table) {
            $table->string('new_status')->nullable()->change(); // fallback to old type if needed
            $table->timestamp('status_changed_at')->nullable()->change();
        });
    }
}
