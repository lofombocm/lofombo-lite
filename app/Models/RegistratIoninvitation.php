<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistratIoninvitation extends Model
{
    protected $table = 'registrationinvitations';
    protected $primaryKey = 'id';
    protected $casts = ['id' => 'string'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'name', 'email', 'invited_by', 'invited_at', 'expire_at',
        'invitation_url', 'active', 'is_admin', 'enterprise_name'];
}
