<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Lấy token từ request
            $token = $request->bearerToken();
            if (!$token) {
                \Log::error('JWT Token is absent in request');
                return response()->json(['error' => 'Token is absent'], 401);
            }

            \Log::info('JWT Token received in middleware: ' . substr($token, 0, 10) . '...');

            // Thiết lập token cho JWTAuth
            JWTAuth::setToken($token);

            // Xác thực token
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                \Log::error('JWT User not found');
                return response()->json(['error' => 'User not found'], 401);
            }

            // Đăng nhập người dùng vào Auth
            Auth::login($user);

            \Log::info('JWT User authenticated in middleware: ' . $user->id);
        } catch (TokenExpiredException $e) {
            \Log::error('JWT Token has expired: ' . $e->getMessage());
            return response()->json(['error' => 'Token has expired'], 401);
        } catch (TokenInvalidException $e) {
            \Log::error('JWT Token is invalid: ' . $e->getMessage());
            return response()->json(['error' => 'Token is invalid'], 401);
        } catch (JWTException $e) {
            \Log::error('JWT Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Token error: ' . $e->getMessage()], 401);
        }

        return $next($request);
    }
}