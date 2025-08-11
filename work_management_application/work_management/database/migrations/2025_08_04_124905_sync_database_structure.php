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
        // Add missing indexes to optimize performance
        // Use try-catch to handle existing indexes gracefully

        try {
            Schema::table('tasks', function (Blueprint $table) {
                $table->index('creator_id', 'tasks_creator_id_idx');
            });
        } catch (\Exception $e) {
            // Index already exists
        }

        try {
            Schema::table('tasks', function (Blueprint $table) {
                $table->index('assigned_to', 'tasks_assigned_to_idx');
            });
        } catch (\Exception $e) {
            // Index already exists
        }

        try {
            Schema::table('tasks', function (Blueprint $table) {
                $table->index('status', 'tasks_status_idx');
            });
        } catch (\Exception $e) {
            // Index already exists
        }

        try {
            Schema::table('tasks', function (Blueprint $table) {
                $table->index('priority', 'tasks_priority_idx');
            });
        } catch (\Exception $e) {
            // Index already exists
        }

        try {
            Schema::table('tasks', function (Blueprint $table) {
                $table->index('due_date', 'tasks_due_date_idx');
            });
        } catch (\Exception $e) {
            // Index already exists
        }

        try {
            Schema::table('users', function (Blueprint $table) {
                $table->index('role', 'users_role_idx');
            });
        } catch (\Exception $e) {
            // Index already exists
        }

        try {
            Schema::table('teams', function (Blueprint $table) {
                $table->index('leader_id', 'teams_leader_id_idx');
            });
        } catch (\Exception $e) {
            // Index already exists
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropIndex('tasks_creator_id_idx');
                $table->dropIndex('tasks_assigned_to_idx');
                $table->dropIndex('tasks_status_idx');
                $table->dropIndex('tasks_priority_idx');
                $table->dropIndex('tasks_due_date_idx');
            });
        } catch (\Exception $e) {
            // Indexes don't exist
        }

        try {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex('users_role_idx');
            });
        } catch (\Exception $e) {
            // Index doesn't exist
        }

        try {
            Schema::table('teams', function (Blueprint $table) {
                $table->dropIndex('teams_leader_id_idx');
            });
        } catch (\Exception $e) {
            // Index doesn't exist
        }
    }
};
