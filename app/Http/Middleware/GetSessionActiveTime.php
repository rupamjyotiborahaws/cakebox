<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\RedirectResponse;
use App\Models\SessionTime;

class GetSessionActiveTime
{    
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $session_time = SessionTime::getSessionTime();
        $request->session_time = $session_time->session_time;
        $response = $next($request);
        return $response;
    }
}
