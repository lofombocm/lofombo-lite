<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $table = 'campaigns';
    protected $primaryKey = 'id';

    protected $casts = ['id' => 'string'];
    public $incrementing = false;

    protected $keyType = 'string';
    protected $fillable = ['id', 'channel', 'subject', 'message', 'client_address', 'sender', 'send_at'];
}
