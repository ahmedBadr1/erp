<?php

namespace App\Models\System;

use App\Models\MainModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends MainModel
{

    public function states()
    {
        return $this->hasMany(State::class);
    }

    public function cities()
    {
        return $this->hasManyThrough(City::class,State::class);
    }

    public static function search($search)
    {
        return empty($search) ? static::query()
            : static::query()->where('id', 'like', '%'.$search.'%')
                ->orWhere('name', 'like', '%'.$search.'%')
                ->orWhere('iso2', 'like', '%'.$search.'%')
                ->orWhereHas('states', fn($q) => $q->where('name','like', '%'.$search.'%'));
    }
}
