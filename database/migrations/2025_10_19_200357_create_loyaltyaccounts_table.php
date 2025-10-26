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
            $table->string('receiptnumber')->nullable();
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
            $table->uuid('holderid')->nullable(false);
            $table->foreign('holderid')->references('id')->on('clients')->onDelete('cascade');
            $table->double('amount_balance')->nullable(false)->default(0); // montant accumule des   differents achats
            $table->bigInteger('point_balance')->nullable(false)->default(0); // point accumule obtenu de la conversion montant point suivant la conversion defini
            $table->double('amount_from_converted_point')->nullable()->default(0); // montant cumule obtenu de la conversion des points. Qui sera le montant du bon d'achat
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


        Schema::create('transactiontypes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->nullable(false);
            $table->string('name')->nullable(false);
            $table->string('description')->nullable();
            $table->integer('signe')->nullable(false)->default(1);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('conversions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->double('amount_to_point_amount')->nullable(false);
            $table->double('amount_to_point_point')->nullable(false);
            $table->double('point_to_amount_point')->nullable(false);
            $table->double('point_to_amount_amount')->nullable(false);
            $table->double('birthdate_rate')->nullable(false)->default(1.5);
            $table->boolean('active')->default(true);
            $table->boolean('is_applicable')->default(true);
            $table->bigInteger('defined_by')->nullable(false);
            $table->foreign('defined_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });


        Schema::create('loyaltytransactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamp('date')->nullable(false);
            $table->uuid('loyaltyaccountid')->nullable(false);
            $table->foreign('loyaltyaccountid')->references('id')->on('loyaltyaccounts')->onDelete('cascade');
            $table->uuid('conversionid')->nullable(false);
            $table->foreign('conversionid')->references('id')->on('conversions')->onDelete('cascade');
            $table->bigInteger('sellerid')->nullable(false);
            $table->foreign('sellerid')->references('id')->on('users')->onDelete('cascade');
            $table->uuid('purchaseid')->nullable(false);
            //$table->foreign('purchaseid')->references('id')->on('purchases')->onDelete('cascade');
            $table->double('amount')->nullable(false)->default(0);
            $table->bigInteger('point')->nullable(false)->default(0);
            $table->double('amount_from_converted_point')->nullable(false)->default(0);
            $table->bigInteger('current_point')->nullable(false)->default(0);
            $table->uuid('transactiontypeid')->nullable(false);
            $table->foreign('transactiontypeid')->references('id')->on('transactiontypes')->onDelete('cascade');
            $table->string('transactiondetail')->nullable();
            $table->string('clienttransactionid')->nullable();
            $table->string('state')->nullable();
            $table->string('returnresult')->nullable();
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
        Schema::dropIfExists('transactiontypes');
        Schema::dropIfExists('conversions');
    }
};
