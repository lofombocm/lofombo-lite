<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FriendInvitatin extends Model
{
    const PENDING = "PENDING";
    const ACCEPTED = "ACCEPTED";
    const REFUSED = "REFUSED";
    const CONFIRM = "CONFIRM";

    protected $table = 'friend_invitations';
    protected $primaryKey = 'id';
    protected $casts = ['id' => 'string'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'name', 'email', 'telephone', 'inviter_id', 'state', 'active','invitation_link','sent_data'];
}
