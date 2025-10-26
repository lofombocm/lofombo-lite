<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loyaltyewalet extends Model
{
    protected $table = 'loyaltyewalets';

    protected $primaryKey = 'id';
    protected $casts = ['id' => 'string'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'holderid','accountids','issuer', 'active'];

}
