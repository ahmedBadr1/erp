<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\AuthModel;
use App\Models\System\Bookmark;
use App\Models\System\Profile;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Permission\Models\Role;

class User extends AuthModel
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'otp','otp_expire_at','lang','image','phone','active'
    ];


    protected $appends = ['fullName'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'name' => 'json',
    ];


//    public function role()
//    {
//        return $this->hasMany(Role::class)->latest()->limit(1);
//    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    protected function name():Attribute
    {
        return Attribute::make(
            get: fn($value) => json_decode($value, true),
            set: fn($value) => json_encode($value),
        );
    }

    public function getFullNameAttribute()
    {
        return $this->name['first'] . ' ' . $this->name['last'] ;
    }






}
