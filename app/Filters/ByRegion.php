<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ByRegion
{
    public function __construct(protected Request $request)
    {
    }

    public function handle(Builder $builder,\Closure $next){
        return $next($builder)
            ->when($this->request->has('region'),
                fn($query) => $query->where('region','REGEXP',$this->request->region)
            );
    }
}
