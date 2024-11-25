<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use App\Models\Task;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckArchived
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Task::where('archived_at', '<', Carbon::now()->subWeek())->delete();
        return $next($request);
    }
}
