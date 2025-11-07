<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('registrationinvitations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->integer('invited_by');
            $table->foreign('invited_by')->references('id')->on('users')->onDelete('cascade');
            $table->string('enterprise_name')->nullable(false);
            $table->timestamp('invited_at')->nullable(false)->default(Carbon::now());
            $table->timestamp('expire_at')->nullable(false)->default(Carbon::now());
            $table->string('invitation_url');
            $table->boolean('active')->nullable(false)->default(true);
            $table->boolean('is_admin')->nullable(false)->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
