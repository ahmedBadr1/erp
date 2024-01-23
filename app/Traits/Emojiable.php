<?php

namespace App\Traits;

use App\Models\System\Emoji;

trait Emojiable
{
    public function emojis()
    {
        return $this->morphMany(Emoji::class,'emojiable');
    }
    public function lastEmoji()
    {
        return $this->morphOne(Emoji::class,'emojiable')->latestOfMany();
    }
}
