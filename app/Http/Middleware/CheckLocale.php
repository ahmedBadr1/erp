<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->expectsJson()){
            $local = ($request->hasHeader('X_Lang')) ? $request->header('X_Lang') : "ar";
            app()->setLocale($local);
            return $next($request);
        }
        if(session()->has('locale')) {
            app()->setLocale(session('locale'));
            app()->setLocale(config('app.locale'));
        }
        return $next($request);
    }
}
