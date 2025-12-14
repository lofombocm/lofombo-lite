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
        Schema::create('friend_invitations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->nullable();
            $table->string('email')->nullable(false);
            $table->string('telephone')->nullable();
            $table->uuid('inviter_id')->nullable(false);
            $table->foreign('inviter_id')->references('id')->on('clients');
            $table->enum('state',['PENDING', 'ACCEPTED', 'REFUSED', 'CONFIRM'])->nullable(false);
            $table->boolean('active')->default(true)->nullable();
            $table->string('invitation_link', 5000)->nullable(false);
            $table->json('sent_data')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->foreign('invited_by')->references('id')->on('clients');
            $table->foreign('invitation_id')->references('id')->on('friend_invitations')->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('friend_invitations');
    }
};
