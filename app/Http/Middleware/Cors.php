<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request)
//            ->header('Referrer-Policy','strict-origin-when-cross-origin')
//            ->header('Access-Control-Allow-Origin','*')
//            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
//            ->header('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, X-Token-Auth, Authorization');
        ->header('Access-Control-Allow-Origin',config('cors.allowed_origins'))
        ->header('Access-Control-Allow-Methods',config('cors.allowed_methods'))
        ->header('Access-Control-Allow-Headers', config('cors.allowed_headers'));
    }
}
