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
        Schema::create('milestones', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('milestone_name');
            $table->date('completion_date');
            $table->string('deliverable');
            $table->enum('status', ['Not Started', 'In Progress', 'Completed']);
            $table->text('remark')->nullable();
            $table->timestamp('date_updated')->useCurrent();
            $table->foreignId('grant_id')->constrained('research_grants');  // Foreign key to ResearchGrant
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('milestones');
    }
};
