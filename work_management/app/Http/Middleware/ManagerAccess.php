<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ManagerAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || ($request->user()->role !== 'manager' && $request->user()->role !== 'admin')) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Unauthorized. Manager or admin access required.'], 403);
            }
            return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập trang này. Yêu cầu quyền manager hoặc admin.');
        }

        return $next($request);
    }
}
