<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loyaltytransaction extends Model
{
    protected $table = 'loyaltytransactions';
    protected $primaryKey = 'id';
    protected $casts = ['id' => 'string'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable =
        [
              'id'
            , 'date'
            , 'loyaltyaccountid'
            , 'configid'
            , 'madeby'
            , 'reference'
            , 'amount'
            , 'point'
            , 'old_amount'
            , 'old_point'
            , 'transactiontype'
            , 'transactiondetail'
            , 'clientid'
            , 'products'
        ];

}
