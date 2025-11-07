<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $casts = ['id' => 'string'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
          'id'
        , 'generator'
        , 'subject'
        , 'sent_at'
        , 'body'
        , 'data'
        , 'sender'
        , 'recipient'
        , 'sender_address'
        , 'recipient_address'
        , 'read'
    ];
}
