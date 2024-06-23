<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait EnumOptions
{
    public static function options(): array
    {
        $cases   = static::cases();
        $options = [];
        foreach($cases as $case){
            $label = $case->name;
            if(Str::contains($label, '_')){
                $label = Str::replace('_', ' ', $label);
            }
            $options[] = [
                'value' => $case->value,
                'label' => Str::title($label)
            ];
        }
        return $options;
    }

    public static function get($value): string
    {
        return match ($value) {
            self::primary->value => self::primary->name,
            self::smaller->value => self::smaller->name,
            self::bigger->value => self::bigger->name,
        };
    }
}
