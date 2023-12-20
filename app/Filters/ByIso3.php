<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ByIso3
{
    public function __construct(protected Request $request)
    {
    }

    public function handle(Builder $builder,\Closure $next){
        return $next($builder)
            ->when($this->request->has('iso3'),
                fn($query) => $query->where('iso3','REGEXP',$this->request->iso3)
            );
    }
}
