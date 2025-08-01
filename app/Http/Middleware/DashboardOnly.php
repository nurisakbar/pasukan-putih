<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class DashboardOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->id === '9f87571e-545c-4204-9d59-bc139d243c48') {
        
        if ($request->is('/') || $request->is('logout')) {
            return $next($request);
        }

        return redirect()->to('/');
    }

    return $next($request);
    }
}
