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
        Schema::create('academicians_grant', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('leader_id')->constrained('academicians')->onDelete('cascade'); // Foreign key to Academician
            $table->foreignId('grant_id')->constrained('research_grants')->onDelete('cascade'); // Foreign key to ResearchGrant
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academicians_grant');
    }
};
