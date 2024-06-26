<?php

namespace App\Traits;

  use App\Models\System\Contact;

  trait HasContacts
  {
      public function contacts()
      {
          return $this->morphMany(Contact::class,'contactable');
      }

      public function lastContacts()
      {
          return $this->morphMany(Contact::class,'contactable')->latest();
      }

      public function lastContact()
      {
          return $this->morphOne(Contact::class,'contactable')->latestOfMany();
      }
  }
