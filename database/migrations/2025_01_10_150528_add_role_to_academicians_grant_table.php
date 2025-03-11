<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('academicians_grant', function (Blueprint $table) {
            $table->string('role')->default('member'); // Add a 'role' column with a default value of 'member'
        });
    }

    public function down()
    {
        Schema::table('academicians_grant', function (Blueprint $table) {
            $table->dropColumn('role'); // Drop the 'role' column
        });
    }

};
