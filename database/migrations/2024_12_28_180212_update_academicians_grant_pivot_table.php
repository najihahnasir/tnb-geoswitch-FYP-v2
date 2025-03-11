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
        Schema::table('academicians_grant', function (Blueprint $table) {
            // Drop existing foreign keys using correct constraint names
            $table->dropForeign('academicians_grant_leader_id_foreign');
            $table->dropForeign('academicians_grant_grant_id_foreign');

            // Rename columns
            $table->renameColumn('leader_id', 'academician_id');
            $table->renameColumn('grant_id', 'research_grant_id');

            // Recreate foreign keys with updated column names
            $table->foreign('academician_id')->references('id')->on('academicians')->onDelete('cascade');
            $table->foreign('research_grant_id')->references('id')->on('research_grants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academicians_grant', function (Blueprint $table) {
            // Drop updated foreign keys
            $table->dropForeign(['academician_id']);
            $table->dropForeign(['research_grant_id']);

            // Rename columns back to original
            $table->renameColumn('academician_id', 'leader_id');
            $table->renameColumn('research_grant_id', 'grant_id');

            // Recreate original foreign keys
            $table->foreign('leader_id')->references('id')->on('academicians')->onDelete('cascade');
            $table->foreign('grant_id')->references('id')->on('research_grants')->onDelete('cascade');
        });
    }
};
