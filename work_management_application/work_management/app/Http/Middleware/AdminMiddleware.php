<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return redirect()->route('login');
        }

        // Debug: Log thông tin về user
        \Log::info('AdminMiddleware: User role = ' . $request->user()->role);

        if ($request->user()->role === 'admin') {
            return $next($request);
        }

        \Log::warning('AdminMiddleware: Access denied for user ' . $request->user()->id . ' with role ' . $request->user()->role);

        if ($request->wantsJson()) {
            return response()->json(['error' => 'Forbidden. Admin access required.'], 403);
        }

        return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập trang này. Yêu cầu quyền admin.');
    }
}
