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
        Schema::create('switchgears', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('substation_id');
            $table->string('name');
            $table->string('lineup')->nullable();
            $table->string('panel')->nullable();
            $table->string('compartment')->nullable();
            $table->foreign('substation_id')->references('id')->on('substations')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('switchgears');
    }
};
