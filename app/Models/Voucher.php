<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $table = 'vouchers';
    protected $primaryKey = 'id';
    protected $casts = ['id' => 'string'];
    public $incrementing = false;

    protected $keyType = 'string';
    protected $fillable = ['id', 'serialnumber', 'clientid', 'level', 'point', 'amount', 'enterprise', 'expirationdate', 'active', 'activated_by', 'reward', 'is_used', 'used_at'];
}
