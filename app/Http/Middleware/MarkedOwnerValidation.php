<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class MarkedOwnerValidation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->task->user_id !== Auth::user()->id) {
            return response()->json([
                'success' => false,
                'message' => "Unauthorized request"
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
