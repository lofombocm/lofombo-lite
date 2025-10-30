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
    protected $fillable = ['id', 'date', 'loyaltyaccountid', 'conversionid', 'sellerid', 'amount', 'purchaseid', 'point',
        'old_point', 'transactiontypeid', 'transactiondetail', 'clienttransactionid'];

}
