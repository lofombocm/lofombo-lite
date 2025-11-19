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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('channel');
            $table->string('subject');
            $table->text('message');
            $table->json('client_address')->nullable(false);
            $table->bigInteger('sender')->unsigned()->nullable(false);
            $table->foreign('sender')->references('id')->on('users')->onDelete('cascade');
            $table->timestamp('send_at')->nullable();
            //$table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
