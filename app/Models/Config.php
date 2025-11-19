<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $table = 'configs';
    protected $primaryKey = 'id';

    protected $casts = ['id' => 'string'];
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
          'id'
        , 'initial_loyalty_points'
        , 'amount_per_point'
        , 'currency_name'
        , 'levels'
        , 'voucher_duration_in_month'
        , 'password_recovery_request_duration'
        , 'enterprise_name'
        , 'enterprise_email'
        , 'enterprise_phone'
        , 'enterprise_website'
        , 'enterprise_address'
        , 'enterprise_logo'
        ,  'defined_by'
        , 'is_applicable'
        , 'birthdate_bonus_rate'
    ];
}
