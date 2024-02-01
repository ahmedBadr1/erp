<?php
namespace App\Enums;

use App\Traits\EnumOptions;
use App\Traits\EnumValues;

enum SupplierStatus :string
{
    use EnumValues , EnumOptions ;
    case ACTIVE = "Active" ;
    case PENDING = "Pending" ;
    case WAITING = "Waiting" ;

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
