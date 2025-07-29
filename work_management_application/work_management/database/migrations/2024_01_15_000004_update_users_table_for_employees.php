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
            $table->string('employee_id', 50)->unique()->nullable()->after('email');
            $table->date('hire_date')->nullable()->after('employee_id');
            $table->string('department', 100)->nullable()->after('hire_date');
            $table->string('position', 100)->nullable()->after('department');
            $table->string('phone', 20)->nullable()->after('position');
            $table->text('address')->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'employee_id', 
                'hire_date', 
                'department', 
                'position', 
                'phone', 
                'address'
            ]);
        });
    }
};
