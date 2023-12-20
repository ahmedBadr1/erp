<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ByFullName
{
    public function __construct(protected Request $request)
    {
    }

    public function handle(Builder $builder,\Closure $next){
        return $next($builder)
            ->when($this->request->has('name'),
                fn($query) => $query->where('first_name','REGEXP',$this->request->name)
                    ->orWhere('last_name','REGEXP',$this->request->name)
//                    ->orWhere('last_name','REGEXP',$this->request->name)
            );
    }
}
