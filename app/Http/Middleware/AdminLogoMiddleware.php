<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class AdminLogoMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $adminLogo = Auth::user()->logo ? asset('assets/img/logo/' . Auth::user()->logo) : asset('assets/img/logo/default-logo.jpg');
            View::share('adminLogo', $adminLogo);
        }

        return $next($request);
    }
}