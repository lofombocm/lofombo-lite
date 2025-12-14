<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Client extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

    protected $guarded = [];

    protected $table = 'clients';
    protected $primaryKey = 'id';
    protected $casts = ['id' => 'string'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
          'id'
        , 'name'
        , 'email'
        , 'telephone'
        , 'birthdate'
        , 'gender'
        , 'quarter'
        , 'city'
        , 'password'
        , 'registered_by'
        , 'active'
        , 'was_invited'
        , 'invited_by'
        , 'invitation_id'
    ];

    protected $hidden = [
        'password',
    ];


}
