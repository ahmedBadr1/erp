<?php

namespace App\Models\System;

use App\Models\MainModelSoft;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends MainModelSoft
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'bio',
        'address',
        'photo',
        'url',
        'location',
        'gender',
        'lang',
        'phone',
    ];

    public function user()
    {
        return  $this->belongsTo(User::class);
    }

    protected function urls():Attribute
    {
        return Attribute::make(
            get: fn($value) => json_decode($value, true),
            set: fn($value) => json_encode($value),
        );
    }
}
