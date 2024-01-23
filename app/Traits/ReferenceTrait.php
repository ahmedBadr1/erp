<?php

namespace App\Traits;

  use App\Models\Accounting\Transaction;

  trait ReferenceTrait
  {
      public function transactions()
      {
          return $this->morphMany(Transaction::class,'reference');
      }

      public function lastTransactions()
      {
          return $this->morphMany(Transaction::class,'reference')->latest();
      }

      public function lastTransaction()
      {
          return $this->morphOne(Transaction::class,'reference')->latestOfMany();
      }
  }
