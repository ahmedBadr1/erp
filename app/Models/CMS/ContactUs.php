<?php

namespace App\Models\CMS;

use App\Models\MainModelSoft;
use App\Models\System\Status;

class ContactUs extends MainModelSoft
{
    protected $fillable = ['first_name', 'last_name', 'email', 'phone', 'title', 'content', 'from', 'status_id'];

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

}
