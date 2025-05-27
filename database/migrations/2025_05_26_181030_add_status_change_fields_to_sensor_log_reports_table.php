<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sensor_log_reports', function (Blueprint $table) {
            $table->enum('old_status', ['online', 'offline', 'repairing'])->nullable()->after('remark');
            $table->enum('new_status', ['online', 'offline', 'repairing'])->nullable()->after('old_status');
            $table->foreignId('status_changed_by')->nullable()->constrained('users')->after('new_status');
            $table->timestamp('status_changed_at')->nullable()->after('status_changed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sensor_log_reports', function (Blueprint $table) {
            $table->dropForeign(['status_changed_by']);
            $table->dropColumn(['old_status', 'new_status', 'status_changed_by', 'status_changed_at']);
        });
    }
};
