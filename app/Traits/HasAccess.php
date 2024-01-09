<?php

namespace App\Traits;

  use App\Models\System\Access;
  use App\Models\System\Ticket;

  trait HasAccess
  {
      public function accesses()
      {
          return $this->morphMany(Access::class,'model');
      }

      public function lastAccesses()
      {
          return $this->morphMany(Access::class,'model')->latest();
      }

      public function lastAccess()
      {
          return $this->morphMany(Access::class,'model')->latest()->limit(1);
      }


  }
