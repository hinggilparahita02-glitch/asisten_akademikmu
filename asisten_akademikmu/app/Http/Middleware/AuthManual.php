<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthManual
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Cek apakah user mencoba mengakses halaman login
        // Jika iya, biarkan mereka lewat tanpa pengecekan session
        if ($request->routeIs('login')) {
            return $next($request);
        }

        // 2. Jika bukan halaman login dan session user_id tidak ada
        if (!$request->session()->has('user_id')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        
        }
        return $next($request);
    }

}