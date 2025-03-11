<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update `milestones` table
        Schema::table('milestones', function (Blueprint $table) {
            $table->dropForeign(['grant_id']);
            $table->foreign('grant_id')->references('id')->on('research_grants')->onDelete('cascade');
        });

        // Update `research_grants` table
        Schema::table('research_grants', function (Blueprint $table) {
            $table->dropForeign(['leader_id']);
            $table->foreign('leader_id')->references('id')->on('academicians')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert `milestones` table
        Schema::table('milestones', function (Blueprint $table) {
            $table->dropForeign(['grant_id']);
            $table->foreign('grant_id')->references('id')->on('research_grants');
        });

        // Revert `research_grants` table
        Schema::table('research_grants', function (Blueprint $table) {
            $table->dropForeign(['leader_id']);
            $table->foreign('leader_id')->references('id')->on('academicians');
        });
    }
};
