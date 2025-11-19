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
        Schema::create('voucher_usage_codes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('code')->index()->unique();
            $table->uuid('voucherid')->nullable(false);
            $table->foreign('voucherid')->references('id')->on('vouchers')->onDelete('cascade');
            $table->bigInteger('allowed_by')->nullable(true);
            $table->foreign('allowed_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamp('used_at')->nullable();
            $table->timestamp('expired_at')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_usage_codes');
    }
};
