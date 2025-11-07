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
            $table->boolean('is_used')->nullable(false)->default(false);
            $table->timestamp('used_at')->nullable();
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
