<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ByCode
{
    public function __construct(protected Request $request)
    {
    }

    public function handle(Builder $builder,\Closure $next){
        return $next($builder)
            ->when($this->request->has('code'),
                fn($query) => $query->where('code','REGEXP',$this->request->code)
            );
    }
}
