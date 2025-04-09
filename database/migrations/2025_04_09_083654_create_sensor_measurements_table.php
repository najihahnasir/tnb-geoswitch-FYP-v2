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
        Schema::create('sensor_measurements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sensor_id');
            $table->float('value');
            $table->string('unit')->nullable();
            $table->float('threshold');
            $table->string('status'); // e.g., ALARM, AWAIT
            $table->string('severity'); // e.g., WARN, PRESEN
            $table->foreign('sensor_id')->references('id')->on('sensors')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_measurements');
    }
};
