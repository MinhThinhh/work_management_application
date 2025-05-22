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
        // Nếu người dùng đã đăng nhập, không cần xử lý JWT
        if (Auth::check()) {
            return $next($request);
        }

        // Không tự động đăng nhập người dùng từ token trong session hoặc cookie
        // Người dùng phải đăng nhập lại để đảm bảo an toàn

        // Chỉ kiểm tra tính hợp lệ của token trong cookie để xác thực API
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
            } catch (\Exception $e) {
                // Xóa cookie không hợp lệ
                \Cookie::queue(\Cookie::forget('jwt_token'));
                // Xóa token khỏi session nếu có
                if (Session::has('jwt_token')) {
                    Session::forget('jwt_token');
                }
            }
        }

        // Tiếp tục xử lý request
        return $next($request);
    }
}
