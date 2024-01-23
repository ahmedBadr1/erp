<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Permission\Guard;
use  \Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
//    protected $fillable = ['name','slug','guard_name'];

    public static function findBySlug(string $slug, $guardName = 'web')
    {
        $role = static::findByParam(['slug' => $slug, 'guard_name' => $guardName]);

        if (! $role) {
            throw new \RuntimeException("There is no role with Slug `{$slug}`.");
        }

        return $role;
    }

    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub

        static::creating(function($model){
            $model->slug = Str::slug($model->name);
        });
    }

//    protected function slug(): Attribute
//    {
//        return Attribute::make(
////            get: fn($value) => json_decode($value, true),
//            set: fn($value) => Str::slug($this->name),
//        );
//    }

}