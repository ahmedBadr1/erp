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

      public function userAccesses()
      {
          return $this->morphMany(Access::class,'model')->where('auth_type','user');
      }

      public function lastAccesses()
      {
          return $this->morphMany(Access::class,'model')->latest();
      }

      public function lastAccess()
      {
          return $this->morphOne(Access::class,'model')->latestOfMany();
      }
      public function accessesTo()
      {
          return $this->morphMany(Access::class,'auth');
      }
      public function userAccessesTo()
      {
          return $this->morphMany(Access::class,'auth')->where('auth_type','user');
      }

      public function lastAccessesTo()
      {
          return $this->morphMany(Access::class,'auth')->latest();
      }

      public function lastAccessTo()
      {
          return $this->morphOne(Access::class,'auth')->latestOfMany();
      }

  }
