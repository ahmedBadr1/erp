<?php
namespace App\Enums\Inventory;

use App\Traits\EnumOptions;
use App\Traits\EnumValues;

enum UnitTypesEnum :int
{
    use EnumValues , EnumOptions ;

    case primary = 1 ;

    case smaller = 2 ;

    case bigger = 3;

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
