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
        Schema::create('kpi_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('metric_type', [
                'task_completion', 
                'quality_score', 
                'deadline_adherence', 
                'collaboration', 
                'custom'
            ]);
            $table->decimal('weight', 5, 2)->default(1.00);
            $table->string('unit', 50)->default('percentage'); // percentage, score, count, etc.
            $table->decimal('min_value', 10, 2)->default(0.00);
            $table->decimal('max_value', 10, 2)->default(100.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_metrics');
    }
};
