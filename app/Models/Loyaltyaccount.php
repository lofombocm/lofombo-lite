<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loyaltyaccount extends Model
{
    protected $table = 'loyaltyaccounts';
    protected $primaryKey = 'id';
    protected $casts = ['id' => 'string'];
    public $incrementing = false;

    protected $keyType = 'string';
    protected $fillable = ['id', 'loyaltyaccountnumber', 'holderid', 'amount_balance', 'purchase_amount_balance',
        'gift_amount_balance', 'birthdate_amount_balance', 'point_balance', 'amount_from_converted_point',
        'current_point', 'photo', 'issuer', 'active', 'currency_name'];

}
