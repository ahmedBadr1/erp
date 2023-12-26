<?php

namespace App\Models\System;

use App\Models\MainModelSoft;
use App\Models\User;
use Spatie\Permission\Models\Role;

class Invitation extends MainModelSoft
{

    protected $fillable = ['email', 'token','expire_at', 'registered_at', 'sent_by','role_id'];
    protected $casts = [
        'expire_at' => 'datetime',
        'registered_at' => 'datetime'
    ] ;
    protected $hidden = 'token';

    public function generateInvitationToken()
    {
        return substr(md5(rand(0, 9) . $this->email . time()), 0, 64);
    }

    public function getLink() {

        return urldecode( env('FRONT_APP_URL').'reg/' .$this->token);
   ///   return urldecode(route('reg', $this->invitation_token));

    }
    public function sender()
    {
        return $this->belongsTo(User::class,'sent_by');
    }

    public function role()
    {
        return $this->belongsTo(Role::class,'role_id');
    }
    public function getRegisteredAttribute()
    {
        return $this->registered_at ?:  $this->registered_at->diffForHumans() ;
    }
}
