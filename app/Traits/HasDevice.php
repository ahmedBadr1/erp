<?php

namespace App\Traits;

  use App\Models\System\Device;

  trait HasDevice
  {
      public function devices()
      {
          return $this->morphMany(Device::class,'owner');
      }

      public function lastDevices()
      {
          return $this->morphMany(Device::class,'owner')->latest();
      }

      public function lastDevice()
      {
          return $this->morphOne(Device::class,'owner')->latestOfMany();
      }
  }
