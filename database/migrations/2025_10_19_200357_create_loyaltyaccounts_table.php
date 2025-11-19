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
        Schema::create('purchases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('clientid');
            $table->foreign('clientid')->references('id')->on('clients')->onDelete('cascade');
            $table->double('amount')->default(0);
            $table->string('receiptnumber')->nullable(false)->unique();
            $table->json('products')->nullable(false);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->double('price')->default(0);
            $table->string('others')->nullable();
            $table->timestamps();
        });

        Schema::create('loyaltyaccounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('loyaltyaccountnumber')->unique()->nullable(false)->index();
            $table->uuid('holderid')->nullable(false)->unique()->index();
            $table->foreign('holderid')->references('id')->on('clients')->onDelete('cascade');
            $table->double('amount_balance')->nullable(false)->default(0); // montant accumule des   differents achats
            $table->bigInteger('point_balance')->nullable(false)->default(0); // point accumule obtenu de la conversion montant point suivant la conversion defini
            $table->bigInteger('current_point')->nullable()->default(0); // point courant avant la prochaine modification
            $table->string('photo')->nullable();
            $table->bigInteger('issuer')->nullable(false);
            $table->foreign('issuer')->references('id')->on('users')->onDelete('cascade');
            $table->boolean('active')->default(true);
            $table->string('currency_name')->nullable(false)->default('FCFA');
            $table->timestamps();
        });

        Schema::create('loyaltyewalets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('holderid')->nullable(false);
            $table->foreign('holderid')->references('id')->on('clients')->onDelete('cascade');
            $table->string('accountids')->nullable();
            $table->bigInteger('issuer')->nullable(false);
            $table->foreign('issuer')->references('id')->on('users')->onDelete('cascade');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });


        /*Schema::create('transactiontypes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->nullable(false);
            $table->string('name')->nullable(false);
            $table->string('description')->nullable();
            $table->integer('signe')->nullable(false)->default(1);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });*/

        Schema::create('rewards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->nullable(false);
            $table->string('nature')->nullable(false);
            $table->double('value')->nullable(false);
            $table->json('level')->nullable(false); //level = object ['config' => 'uuid','name' => 'levelName', 'point' => 50]
            $table->boolean('active')->default(true);
            $table->bigInteger('registered_by')->nullable(false);
            $table->foreign('registered_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('conversion_amount_points', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->double('min_amount')->nullable(false);
            $table->double('birthdate_rate')->nullable(false)->default(1.5);
            $table->boolean('active')->default(true);
            $table->boolean('is_applicable')->default(true);
            $table->bigInteger('defined_by')->nullable(false);
            $table->foreign('defined_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        /*Schema::create('conversion_point_rewards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('min_point')->nullable(false);
            $table->uuid('reward')->nullable(false);
            $table->foreign('reward')->references('id')->on('rewards')->onDelete('cascade');
            $table->boolean('active')->default(true);
            $table->boolean('is_applicable')->default(true);
            $table->bigInteger('defined_by')->nullable(false);
            $table->foreign('defined_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });*/

        Schema::create('thresholds', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->double('classic_threshold')->nullable(false);
            $table->double('premium_threshold')->nullable(false);
            $table->double('gold_threshold')->nullable(false);
            $table->boolean('active')->default(true);
            $table->boolean('is_applicable')->default(true);
            $table->bigInteger('defined_by')->nullable(false);
            $table->foreign('defined_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });


        Schema::create('configs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->double('initial_loyalty_points')->nullable(false)->default(5);
            $table->double('amount_per_point')->nullable(false)->default(5000);
            $table->string('currency_name')->nullable(false)->default('FCFA');
            $table->json('levels')->nullable(false)->default('[]'); // array of object ['name' => 'levelName', 'point' => 50]
            /*$table->integer('classic_threshold')->nullable(false)->default(50);
            $table->integer('premium_threshold')->nullable(false)->default(80);
            $table->integer('gold_threshold')->nullable(false)->default(120);*/
            $table->integer('voucher_duration_in_month')->nullable(false)->default(3);
            $table->integer('password_recovery_request_duration')->nullable(false)->default(1440);
            $table->string('enterprise_name')->nullable(false)->default('ENTREPRISE TEST');
            $table->string('enterprise_email')->nullable(false)->default('contact@gmail.com');
            $table->string('enterprise_phone')->nullable(false)->default('0123456789');
            $table->string('enterprise_website')->nullable(false)->default(url('/'));
            $table->string('enterprise_address')->nullable(false)->default('');
            $table->string('enterprise_logo')->nullable(false)->default(asset('images/logo.jpg'));
            $table->integer('defined_by')->nullable(false);
            $table->foreign('defined_by')->references('id')->on('users')->onDelete('cascade');
            $table->boolean('is_applicable')->nullable(false)->default(true);
            $table->double('birthdate_bonus_rate')->nullable(false)->default(1);
            $table->timestamps();
        });



        Schema::create('loyaltytransactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamp('date')->nullable(false);
            $table->uuid('loyaltyaccountid')->nullable(false);
            $table->foreign('loyaltyaccountid')->references('id')->on('loyaltyaccounts')->onDelete('cascade');
            $table->uuid('configid')->nullable(false); // configuration qui a donne la conversion montant points
            $table->foreign('configid')->references('id')->on('configs')->onDelete('cascade');
            $table->string('madeby')->nullable(false); // userid or client id
            $table->string('reference')->nullable(false); // transaction for what? purchase ID or Voucher ID
            $table->double('amount')->nullable(false)->default(0);
            $table->bigInteger('point')->nullable(false)->default(0);
            $table->double('old_amount')->nullable(false)->default(0);
            $table->bigInteger('old_point')->nullable(false)->default(0);
            $table->string('transactiontype')->nullable(false);
            $table->string('transactiondetail')->nullable();
            $table->uuid('clientid')->nullable();
            $table->foreign('clientid')->references('id')->on('clients')->onDelete('cascade');
            $table->json('products')->nullable(false)->default('[]');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('products');
        Schema::dropIfExists('loyaltyaccounts');
        Schema::dropIfExists('loyaltyewalets');
        Schema::dropIfExists('loyaltytransactions');
        Schema::dropIfExists('configs');
        //Schema::dropIfExists('transactiontypes');
        Schema::dropIfExists('conversions');
        Schema::dropIfExists('conversion_amount_points');
        Schema::dropIfExists('rewards');
        //Schema::dropIfExists('conversion_point_rewards');
        Schema::dropIfExists('thresholds');


    }
};
