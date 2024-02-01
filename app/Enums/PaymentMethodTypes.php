<?php
namespace App\Enums;

enum PaymentMethodTypes :string
{
    case CASH = "Cash" ;
    case VISA = "Visa" ;

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
