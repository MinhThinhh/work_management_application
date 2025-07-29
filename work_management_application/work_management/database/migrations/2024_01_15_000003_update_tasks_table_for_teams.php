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
        Schema::table('tasks', function (Blueprint $table) {
            // Rename assigned_user_id to user_id for consistency
            $table->renameColumn('assigned_user_id', 'user_id');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('team_id')->nullable()->after('user_id')->constrained('teams')->onDelete('set null');
            $table->foreignId('assigned_by')->nullable()->after('team_id')->constrained('users')->onDelete('set null');

            $table->index('team_id');
            $table->index('assigned_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropForeign(['assigned_by']);
            $table->dropIndex(['team_id']);
            $table->dropIndex(['assigned_by']);
            $table->dropColumn(['team_id', 'assigned_by']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            // Rename back to assigned_user_id
            $table->renameColumn('user_id', 'assigned_user_id');
        });
    }
};
