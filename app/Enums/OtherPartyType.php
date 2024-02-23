<?php
namespace App\Enums;

use App\Traits\EnumOptions;
use App\Traits\EnumValues;

enum OtherPartyType :string
{
    use EnumValues , EnumOptions ;
    case IN = "in" ;
    case OUT = "out" ;

    /**
     * Retrieve a map of enum keys and values.
     *
     * @return array
     */
    public static function toArray(): array
    {
        $array = [];
        foreach (self::cases() as $case) {
            $array[$case->name] = $case->value;
        }
        return $array;
    }

}
