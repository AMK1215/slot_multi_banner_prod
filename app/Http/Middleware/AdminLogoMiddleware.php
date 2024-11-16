<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;


class AdminLogoMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
             $logoFilename = Auth::user()->logo;
    Log::info('Auth User Logo:', ['logo' => $logoFilename]);
            $adminLogo = Auth::user()->logo ? asset('assets/img/logo/' . Auth::user()->logo) : asset('assets/img/logo/default-logo.jpg');
    Log::info('Admin Logo Path:', ['path' => $adminLogo]);
            View::share('adminLogo', $adminLogo);
        }

        return $next($request);
    }
}