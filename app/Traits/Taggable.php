<?php

namespace App\Traits;

  use App\Models\System\Tag;

  trait Taggable
  {
      public function tags() {
          return $this->morphToMany(Tag::class, 'taggable');
      }
  }
