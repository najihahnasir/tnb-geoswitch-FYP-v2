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
        Schema::create('substations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Substation name
            $table->string('code')->unique(); // Unique substation code
            $table->decimal('latitude', 10, 7); // Latitude for GIS mapping
            $table->decimal('longitude', 10, 7); // Longitude for GIS mapping
            $table->text('description')->nullable(); // Additional details
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('substations');
    }
};
