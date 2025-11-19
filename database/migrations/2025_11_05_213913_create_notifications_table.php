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
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('generator')->index();
            $table->string('subject')->nullable(false);
            $table->timestamp('sent_at')->nullable(false);
            $table->text('body')->nullable(false);
            $table->json('data')->nullable(false);
            $table->string('sender')->nullable(false);
            $table->string('recipient')->nullable(false);
            $table->string('sender_address')->nullable(false);
            $table->string('recipient_address')->nullable(false);
            $table->boolean('read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
