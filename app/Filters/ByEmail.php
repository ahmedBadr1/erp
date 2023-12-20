<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ByEmail
{
    public function __construct(protected Request $request)
    {
    }

    public function handle(Builder $builder,\Closure $next){
        return $next($builder)
            ->when($this->request->has('email'),
                fn($query) => $query->where('email','REGEXP',$this->request->email)
            );
    }
}
