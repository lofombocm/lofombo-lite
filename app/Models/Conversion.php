<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversion extends Model
{
    protected $table = 'conversions';
    protected $primaryKey = 'id';

    protected $casts = ['id' => 'string'];
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'amount_to_point_amount', 'amount_to_point_point', 'point_to_amount_point', 'point_to_amount_amount', 'birthdate_rate', 'active', 'is_applicable', 'defined_by'];
}
