<?php

namespace App\Models\System;

use App\Models\MainModelSoft;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class Invitation extends MainModelSoft
{

    protected $fillable = ['email', 'invitation_token','expire_at', 'registered_at', 'sent_by','role_id'];
    protected $casts = [
        'expire_at' => 'datetime',
        'registered_at' => 'datetime'
    ] ;

    public function generateInvitationToken()
    {
        $this->invitation_token = substr(md5(rand(0, 9) . $this->email . time()), 0, 32);
    }

    public function getLink() {

        return urldecode( env('FRONT_APP_URL').'reg/' .$this->invitation_token);
   ///   return urldecode(route('reg', $this->invitation_token));

    }
    public function sender()
    {
        return $this->belongsTo(User::class,'sent_by');
    }

    public function role()
    {
        return $this->belongsTo(Role::class,'sent_by');
    }
    public function getRegisteredAttribute()
    {
        if (!$this->registered_at) return ;
        return $this->registered_at->diffForHumans();
    }
}
