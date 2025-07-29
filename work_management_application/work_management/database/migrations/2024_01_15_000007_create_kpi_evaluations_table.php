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
        Schema::create('kpi_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->year('year');
            $table->tinyInteger('quarter')->nullable(); // 1,2,3,4 for quarterly, NULL for annual
            $table->decimal('overall_score', 5, 2);
            $table->text('manager_comments')->nullable();
            $table->text('self_assessment')->nullable();
            $table->text('improvement_areas')->nullable();
            $table->text('achievements')->nullable();
            $table->json('metric_scores')->nullable(); // Store individual metric scores
            $table->foreignId('evaluated_by')->constrained('users')->onDelete('cascade');
            $table->date('evaluation_date');
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->timestamps();
            
            $table->index(['user_id', 'year']);
            $table->index(['team_id', 'year']);
            $table->index('evaluation_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_evaluations');
    }
};
