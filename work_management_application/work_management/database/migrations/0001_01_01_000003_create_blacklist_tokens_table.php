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
        Schema::create('blacklist_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token_id')->comment('JWT token ID (jti)');
            $table->timestamp('expires_at')->useCurrent()->useCurrentOnUpdate()->comment('Thời gian hết hạn của token');
            $table->timestamps();
            
            $table->index('token_id');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blacklist_tokens');
    }
};
