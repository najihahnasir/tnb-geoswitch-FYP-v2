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
        Schema::create('switchgear_maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('switchgear_id')->nullable(); // optional link
            $table->string('functional_location');
            $table->date('report_date');
            $table->string('defect_description_1');
            $table->string('defect_description_2')->nullable();
            $table->string('defect_from')->nullable();
            $table->string('tev_us_in_db')->nullable();
            $table->string('hotspot_delta_t_in_c')->nullable();
            $table->string('switchgear_type')->nullable();
            $table->string('switchgear_brand')->nullable();
            $table->string('substation_name')->nullable(); // fallback if switchgear_id not linked
            $table->string('defect_owner')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('switchgear_maintenance_logs');
    }
};
