<?php

namespace App\Traits;

  use App\Models\System\Ticket;

  trait HasTicket
  {
      public function ticktes()
      {
          return $this->morphMany(Ticket::class,'has_ticket');
      }

      public function lastTickets()
      {
          return $this->morphMany(Ticket::class,'has_ticket')->latest();
      }

      public function lastTicket()
      {
          return $this->morphOne(Ticket::class,'has_ticket')->latestOfMany();
      }

  }
