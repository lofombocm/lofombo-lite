<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordRecoveryRequest extends Model
{
    protected $table = 'password_recovery_requests';
    protected $primaryKey = 'id';
    protected $casts = ['id' => 'string'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
          'id'
        , 'email'
        , 'telephone'
        , 'expire_at'
    ];
}
