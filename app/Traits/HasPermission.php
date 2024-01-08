<?php

namespace App\Traits;

  use App\Models\System\ModelPermission;
  use App\Models\System\Ticket;

  trait HasPermission
  {
      public function permissions()
      {
          return $this->morphMany(ModelPermission::class,'model');
      }

      public function lastPermissions()
      {
          return $this->morphMany(ModelPermission::class,'model')->latest();
      }

      public function lastPermission()
      {
          return $this->morphMany(ModelPermission::class,'model')->latest()->limit(1);
      }

  }
