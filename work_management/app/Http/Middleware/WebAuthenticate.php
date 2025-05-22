<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class WebAuthenticate
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
        // Kiểm tra xem người dùng đã đăng nhập thông qua Laravel Auth chưa
        if (Auth::check()) {
            return $next($request);
        }

        // Kiểm tra xem có JWT token trong session không
        if (Session::has('jwt_token')) {
            try {
                $token = Session::get('jwt_token');
                JWTAuth::setToken($token);
                $user = JWTAuth::authenticate();

                if ($user) {
                    // Đăng nhập người dùng vào Laravel Auth
                    Auth::login($user);
                    return $next($request);
                }
            } catch (TokenExpiredException $e) {
                // Token đã hết hạn, xóa khỏi session
                Session::forget('jwt_token');
            } catch (TokenInvalidException $e) {
                // Token không hợp lệ, xóa khỏi session
                Session::forget('jwt_token');
            } catch (JWTException $e) {
                // Lỗi khác, xóa token khỏi session
                Session::forget('jwt_token');
            }
        }

        // Kiểm tra xem có JWT token trong cookie không
        $token = $request->cookie('jwt_token');
        if ($token) {
            try {
                JWTAuth::setToken($token);
                $user = JWTAuth::authenticate();

                if ($user) {
                    // Đăng nhập người dùng vào Laravel Auth
                    Auth::login($user);
                    // Lưu token vào session
                    Session::put('jwt_token', $token);

                    // Ghi log thành công
                    \Log::info('User authenticated via cookie token: ' . $user->id);

                    return $next($request);
                }
            } catch (TokenExpiredException $e) {
                // Token đã hết hạn, xóa cookie
                \Log::warning('Token expired in cookie: ' . substr($token, 0, 10) . '...');
                \Cookie::queue(\Cookie::forget('jwt_token'));
            } catch (TokenInvalidException $e) {
                // Token không hợp lệ, xóa cookie
                \Log::warning('Invalid token in cookie: ' . substr($token, 0, 10) . '...');
                \Cookie::queue(\Cookie::forget('jwt_token'));
            } catch (\Exception $e) {
                // Lỗi khác, xóa cookie
                \Log::error('Error with token in cookie: ' . $e->getMessage());
                \Cookie::queue(\Cookie::forget('jwt_token'));
            }
        }

        // Nếu không có xác thực nào thành công, chuyển hướng đến trang đăng nhập
        return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
    }
}
