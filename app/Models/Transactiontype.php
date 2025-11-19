<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transactiontype extends Model
{
    protected $table = 'transactiontypes';
    protected $primaryKey = 'id';
    protected $casts = ['id' => 'string'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'code', 'name', 'description', 'signe', 'active'];

}
