<?php

namespace App\Traits;

  use App\Models\Inventory\Dimension;
  use App\Models\Inventory\Discount;

  trait HasDimension
  {
      public function dimensions()
      {
          return $this->morphMany(Dimension::class,'measurable');
      }

      public function lastDimensions()
      {
          return $this->morphMany(Dimension::class,'measurable')->latest();
      }

      public function dimension()
      {
          return $this->morphOne(Dimension::class,'measurable')->latestOfMany();
      }
  }
