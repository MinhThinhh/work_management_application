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

class HandleJwtToken
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
        \Log::info('HandleJwtToken middleware được gọi cho URL: ' . $request->url());

        // LUÔN LUÔN kiểm tra JWT token để xóa khi hết hạn
        // Không skip ngay cả khi user đã đăng nhập

        // Kiểm tra tính hợp lệ của token trong cookie và xóa nếu hết hạn
        $token = $request->cookie('jwt_token');
        if ($token) {
            try {
                JWTAuth::setToken($token);
                $user = JWTAuth::authenticate();

                // Không tự động đăng nhập, chỉ xác thực token
                if ($user) {
                    // Lưu thông tin user vào request để sử dụng nếu cần
                    $request->attributes->add(['jwt_user' => $user]);
                }
            } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
                // Token đã hết hạn, XÓA COOKIE, SESSION và ĐĂNG XUẤT
                \Cookie::queue(\Cookie::forget('jwt_token'));
                if (Session::has('jwt_token')) {
                    Session::forget('jwt_token');
                }
                // ĐĂNG XUẤT người dùng khỏi Laravel Auth
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                \Log::warning('JWT Token expired in HandleJwtToken middleware, logged out user');
            } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                // Token không hợp lệ, XÓA COOKIE, SESSION và ĐĂNG XUẤT
                \Cookie::queue(\Cookie::forget('jwt_token'));
                if (Session::has('jwt_token')) {
                    Session::forget('jwt_token');
                }
                // ĐĂNG XUẤT người dùng khỏi Laravel Auth
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                \Log::warning('JWT Token invalid in HandleJwtToken middleware, logged out user');
            } catch (\Exception $e) {
                // Lỗi khác, XÓA COOKIE và SESSION
                \Cookie::queue(\Cookie::forget('jwt_token'));
                if (Session::has('jwt_token')) {
                    Session::forget('jwt_token');
                }
                \Log::error('JWT Token error in HandleJwtToken middleware: ' . $e->getMessage());
            }
        } else {
            // Không có JWT token trong cookie
            \Log::info('HandleJwtToken: Không có JWT token trong cookie');

            // Nếu user đã đăng nhập qua Laravel session nhưng không có JWT token
            // thì đăng xuất để đảm bảo tính nhất quán
            if (Auth::check()) {
                \Log::info('HandleJwtToken: User đã đăng nhập qua session nhưng không có JWT token - Đăng xuất');
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Chuyển hướng về trang login
                return redirect('/login')->with('message', 'Phiên đăng nhập đã hết hạn');
            }
        }

        // Tiếp tục xử lý request
        return $next($request);
    }
}
