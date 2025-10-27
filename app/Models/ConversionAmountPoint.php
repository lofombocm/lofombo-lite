<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversionAmountPoint extends Model
{
    protected $table = 'conversion_amount_points';
    protected $primaryKey = 'id';
    protected $casts = ['id' => 'string'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'min_amount', 'birthdate_rate', 'active', 'is_applicable', 'defined_by'];
}
