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
        Schema::create('password_recovery_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email');
            $table->foreign('email')->references('email')->on('users');;
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('expire_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_recovery_requests');
    }
};
