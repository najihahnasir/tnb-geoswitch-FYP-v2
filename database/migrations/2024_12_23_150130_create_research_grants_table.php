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
        Schema::create('research_grants', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('project_title')->nullable();
            $table->decimal('grant_amount', 15, 2);
            $table->string('grant_provider');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('duration_month');
            $table->foreignId('leader_id')->constrained('academicians');  // Foreign key to Academician
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_grants');
    }
};
