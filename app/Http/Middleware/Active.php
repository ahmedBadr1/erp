<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Active
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if( !auth()->user()->active){

            if ($request->expectsJson()) {
                \auth('api')->user()
                    ->tokens()
                    ->delete();
                return response()->json(['error' => __('message.suspended')], 401);
            }

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('admin.login')->with('error', __('message.suspended'));
        }
        return $next($request);
    }
}
