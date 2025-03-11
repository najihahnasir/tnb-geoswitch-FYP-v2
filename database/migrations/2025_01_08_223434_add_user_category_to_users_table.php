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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('userLevel')->default(0); // 0 = Leader, 1 = iRMC Staff, 2 = Admin
            $table->enum('userCategory', ['Admin', 'Member', 'iRMC Staff'])->default('Member');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('userLevel');
            $table->dropColumn('userCategory');
        });
    }
};
