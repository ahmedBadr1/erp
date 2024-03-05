<?php

namespace App\Traits;

trait EnumValues
{
     public static function values(): array
    {
        $cases   = static::cases();
        $options = [];
        foreach($cases as $case){
            $options[] = $case->value;
        }
        return $options;
    }

}
