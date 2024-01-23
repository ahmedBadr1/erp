<?php

namespace App\Traits;


use App\Models\WorkAt;

  trait WorkAtTrait

  {
      public function work_at() {
          return $this->morphOne(WorkAt::class, 'workable')->latestOfMany();
      }

      public function workers() {
        return $this->morphMany(WorkAt::class,'workable');
      }
  }




?>
