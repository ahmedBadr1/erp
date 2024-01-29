<?php

namespace App\Traits;

  use App\Models\Inventory\Discount;
  use App\Models\System\Contact;

  trait HasDiscount
  {
      public function discounts()
      {
          return $this->morphMany(Discount::class,'discountable');
      }

      public function lastDiscounts()
      {
          return $this->morphMany(Discount::class,'discountable')->latest();
      }

      public function discount()
      {
          return $this->morphOne(Discount::class,'discountable')->latestOfMany();
      }
  }
