<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversionPointReward extends Model
{
    protected $table = 'conversion_point_rewards';
    protected $primaryKey = 'id';
    protected $casts = ['id' => 'string'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'min_point', 'reward', 'active', 'is_applicable', 'defined_by'];
}
