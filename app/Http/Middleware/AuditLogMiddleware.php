<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuditLogMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->isMethod('GET')) {
            return $response;
        }

        if (!Auth::check()) {
            return $response;
        }

        $user = Auth::user();
        $action = $request->route()->action ?? 'unknown';
        $method = $request->method();
        $url = $request->url();

        Log::info("Audit Log: {$method} {$url} by {$user->name}");

        return $response;
    }
}
