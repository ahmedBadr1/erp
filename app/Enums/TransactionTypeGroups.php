<?php
namespace App\Enums;

enum TransactionTypeGroups :string
{
    case ACC = "acc" ;
    case INV = "inv" ;
    case SALES = "sales" ;
    case PUR = "pur" ;

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
