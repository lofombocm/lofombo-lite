<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherUsageCode extends Model
{
    protected $table = 'voucher_usage_codes';
    protected $primaryKey = 'id';
    protected $casts = ['id' => 'string'];
    public $incrementing = false;

    protected $keyType = 'string';
    protected $fillable = ['id', 'code', 'voucherid', 'allowed_by', 'used_at', 'expired_at',];
}
