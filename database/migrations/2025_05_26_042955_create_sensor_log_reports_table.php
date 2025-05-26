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
        Schema::create('sensor_log_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('substation_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('switchgear_id')->constrained()->onDelete('cascade');
            $table->foreignId('sensor_id')->constrained()->onDelete('cascade');
            $table->foreignId('reported_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['open', 'resolved'])->default('open');
            $table->timestamp('reported_at');
            $table->timestamp('resolved_at')->nullable();
            $table->text('remark')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_log_reports');
    }
};
