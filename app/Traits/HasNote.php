<?php

namespace App\Traits;

  use App\Models\System\Contact;
  use App\Models\System\Note;

  trait HasNote
  {
      public function notes()
      {
          return $this->morphMany(Note::class,'notable');
      }

      public function lastNotes()
      {
          return $this->morphMany(Note::class,'notable')->latest();
      }

      public function lastNote()
      {
          return $this->morphOne(Note::class,'notable')->latestOfMany();
      }
  }
