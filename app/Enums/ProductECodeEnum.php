<?php
namespace App\Enums;


enum ProductECodeEnum :string
{
    case EGS = 'EGS';
    case GS1 = 'GS1';


    public static function toArray(): array
    {
        $array = [];
        foreach (self::cases() as $case) {
            $array[$case->value] = $case->name;
        }
        return $array;
    }

}
