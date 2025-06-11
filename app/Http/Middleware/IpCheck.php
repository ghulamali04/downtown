<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IpCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user()->role != 'superadmin') {
            $ip = $request->ip();
            $allowedIps = optional(SystemSetting::ipconfig()->first())->payload ?? "";
            $allowedIps = explode(",", $allowedIps);
            if (in_array($ip, $allowedIps)) {
                return $next($request);
            }
            abort(404);
        }
        return $next($request);
    }
}
