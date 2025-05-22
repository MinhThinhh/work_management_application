<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!$request->user()) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return redirect()->route('login');
        }

        // Debug: Log thông tin về user và roles
        \Log::info('CheckRole middleware: User role = ' . $request->user()->role);
        \Log::info('CheckRole middleware: Required role = ' . $role);

        // Hỗ trợ nhiều role được phân tách bằng dấu phẩy
        $allowedRoles = explode(',', $role);

        \Log::info('CheckRole middleware: Allowed roles = ' . implode(',', $allowedRoles));

        if (in_array($request->user()->role, $allowedRoles)) {
            return $next($request);
        }

        \Log::warning('CheckRole middleware: Access denied for user ' . $request->user()->id . ' with role ' . $request->user()->role);

        if ($request->wantsJson()) {
            return response()->json(['error' => 'Forbidden. You do not have the required role.'], 403);
        }

        return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập trang này.');
    }
}
