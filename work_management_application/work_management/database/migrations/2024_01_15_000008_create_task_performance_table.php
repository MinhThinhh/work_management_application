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
        Schema::create('task_performance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('completion_time_hours', 8, 2)->nullable();
            $table->decimal('quality_score', 3, 2)->nullable(); // 0.00 to 5.00
            $table->boolean('on_time')->default(false);
            $table->integer('days_overdue')->default(0);
            $table->decimal('manager_rating', 3, 2)->nullable(); // 0.00 to 5.00
            $table->decimal('self_rating', 3, 2)->nullable(); // 0.00 to 5.00
            $table->text('feedback')->nullable();
            $table->text('manager_feedback')->nullable();
            $table->timestamps();
            
            $table->index('task_id');
            $table->index('user_id');
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_performance');
    }
};
