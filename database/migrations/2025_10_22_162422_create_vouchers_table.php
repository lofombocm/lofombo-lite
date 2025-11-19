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
        Schema::dropIfExists('vouchers');
        Schema::create('vouchers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('serialnumber')->unique()->nullable(false)->index();
            $table->uuid('clientid')->nullable(false);
            $table->foreign('clientid')->references('id')->on('clients')->onDelete('cascade');
            $table->string('level')->nullable(false);
            $table->integer('point')->nullable(false)->default(0);
            $table->double('amount')->nullable(false)->default(0);
            $table->string('enterprise')->nullable(false);
            $table->dateTime('expirationdate')->nullable(false);
            $table->boolean('active')->nullable(false)->default(false);
            $table->bigInteger('activated_by');
            $table->foreign('activated_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('deactivated_at')->nullable();
            $table->boolean('is_used')->nullable(false)->default(false);
            $table->string('code_used')->nullable()->index();
            $table->timestamp('used_at')->nullable();
            $table->uuid('reward_id')->index()->nullable();
            $table->foreign('reward_id')->references('id')->on('rewards')->onDelete('cascade');
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
