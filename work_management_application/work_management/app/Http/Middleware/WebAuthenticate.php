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
        // Đăng xuất Laravel Auth để chỉ dựa vào JWT
        Auth::logout();

        // Không xóa session để giữ CSRF token và các thông tin quan trọng khác
        // Session::flush(); // Bỏ comment này để tránh xóa CSRF token

        // THAY ĐỔI: Kiểm tra session TRƯỚC, sau đó mới kiểm tra cookie
        $sessionToken = Session::get('jwt_token');
        $cookieToken = $request->cookie('jwt_token');

        \Log::info('WebAuthenticate - URL: ' . $request->url());
        \Log::info('WebAuthenticate - Session token exists: ' . ($sessionToken ? 'YES' : 'NO'));
        \Log::info('WebAuthenticate - Cookie token exists: ' . ($cookieToken ? 'YES' : 'NO'));

        // Ưu tiên session token trước, sau đó mới cookie token
        $token = $sessionToken ?: $cookieToken;

        // Nếu không có token nào, redirect về login
        if (!$token) {
            \Log::info('No JWT token found in session or cookie, redirecting to login');
            Session::forget('jwt_token');
            return redirect()->route('login')->with('info', 'Vui lòng đăng nhập để tiếp tục.');
        }
        if ($token) {
            try {
                JWTAuth::setToken($token);

                // Kiểm tra token có trong blacklist không
                $payload = JWTAuth::getPayload($token);
                $jti = $payload['jti'];
                $blacklisted = \DB::table('blacklist_tokens')->where('token_id', $jti)->exists();

                if ($blacklisted) {
                    \Log::warning('Token in cookie is blacklisted: ' . substr($token, 0, 10) . '...');
                    \Cookie::queue(\Cookie::forget('jwt_token'));
                    throw new TokenInvalidException('Token is blacklisted');
                }

                $user = JWTAuth::authenticate();

                if ($user) {
                    // Đăng nhập tạm thời cho request này
                    Auth::login($user);

                    // Kiểm tra nếu token sắp hết hạn (còn < 30 phút), refresh token
                    $payload = JWTAuth::getPayload($token);
                    $exp = $payload['exp'];
                    $now = time();
                    $timeLeft = $exp - $now;

                    if ($timeLeft < 1800) { // < 30 phút
                        try {
                            $newToken = JWTAuth::refresh($token);
                            Session::put('jwt_token', $newToken);
                            \Cookie::queue('jwt_token', $newToken, 60 * 24); // 24 hours
                            \Log::info('Token refreshed for user: ' . $user->id);
                        } catch (\Exception $e) {
                            \Log::warning('Failed to refresh token: ' . $e->getMessage());
                        }
                    } else {
                        // Đồng bộ token vào session
                        Session::put('jwt_token', $token);
                    }

                    // Ghi log thành công
                    \Log::info('User authenticated via cookie token: ' . $user->id);

                    return $next($request);
                }
            } catch (TokenExpiredException $e) {
                // Token đã hết hạn, xóa cả cookie và session
                \Log::warning('Token expired in cookie: ' . substr($token, 0, 10) . '...');
                Session::forget('jwt_token');
                \Cookie::queue(\Cookie::forget('jwt_token'));
            } catch (TokenInvalidException $e) {
                // Token không hợp lệ hoặc bị blacklist, xóa cả cookie và session
                \Log::warning('Invalid token in cookie: ' . substr($token, 0, 10) . '...');
                Session::forget('jwt_token');
                \Cookie::queue(\Cookie::forget('jwt_token'));
            } catch (\Exception $e) {
                // Lỗi khác, xóa cả cookie và session
                \Log::error('Error with token in cookie: ' . $e->getMessage());
                Session::forget('jwt_token');
                \Cookie::queue(\Cookie::forget('jwt_token'));
            }
        }

        // Nếu không có xác thực nào thành công, chuyển hướng đến trang đăng nhập
        return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
    }
}
