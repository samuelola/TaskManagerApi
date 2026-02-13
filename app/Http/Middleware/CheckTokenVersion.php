<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckTokenVersion
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user && $request->bearerToken()) {
            $payload = auth()->payload();
            $tokenVersion = $payload->get('token_version') ?? 0;

            if ($tokenVersion < $user->token_version) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session has expired. Please login again.'
                ], 401);
            }
        }

        return $next($request);
    }
}
