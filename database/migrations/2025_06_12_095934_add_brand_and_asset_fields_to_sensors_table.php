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
        Schema::table('sensors', function (Blueprint $table) {
            $table->string('brand_name')->nullable()->after('type');
            $table->string('sku')->nullable()->after('brand_name');
            $table->string('warranty')->nullable()->after('sku'); // e.g., "2 years", "until 2026"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('sensors', function (Blueprint $table) {
            $table->dropColumn(['brand_name', 'sku', 'warranty']);
        });
    }
};
