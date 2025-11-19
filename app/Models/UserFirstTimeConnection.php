<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFirstTimeConnection extends Model
{
    protected $table = 'user_first_time_connections';
    protected $primaryKey = 'id';
    protected $casts = ['id' => 'string'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'has_been_connected',];
}
