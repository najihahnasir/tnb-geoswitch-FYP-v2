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
            $table->renameColumn('name', 'substation_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('substations', function (Blueprint $table) {
            $table->renameColumn('substation_name', 'name');
        });
    }
};
