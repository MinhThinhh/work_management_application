<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Hiển thị trang thông tin cá nhân
     */
    public function showProfile()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    /**
     * Cập nhật thông tin cá nhân
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();
            
            $validatedData = $request->validate([
                'name' => 'nullable|string|max:255',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users')->ignore($user->id),
                ],
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
            ]);
            
            $user->update($validatedData);
            
            Log::info('User profile updated', ['user_id' => $user->id]);
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Thông tin cá nhân đã được cập nhật',
                    'user' => $user
                ]);
            }
            
            return redirect()->route('profile')->with('success', 'Thông tin cá nhân đã được cập nhật thành công!');
        } catch (\Exception $e) {
            Log::error('Error updating user profile: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Đã xảy ra lỗi: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Cập nhật mật khẩu
     */
    public function updatePassword(Request $request)
    {
        try {
            $user = Auth::user();
            
            $validatedData = $request->validate([
                'current_password' => 'required',
                'password' => 'required|min:6|confirmed',
            ]);
            
            // Kiểm tra mật khẩu hiện tại
            if (!Hash::check($validatedData['current_password'], $user->password)) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Mật khẩu hiện tại không chính xác'
                    ], 400);
                }
                
                return redirect()->back()->with('error', 'Mật khẩu hiện tại không chính xác');
            }
            
            // Cập nhật mật khẩu mới
            $user->password = Hash::make($validatedData['password']);
            $user->save();
            
            Log::info('User password updated', ['user_id' => $user->id]);
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mật khẩu đã được cập nhật'
                ]);
            }
            
            return redirect()->route('profile')->with('success', 'Mật khẩu đã được cập nhật thành công!');
        } catch (\Exception $e) {
            Log::error('Error updating user password: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Đã xảy ra lỗi: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage())->withInput();
        }
    }
}
