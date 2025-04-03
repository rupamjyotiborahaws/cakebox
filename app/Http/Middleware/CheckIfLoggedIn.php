<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cookie;
use Auth;

class CheckIfLoggedIn
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::check()) {
            date_default_timezone_set('Asia/Kolkata');
            $last_login = base64_decode($request->cookies->get('last_login'));
            $now = time();
            if($now - strtotime($last_login) > $request->session_time * 60) {
                Auth::logout();
                Cookie::queue('auth_token', false, -1);
                Cookie::queue('last_login', false, -1);
                return redirect()->route('user-login')->with('error', 'You are logged out!');
            }
            return $next($request);
        }
        return redirect()->route('user-login');
    }
}
