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
        // Cập nhật tasks cũ: nếu assigned_user_id = NULL thì gán = creator_id
        \DB::statement('UPDATE tasks SET assigned_user_id = creator_id WHERE assigned_user_id IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback: set assigned_user_id = NULL for tasks where assigned_user_id = creator_id
        \DB::statement('UPDATE tasks SET assigned_user_id = NULL WHERE assigned_user_id = creator_id');
    }
};
