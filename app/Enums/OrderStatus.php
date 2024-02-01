<?php
namespace App\Enums;

enum OrderStatus :string
{
    case PAID = "paid" ;
    case UNPAID = "unpaid" ;
    case PARTIALS = "partials" ;

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

    public static function next($status): OrderStatus
    {
        return match ($status) {
            'unpaid' => self::PARTIALS,
            'partials' => self::PAID,
            'paid' =>null,
        };
    }

}
