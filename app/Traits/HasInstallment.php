<?php

namespace App\Traits;

  use App\Models\Accounting\Installment;

  trait HasInstallment
  {
      public function installments()
      {
          return $this->morphMany(Installment::class,'installmentable');
      }

      public function lastInstallments()
      {
          return $this->morphMany(Installment::class,'installmentable')->latest();
      }

      public function installment()
      {
          return $this->morphOne(Installment::class,'installmentable')->latestOfMany();
      }
  }
