<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class ManagerController extends Controller
{
    /**
     * Hiển thị dashboard cho manager
     */
    public function dashboard()
    {
        try {
            // Kiểm tra quyền truy cập
            if (Auth::user()->role !== 'manager' && Auth::user()->role !== 'admin') {
                return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập trang này.');
            }

            // Lấy 5 công việc gần đây nhất của team
            $user = Auth::user();
            $managedTeams = $user->managedTeams()->pluck('id');
            $teamMemberIds = User::whereIn('team_id', $managedTeams)->pluck('id');

            $recentTasks = Task::with(['creator', 'assignedUser'])
                ->where(function($query) use ($teamMemberIds, $user) {
                    $query->whereIn('assigned_to', $teamMemberIds)
                          ->orWhereIn('creator_id', $teamMemberIds)
                          ->orWhere('creator_id', $user->id);
                })
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            return view('manager.dashboard', compact('recentTasks'));
        } catch (\Exception $e) {
            Log::error('Error in ManagerController@dashboard: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị danh sách tất cả người dùng (chỉ user thường)
     * Manager không có quyền quản lý người dùng, chỉ xem để gán task
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
            if (Auth::user()->role !== 'manager' && Auth::user()->role !== 'admin') {
                if (request()->wantsJson()) {
                    return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
                }
                return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập trang này.');
            }

            // Manager chỉ có thể xem danh sách user trong team họ quản lý
            $managedTeams = Auth::user()->managedTeams()->pluck('id');
            $users = User::whereIn('team_id', $managedTeams)->get();

            // Nếu là API request (từ desktop app), trả về JSON
            if (request()->wantsJson() || request()->header('Authorization')) {
                return response()->json([
                    'success' => true,
                    'users' => $users
                ]);
            }

            return redirect()->route('manager.dashboard');
        } catch (\Exception $e) {
            Log::error('Error in ManagerController@users: ' . $e->getMessage());
            if (request()->wantsJson() || request()->header('Authorization')) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị danh sách tất cả công việc của tất cả user
     */
    public function allTasks()
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
            if (Auth::user()->role !== 'manager' && Auth::user()->role !== 'admin') {
                if (request()->wantsJson() || request()->header('Authorization')) {
                    return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
                }
                return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập trang này.');
            }

            // Manager chỉ xem tasks của team họ quản lý
            $user = Auth::user();
            $managedTeams = $user->managedTeams()->pluck('id');
            $teamMemberIds = User::whereIn('team_id', $managedTeams)->pluck('id');

            $tasks = Task::with(['creator', 'assignedUser'])
                ->where(function($query) use ($teamMemberIds, $user) {
                    $query->whereIn('creator_id', $teamMemberIds)
                          ->orWhereIn('assigned_to', $teamMemberIds)
                          ->orWhere('creator_id', $user->id); // Tasks tạo bởi chính manager
                })
                ->get();

            // Nếu là API request (từ desktop app), trả về JSON
            if (request()->wantsJson() || request()->header('Authorization')) {
                return response()->json([
                    'success' => true,
                    'tasks' => $tasks
                ]);
            }

            return view('manager.all-tasks', compact('tasks'));
        } catch (\Exception $e) {
            Log::error('Error in ManagerController@allTasks: ' . $e->getMessage());
            if (request()->wantsJson() || request()->header('Authorization')) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị form tạo công việc mới cho user
     */
    public function createTaskForm()
    {
        try {
            // Kiểm tra quyền truy cập
            if (Auth::user()->role !== 'manager' && Auth::user()->role !== 'admin') {
                return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập trang này.');
            }
            $users = User::where('role', 'user')->get();
            return view('manager.create-task', compact('users'));
        } catch (\Exception $e) {
            Log::error('Error in ManagerController@createTaskForm: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Lưu công việc mới cho user
     */
    public function storeTask(Request $request)
    {
        try {
            // Kiểm tra quyền truy cập
            if (Auth::user()->role !== 'manager' && Auth::user()->role !== 'admin') {
                return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập trang này.');
            }
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_date' => 'nullable|date',
                'due_date' => 'nullable|date',
                'status' => 'required|in:pending,in_progress,completed',
                'priority' => 'required|in:low,medium,high',
                'user_id' => 'required|exists:users,id',
            ]);

            // Kiểm tra và điều chỉnh ngày nếu due_date < start_date
            $dateMessage = Task::validateAndAdjustDates($validatedData);

            $task = Task::create([
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'start_date' => $validatedData['start_date'],
                'due_date' => $validatedData['due_date'],
                'status' => $validatedData['status'],
                'priority' => $validatedData['priority'],
                'creator_id' => $validatedData['user_id'],
            ]);

            $successMessage = 'Công việc đã được tạo thành công.';
            if ($dateMessage) {
                $successMessage .= ' ' . $dateMessage;
            }

            return redirect()->route('manager.all-tasks')->with('success', $successMessage);
        } catch (\Exception $e) {
            Log::error('Error in ManagerController@storeTask: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Hiển thị form chỉnh sửa công việc
     */
    public function editTask($id)
    {
        try {
            // Kiểm tra quyền truy cập
            if (Auth::user()->role !== 'manager' && Auth::user()->role !== 'admin') {
                return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập trang này.');
            }
            $task = Task::findOrFail($id);
            $users = User::where('role', 'user')->get();
            return view('manager.edit-task', compact('task', 'users'));
        } catch (\Exception $e) {
            Log::error('Error in ManagerController@editTask: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Cập nhật công việc
     */
    public function updateTask(Request $request, $id)
    {
        try {
            // Kiểm tra quyền truy cập
            if (Auth::user()->role !== 'manager' && Auth::user()->role !== 'admin') {
                return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập trang này.');
            }
            $task = Task::findOrFail($id);

            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_date' => 'nullable|date',
                'due_date' => 'nullable|date',
                'status' => 'required|in:pending,in_progress,completed',
                'priority' => 'required|in:low,medium,high',
                'user_id' => 'required|exists:users,id',
            ]);

            // Kiểm tra và điều chỉnh ngày nếu due_date < start_date
            $dateMessage = Task::validateAndAdjustDates($validatedData);

            $task->update([
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'start_date' => $validatedData['start_date'],
                'due_date' => $validatedData['due_date'],
                'status' => $validatedData['status'],
                'priority' => $validatedData['priority'],
                'creator_id' => $validatedData['user_id'],
            ]);

            $successMessage = 'Công việc đã được cập nhật thành công.';
            if ($dateMessage) {
                $successMessage .= ' ' . $dateMessage;
            }

            return redirect()->route('manager.all-tasks')->with('success', $successMessage);
        } catch (\Exception $e) {
            Log::error('Error in ManagerController@updateTask: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Xóa công việc
     */
    public function deleteTask($id)
    {
        try {
            // Kiểm tra quyền truy cập
            if (Auth::user()->role !== 'manager' && Auth::user()->role !== 'admin') {
                return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập trang này.');
            }
            $task = Task::findOrFail($id);
            $task->delete();

            return redirect()->route('manager.all-tasks')->with('success', 'Công việc đã được xóa thành công.');
        } catch (\Exception $e) {
            Log::error('Error in ManagerController@deleteTask: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị báo cáo và thống kê
     */
    public function reports()
    {
        try {
            // Kiểm tra quyền truy cập
            if (Auth::user()->role !== 'manager' && Auth::user()->role !== 'admin') {
                return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập trang này.');
            }
            $user = Auth::user();

            // Lấy team mà manager này quản lý
            $managedTeams = $user->managedTeams()->pluck('id');
            $teamMemberIds = User::whereIn('team_id', $managedTeams)->pluck('id');

            // Thống kê tasks của team
            $totalTasks = Task::whereIn('assigned_to', $teamMemberIds)->count();
            $pendingTasks = Task::whereIn('assigned_to', $teamMemberIds)->where('status', 'pending')->count();
            $inProgressTasks = Task::whereIn('assigned_to', $teamMemberIds)->where('status', 'in_progress')->count();
            $completedTasks = Task::whereIn('assigned_to', $teamMemberIds)->where('status', 'completed')->count();

            // Thống kê theo từng thành viên trong team
            $userStats = User::whereIn('team_id', $managedTeams)
                ->withCount([
                    'assignedTasks as total_tasks',
                    'assignedTasks as pending_tasks' => function ($query) {
                        $query->where('status', 'pending');
                    },
                    'assignedTasks as in_progress_tasks' => function ($query) {
                        $query->where('status', 'in_progress');
                    },
                    'assignedTasks as completed_tasks' => function ($query) {
                        $query->where('status', 'completed');
                    }
                ])
                ->get();

            return view('manager.reports', compact(
                'totalTasks',
                'pendingTasks',
                'inProgressTasks',
                'completedTasks',
                'userStats'
            ));
        } catch (\Exception $e) {
            Log::error('Error in ManagerController@reports: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }
}
