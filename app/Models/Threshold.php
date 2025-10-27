<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Threshold extends Model
{
    protected $table = 'thresholds';
    protected $primaryKey = 'id';
    protected $casts = ['id' => 'string'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'classic_threshold', 'premium_threshold', 'gold_threshold', 'active', 'is_applicable', 'defined_by'];
}
