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
        Schema::table('substations', function (Blueprint $table) {
            $table->string('substation_location')->nullable();
            $table->string('status')->default('active');
            $table->string('type')->nullable(); // PPU or SSU
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('substations', function (Blueprint $table) {
            $table->dropColumn(['substation_location', 'status', 'type']);
        });
    }
};
