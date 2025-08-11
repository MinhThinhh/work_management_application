<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminController extends Controller
{
    /**
     * Hiển thị danh sách tất cả người dùng
     */
    public function users()
    {
        try {
            // JWT Authentication cho desktop app (chỉ khi có Authorization header)
            if (request()->header('Authorization') && !Auth::check()) {
                $token = str_replace('Bearer ', '', request()->header('Authorization'));
                try {
                    JWTAuth::setToken($token);
                    $user = JWTAuth::parseToken()->authenticate();
                    if (!$user) {
                        return response()->json(['success' => false, 'error' => 'User not found'], 401);
                    }
                    Auth::login($user);
                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'error' => 'Invalid token'], 401);
                }
            }

            // Kiểm tra user đã authenticate chưa
            if (!Auth::check()) {
                if (request()->wantsJson() || request()->header('Authorization')) {
                    return response()->json(['success' => false, 'error' => 'Unauthenticated'], 401);
                }
                return redirect()->route('login');
            }

            // Kiểm tra quyền truy cập
            if (Auth::user()->role !== 'admin') {
                if (request()->wantsJson() || request()->header('Authorization')) {
                    return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
                }
                return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập trang này.');
            }

            $users = User::all();

            // Nếu là API request (từ desktop app), trả về JSON
            if (request()->wantsJson() || request()->header('Authorization')) {
                return response()->json([
                    'success' => true,
                    'users' => $users
                ]);
            }

            return view('admin.users', compact('users'));
        } catch (\Exception $e) {
            Log::error('Error in AdminController@users: ' . $e->getMessage());
            if (request()->wantsJson() || request()->header('Authorization')) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị form tạo người dùng mới
     */
    public function createUserForm()
    {
        try {
            // Kiểm tra quyền truy cập
            if (Auth::user()->role !== 'admin') {
                return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập trang này.');
            }
            return view('admin.create-user');
        } catch (\Exception $e) {
            Log::error('Error in AdminController@createUserForm: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Lưu người dùng mới
     */
    public function storeUser(Request $request)
    {
        try {
            // Kiểm tra quyền truy cập
            if (Auth::user()->role !== 'admin') {
                return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập trang này.');
            }
            $validatedData = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[a-zA-ZÀ-ỹ\s]+$/u'
                ],
                'email' => 'required|email|unique:users,email',
                'password' => [
                    'required',
                    'min:8',
                    'confirmed',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'
                ],
                'role' => 'required|in:user,manager,admin',
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
                'role' => $validatedData['role'],
            ]);

            return redirect()->route('admin.users')->with('success', 'Người dùng đã được tạo thành công.');
        } catch (\Exception $e) {
            Log::error('Error in AdminController@storeUser: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Hiển thị form chỉnh sửa người dùng
     */
    public function editUser($id)
    {
        try {
            // Kiểm tra quyền truy cập
            if (Auth::user()->role !== 'admin') {
                return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập trang này.');
            }
            $user = User::findOrFail($id);
            return view('admin.edit-user', compact('user'));
        } catch (\Exception $e) {
            Log::error('Error in AdminController@editUser: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Cập nhật người dùng
     */
    public function updateUser(Request $request, $id)
    {
        try {
            // Kiểm tra quyền truy cập
            if (Auth::user()->role !== 'admin') {
                return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập trang này.');
            }
            $user = User::findOrFail($id);

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'role' => 'required|in:user,manager,admin',
                'password' => 'nullable|min:6|confirmed',
            ]);

            $updateData = [
                'name' => $validatedData['name'],
                'role' => $validatedData['role'],
            ];

            if (!empty($validatedData['password'])) {
                $updateData['password'] = Hash::make($validatedData['password']);
            }

            $user->update($updateData);

            return redirect()->route('admin.users')->with('success', 'Người dùng đã được cập nhật thành công. (Email không thể thay đổi vì lý do bảo mật)');
        } catch (\Exception $e) {
            Log::error('Error in AdminController@updateUser: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Xóa người dùng
     */
    public function deleteUser($id)
    {
        try {
            // Kiểm tra quyền truy cập
            if (Auth::user()->role !== 'admin') {
                return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập trang này.');
            }
            $user = User::findOrFail($id);

            // Không cho phép xóa chính mình
            if ($user->id === Auth::id()) {
                return redirect()->route('admin.users')->with('error', 'Bạn không thể xóa tài khoản của chính mình.');
            }

            // Xóa tất cả các công việc của người dùng
            $user->tasks()->delete();

            // Xóa người dùng
            $user->delete();

            return redirect()->route('admin.users')->with('success', 'Người dùng đã được xóa thành công.');
        } catch (\Exception $e) {
            Log::error('Error in AdminController@deleteUser: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị danh sách tất cả công việc (chỉ xem)
     * Admin chỉ có thể xem danh sách công việc, không thể thêm/sửa/xóa
     */
    public function allTasks()
    {
        try {
            // Kiểm tra quyền truy cập
            if (Auth::user()->role !== 'admin') {
                return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập trang này.');
            }
            // Admin chỉ có thể xem danh sách công việc, không thể thêm/sửa/xóa
            $tasks = Task::with(['creator', 'assignedUser'])->get();
            return view('admin.all-tasks', compact('tasks'));
        } catch (\Exception $e) {
            Log::error('Error in AdminController@allTasks: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }
}
