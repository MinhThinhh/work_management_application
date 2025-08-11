<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthController extends Controller
{
    public function checkSession(Request $request)
    {
        try {
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            return response()->json([
                'status' => 'success',
                'user' => $user
            ]);
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token has expired'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token is invalid'], 401);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token is absent'], 401);
        }
    }

    public function showLoginForm()
    {
        return view('login');
    }

    public function showRegisterForm()
    {
        return view('register');
    }

    // Removed duplicate login method to resolve redeclaration error.

    public function register(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[a-zA-ZÀ-ỹ\s]+$/u' // Chỉ cho phép chữ cái và khoảng trắng (bao gồm tiếng Việt)
                ],
                'email' => 'required|email|unique:users,email',
                'password' => [
                    'required',
                    'min:8',
                    'confirmed',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/' // Ít nhất 1 chữ thường, 1 chữ hoa, 1 số
                ],
            ], [
                'name.regex' => 'Họ và tên chỉ được chứa chữ cái và khoảng trắng, không được chứa số.',
                'email.unique' => 'Email này đã được sử dụng. Vui lòng chọn email khác.',
                'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
                'password.regex' => 'Mật khẩu phải chứa ít nhất 1 chữ thường, 1 chữ hoa và 1 số.',
                'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            ]);

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'role' => 'user',
            ]);

            if ($request->wantsJson()) {
                return response()->json(['message' => __('auth.register_success')], 201);
            }

            return redirect()->route('login')->with('success', __('auth.register_success') . ' Vui lòng đăng nhập.');
        } catch (\Exception $e) {
            Log::error('Lỗi đăng ký: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json(['error' => __('messages.error') . ': ' . $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', __('messages.error') . ': ' . $e->getMessage())->withInput();
        }
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            // Ghi log thông tin đăng nhập
            Log::info('Đang xử lý đăng nhập cho email: ' . $credentials['email']);

            // Đăng xuất người dùng hiện tại nếu có
            if (Auth::check()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Xóa cookie JWT nếu có
                if ($request->cookie('jwt_token')) {
                    try {
                        $token = $request->cookie('jwt_token');
                        JWTAuth::setToken($token);
                        $this->addTokenToBlacklist($token);
                        JWTAuth::invalidate($token);
                    } catch (\Exception $e) {
                        Log::error('Lỗi khi vô hiệu hóa token cũ: ' . $e->getMessage());
                    }
                }
            }

            // Chỉ xác thực để lấy user, KHÔNG đăng nhập vào Laravel session
            if (Auth::attempt($credentials, false)) { // Luôn đặt remember = false
                // KHÔNG regenerate session để tránh tự động đăng nhập
                // $request->session()->regenerate();

                // Tạo JWT token cho người dùng
                $user = Auth::user();

                // ĐĂNG XUẤT ngay lập tức để không lưu session
                Auth::logout();

                Log::info('Đăng nhập thành công cho user ID: ' . $user->id);

                try {
                    $token = JWTAuth::fromUser($user);
                    Log::info('JWT token đã được tạo: ' . substr($token, 0, 10) . '...');

                    // Đặt thời gian sống cố định cho token (ngắn hạn) - không sử dụng remember token
                    try {
                        $payload = JWTAuth::setToken($token)->getPayload();
                        $expiration = $payload['exp'];
                        Log::info('JWT token hợp lệ, expires at: ' . date('Y-m-d H:i:s', $expiration));
                        $minutes = 15;

                        Log::info('Đặt thời gian sống cố định cho token: ' . $minutes . ' phút');
                    } catch (\Exception $e) {
                        Log::error('JWT token không hợp lệ: ' . $e->getMessage());
                        $minutes = 15;
                    }

                    // Nếu yêu cầu API
                    if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
                        return response()->json(['token' => $token, 'user' => $user]);
                    }

                    // Không tự động đăng nhập vào Laravel Auth - sử dụng JWT hoàn toàn
                    // Auth::login($user);

                    // Tạo cookie chứa token và chuyển hướng dựa trên role
                    $redirectTo = '/dashboard';

                    // Chuyển hướng dựa trên role
                    if ($user->role === 'admin') {
                        $redirectTo = route('admin.users');
                        Log::info('Chuyển hướng admin đến: ' . $redirectTo);
                    } elseif ($user->role === 'manager') {
                        $redirectTo = route('manager.all-tasks');
                        Log::info('Chuyển hướng manager đến: ' . $redirectTo);
                    } else {
                        $redirectTo = route('dashboard');
                        Log::info('Chuyển hướng user đến: ' . $redirectTo);
                    }

                    Log::info('User role: ' . $user->role . ', redirectTo: ' . $redirectTo);

                    // Lưu token vào session thay vì cookie để tránh vấn đề timing
                    Session::put('jwt_token', $token);

                    // Tạo cookie với các thuộc tính bảo mật
                    $cookie = cookie(
                        'jwt_token',      // Tên cookie
                        $token,           // Giá trị cookie
                        $minutes,         // Thời gian sống (phút)
                        '/',              // Path
                        null,             // Domain (null = domain hiện tại)
                        false,            // Secure (false cho HTTP local)
                        true,             // HttpOnly (không thể truy cập bằng JavaScript)
                        false,            // Raw
                        'Lax'             // SameSite (Lax cho phép gửi cookie khi chuyển hướng từ site khác)
                    );

                    return redirect()->intended($redirectTo)
                        ->with('login_success', __('auth.login_success') . ' Chào mừng bạn trở lại, ' . $user->name . '!')
                        ->cookie($cookie);
                } catch (\Exception $jwtException) {
                    Log::error('Lỗi tạo JWT token: ' . $jwtException->getMessage());
                    throw $jwtException;
                }
            }

            // Xác thực không thành công
            Log::error('Đăng nhập thất bại: Thông tin không hợp lệ cho email ' . $credentials['email']);

            if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
                return response()->json(['error' => __('auth.invalid_credentials')], 401);
            }

            return back()->withErrors([
                'email' => __('auth.invalid_credentials'),
            ])->withInput();
        } catch (\Exception $e) {
            Log::error('Lỗi đăng nhập: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
                return response()->json(['error' => 'Lỗi đăng nhập: ' . $e->getMessage()], 500);
            }

            return back()->with('error', 'Lỗi đăng nhập: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Đăng nhập cho ứng dụng desktop
     */
    public function desktopLogin(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            Log::info('Desktop API login: Đang xử lý cho email: ' . $credentials['email']);

            // Xác thực với JWT trực tiếp
            if (!$token = auth('api')->attempt($credentials)) {
                Log::error('Desktop API login: Xác thực thất bại cho email: ' . $credentials['email']);
                return response()->json([
                    'success' => false,
                    'error' => 'Thông tin đăng nhập không chính xác'
                ]);
            }

            $user = auth('api')->user();
            Log::info('Desktop API login: Xác thực thành công cho user ID: ' . $user->id);
            Log::info('Desktop API login: Token đã được tạo: ' . substr($token, 0, 10) . '...');

            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            Log::error('Desktop API login: Lỗi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Thêm token vào blacklist
     */
    protected function addTokenToBlacklist($token)
    {
        try {
            // Lấy payload từ token
            $payload = JWTAuth::setToken($token)->getPayload();
            $jti = $payload['jti']; // JWT ID
            $exp = $payload['exp']; // Thời gian hết hạn

            // Thêm vào bảng blacklist_tokens
            \DB::table('blacklist_tokens')->insert([
                'token_id' => $jti,
                'expires_at' => date('Y-m-d H:i:s', $exp),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('Token đã được thêm vào blacklist: ' . substr($token, 0, 10) . '...');
            return true;
        } catch (\Exception $e) {
            Log::error('Lỗi khi thêm token vào blacklist: ' . $e->getMessage());
            return false;
        }
    }

    public function logout(Request $request)
    {
        try {
            // Logout Laravel Auth session
            Auth::logout();

            // Lấy JWT token từ cookie hoặc session
            $token = null;
            if ($request->cookie('jwt_token')) {
                $token = $request->cookie('jwt_token');
            } elseif (Session::has('jwt_token')) {
                $token = Session::get('jwt_token');
            }

            // Nếu có token JWT, vô hiệu hóa nó và thêm vào blacklist
            if ($token) {
                try {
                    JWTAuth::setToken($token);
                    $this->addTokenToBlacklist($token);
                    JWTAuth::invalidate($token);
                } catch (\Exception $e) {
                    Log::error('Lỗi khi vô hiệu hóa token: ' . $e->getMessage());
                }
            }

            // Xóa JWT token khỏi session
            Session::forget('jwt_token');

            // Invalidate session và regenerate CSRF token
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Đăng xuất thành công'
                ])->cookie(cookie('jwt_token', '', -1, '/', null, false, true, false, 'Lax'));
            }

            // Tạo cookie hết hạn để xóa token
            $cookie = cookie(
                'jwt_token',      // Tên cookie
                '',               // Giá trị rỗng
                -1,               // Thời gian âm để xóa cookie
                '/',              // Path
                null,             // Domain (null = domain hiện tại)
                false,            // Secure (false cho HTTP local)
                true,             // HttpOnly (không thể truy cập bằng JavaScript)
                false,            // Raw
                'Lax'             // SameSite
            );

            // Chuyển hướng đến trang đăng nhập
            return redirect('/login')
                ->with('force_logout', true) // Thêm flag để buộc đăng nhập lại
                ->cookie($cookie);
        } catch (JWTException $e) {
            Log::error('Lỗi đăng xuất: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json(['error' => 'Không thể đăng xuất, vui lòng thử lại'], 500);
            }

            return redirect('/login');
        }
    }

    public function getToken(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Email hoặc mật khẩu không đúng'
                ], 401);
            }

            $user = auth()->user();

            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Lỗi server: ' . $e->getMessage()
            ], 500);
        }
    }

    public function refresh()
    {
        try {
            // Lấy token hiện tại
            $current_token = JWTAuth::getToken();

            if (!$current_token) {
                return response()->json(['error' => 'Token không tồn tại'], 401);
            }

            try {
                // Kiểm tra token có trong blacklist không
                $payload = JWTAuth::getPayload($current_token);
                $jti = $payload['jti'];

                // Kiểm tra trong blacklist (giả định có bảng blacklist_tokens)
                $blacklisted = \DB::table('blacklist_tokens')->where('token_id', $jti)->exists();
                if ($blacklisted) {
                    return response()->json(['error' => 'Token đã bị vô hiệu hóa'], 401);
                }

                // Tạo token mới
                $new_token = JWTAuth::refresh($current_token);

                // Lưu token cũ vào blacklist
                $this->addTokenToBlacklist($current_token);

                // Lấy thông tin user từ token mới
                JWTAuth::setToken($new_token);
                $user = JWTAuth::toUser();

                // Lấy thời gian hết hạn
                $payload = JWTAuth::getPayload();
                $expires_at = date('Y-m-d H:i:s', $payload['exp']);

                return response()->json([
                    'token' => $new_token,
                    'user' => $user,
                    'expires_at' => $expires_at
                ]);
            } catch (TokenExpiredException $e) {
                // Token đã hết hạn, thử refresh bằng cách khác
                try {
                    // Sử dụng JWTAuth::manager() để refresh token đã hết hạn
                    $new_token = JWTAuth::manager()->refresh(JWTAuth::getToken(), false, true);

                    // Lưu token cũ vào blacklist
                    $this->addTokenToBlacklist($current_token);

                    // Lấy thông tin user từ token mới
                    JWTAuth::setToken($new_token);
                    $user = JWTAuth::toUser();

                    // Lấy thời gian hết hạn
                    $payload = JWTAuth::getPayload();
                    $expires_at = date('Y-m-d H:i:s', $payload['exp']);

                    return response()->json([
                        'token' => $new_token,
                        'user' => $user,
                        'expires_at' => $expires_at
                    ]);
                } catch (\Exception $e) {
                    Log::error('Lỗi refresh token đã hết hạn: ' . $e->getMessage());
                    return response()->json(['error' => 'Token đã hết hạn và không thể refresh'], 401);
                }
            }
        } catch (JWTException $e) {
            Log::error('Lỗi refresh token: ' . $e->getMessage());
            return response()->json(['error' => 'Không thể refresh token: ' . $e->getMessage()], 500);
        }
    }

    public function me(Request $request)
    {
        try {
            // KHÔNG kiểm tra Laravel Auth để buộc phải dùng JWT
            // if (Auth::check()) {
            //     $user = Auth::user();
            //     Log::info('User đã xác thực qua Laravel Auth: ' . $user->id);
            //     return response()->json(['valid' => true, 'user' => $user]);
            // }

            // Lấy token từ request
            $token = null;

            // Kiểm tra token trong header Authorization
            $authHeader = $request->header('Authorization');
            if ($authHeader && strpos($authHeader, 'Bearer ') === 0) {
                $token = str_replace('Bearer ', '', $authHeader);
                JWTAuth::setToken($token);
            }

            // Nếu không có token trong header, kiểm tra trong session
            if (!$token && session()->has('jwt_token')) {
                $token = session()->get('jwt_token');
                JWTAuth::setToken($token);
            }

            // Nếu không có token trong header hoặc session, kiểm tra trong cookie
            if (!$token && $request->cookie('jwt_token')) {
                $token = $request->cookie('jwt_token');
                JWTAuth::setToken($token);
            }

            // Nếu không tìm thấy token ở đâu cả
            if (!$token) {
                Log::error('JWT Token không tồn tại trong request, session hoặc cookie');
                return response()->json(['valid' => false, 'error' => 'Token không tồn tại'], 401);
            }

            // Không log token để bảo mật

            // Lấy thông tin user hiện tại từ token
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                Log::error('JWT User không tìm thấy');
                return response()->json(['valid' => false, 'error' => 'Không tìm thấy người dùng'], 404);
            }

            // Không tự động đăng nhập người dùng vào Laravel Auth
            // Chỉ kiểm tra tính hợp lệ của token

            Log::info('JWT User đã xác thực: ' . $user->id);

            // Chỉ trả về thông tin cần thiết, không trả về toàn bộ thông tin user
            return response()->json([
                'valid' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    // Không trả về các thông tin nhạy cảm khác
                ]
            ]);
        } catch (TokenExpiredException $e) {
            Log::error('JWT Token đã hết hạn: ' . $e->getMessage());
            return response()->json(['valid' => false, 'error' => 'Token đã hết hạn'], 401);
        } catch (TokenInvalidException $e) {
            Log::error('JWT Token không hợp lệ: ' . $e->getMessage());
            return response()->json(['valid' => false, 'error' => 'Token không hợp lệ'], 401);
        } catch (JWTException $e) {
            Log::error('Lỗi JWT: ' . $e->getMessage());
            return response()->json(['valid' => false, 'error' => 'Lỗi token: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Đổi mật khẩu cho người dùng đã đăng nhập
     */
    public function changePassword(Request $request)
    {
        try {
            $user = Auth::user();

            // Validate dữ liệu đầu vào
            $validatedData = $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:6|confirmed',
            ]);

            // Kiểm tra mật khẩu hiện tại
            if (!Hash::check($validatedData['current_password'], $user->password)) {
                return back()->with('error', 'Mật khẩu hiện tại không chính xác');
            }

            // Cập nhật mật khẩu mới
            $user->password = Hash::make($validatedData['new_password']);
            $user->save();

            // Đăng xuất khỏi các thiết bị khác (vô hiệu hóa tất cả token)
            // Lấy tất cả token của user và thêm vào blacklist
            try {
                // Thêm logic vô hiệu hóa token nếu cần
                // Ví dụ: Có thể thêm tất cả token hiện tại vào blacklist
            } catch (\Exception $e) {
                Log::error('Lỗi khi vô hiệu hóa token sau khi đổi mật khẩu: ' . $e->getMessage());
            }

            // Không tự động đăng nhập lại - sử dụng JWT hoàn toàn
            // Auth::login($user);

            // Tạo token JWT mới
            $token = JWTAuth::fromUser($user);

            // Tạo cookie mới với token mới
            $minutes = 60; // 1 giờ
            $cookie = cookie(
                'jwt_token',
                $token,
                $minutes,
                '/',
                null,
                false,
                true,
                false,
                'Lax'
            );

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Đổi mật khẩu thành công'])->cookie($cookie);
            }

            return redirect()->back()->with('success', 'Đổi mật khẩu thành công')->cookie($cookie);
        } catch (\Exception $e) {
            Log::error('Lỗi đổi mật khẩu: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json(['error' => 'Đổi mật khẩu thất bại: ' . $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Đổi mật khẩu thất bại: ' . $e->getMessage());
        }
    }

    /**
     * Cập nhật thông tin cá nhân của người dùng
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();

            // Validate dữ liệu đầu vào
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
            ]);

            // Cập nhật thông tin người dùng
            $user->name = $validatedData['name'];
            $user->phone = $validatedData['phone'];
            $user->address = $validatedData['address'];
            $user->save();

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Cập nhật thông tin thành công', 'user' => $user]);
            }

            return redirect()->back()->with('success', 'Cập nhật thông tin thành công');
        } catch (\Exception $e) {
            Log::error('Lỗi cập nhật thông tin cá nhân: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json(['error' => 'Cập nhật thông tin thất bại: ' . $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Cập nhật thông tin thất bại: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Desktop app - Cập nhật thông tin cá nhân
     */
    public function desktopUpdateProfile(Request $request)
    {
        try {
            // Lấy token từ request
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'error' => 'Token is absent'
                ]);
            }

            // Thiết lập token cho JWTAuth
            JWTAuth::setToken($token);

            // Xác thực token
            try {
                $user = JWTAuth::parseToken()->authenticate();
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'error' => 'User not found'
                    ]);
                }

                // Validate dữ liệu đầu vào
                $validatedData = $request->validate([
                    'name' => 'required|string|max:255',
                    'phone' => 'nullable|string|max:20',
                    'address' => 'nullable|string|max:255',
                ]);

                // Cập nhật thông tin người dùng
                $user->name = $validatedData['name'];
                $user->phone = $validatedData['phone'];
                $user->address = $validatedData['address'];
                $user->save();

                Log::info('Desktop app - User profile updated', ['user_id' => $user->id]);

                return response()->json([
                    'success' => true,
                    'message' => 'Cập nhật thông tin thành công',
                    'user' => $user
                ]);

            } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'Token has expired',
                    'tokenExpired' => true
                ]);
            } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'Token is invalid'
                ]);
            } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'Token error: ' . $e->getMessage()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Desktop app - Error updating profile: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Desktop app - Đổi mật khẩu
     */
    public function desktopChangePassword(Request $request)
    {
        try {
            // Lấy token từ request
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'error' => 'Token is absent'
                ]);
            }

            // Thiết lập token cho JWTAuth
            JWTAuth::setToken($token);

            // Xác thực token
            try {
                $user = JWTAuth::parseToken()->authenticate();
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'error' => 'User not found'
                    ]);
                }

                // Validate dữ liệu đầu vào
                $validatedData = $request->validate([
                    'current_password' => 'required|string',
                    'new_password' => 'required|string|min:6|confirmed',
                ]);

                // Kiểm tra mật khẩu hiện tại
                if (!Hash::check($validatedData['current_password'], $user->password)) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Mật khẩu hiện tại không đúng'
                    ]);
                }

                // Cập nhật mật khẩu mới
                $user->password = Hash::make($validatedData['new_password']);
                $user->save();

                Log::info('Desktop app - User password updated', ['user_id' => $user->id]);

                return response()->json([
                    'success' => true,
                    'message' => 'Đổi mật khẩu thành công'
                ]);

            } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'Token has expired',
                    'tokenExpired' => true
                ]);
            } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'Token is invalid'
                ]);
            } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'Token error: ' . $e->getMessage()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Desktop app - Error changing password: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
}
