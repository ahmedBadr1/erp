<?php

namespace App\Models\System;

use App\Models\Hr\JobGrade;
use App\Models\MainModelSoft;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends MainModelSoft
{

    protected $table = 'tags';
    public $fillable = ['type','name'];

//    public function taggables()
//    {
//        return $this->morphToMany(Taggable::class, 'taggable');
//    }

    public function jobGrades()
    {
        return $this->morphedByMany(JobGrade::class, 'taggable');
    }

    public function insertTag ($name,$type) {
        $this->name = $name;
        $this->type = $type;
        return $this->save();
    }

    public static function foundTag($name,$type) {
        $tag =  static::where('name', $name)->where('type', $type)->first();
        if (isset($tag)) {
            return $tag->id;
        }else{
            return 0;
        }
    }

    public static function updateTag($id, $name) {
        $tag = static::findOrFail($id);
        $tag->name = $name;
        return $tag->save();
    }
}
