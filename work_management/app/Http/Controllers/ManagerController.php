<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

            // Lấy 5 công việc gần đây nhất
            $recentTasks = Task::with('creator')
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
            // Kiểm tra quyền truy cập
            if (Auth::user()->role !== 'manager' && Auth::user()->role !== 'admin') {
                return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập trang này.');
            }

            // Manager chỉ có thể xem danh sách user để gán task, không thể thêm/sửa/xóa
            $users = User::where('role', 'user')->get();
            return redirect()->route('manager.dashboard');
        } catch (\Exception $e) {
            Log::error('Error in ManagerController@users: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị danh sách tất cả công việc của tất cả user
     */
    public function allTasks()
    {
        try {
            // Kiểm tra quyền truy cập
            if (Auth::user()->role !== 'manager' && Auth::user()->role !== 'admin') {
                return redirect()->route('dashboard')->with('error', 'Bạn không có quyền truy cập trang này.');
            }
            $tasks = Task::with('creator')->get();
            return view('manager.all-tasks', compact('tasks'));
        } catch (\Exception $e) {
            Log::error('Error in ManagerController@allTasks: ' . $e->getMessage());
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
                'due_date' => 'nullable|date|after_or_equal:start_date',
                'status' => 'required|in:pending,in_progress,completed',
                'priority' => 'required|in:low,medium,high',
                'user_id' => 'required|exists:users,id',
            ]);

            $task = Task::create([
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'start_date' => $validatedData['start_date'],
                'due_date' => $validatedData['due_date'],
                'status' => $validatedData['status'],
                'priority' => $validatedData['priority'],
                'creator_id' => $validatedData['user_id'],
            ]);

            return redirect()->route('manager.all-tasks')->with('success', 'Công việc đã được tạo thành công.');
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
                'due_date' => 'nullable|date|after_or_equal:start_date',
                'status' => 'required|in:pending,in_progress,completed',
                'priority' => 'required|in:low,medium,high',
                'user_id' => 'required|exists:users,id',
            ]);

            $task->update([
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'start_date' => $validatedData['start_date'],
                'due_date' => $validatedData['due_date'],
                'status' => $validatedData['status'],
                'priority' => $validatedData['priority'],
                'creator_id' => $validatedData['user_id'],
            ]);

            return redirect()->route('manager.all-tasks')->with('success', 'Công việc đã được cập nhật thành công.');
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
            $totalTasks = Task::count();
            $pendingTasks = Task::where('status', 'pending')->count();
            $inProgressTasks = Task::where('status', 'in_progress')->count();
            $completedTasks = Task::where('status', 'completed')->count();

            $userStats = User::where('role', 'user')
                ->withCount(['tasks as total_tasks',
                            'tasks as pending_tasks' => function ($query) {
                                $query->where('status', 'pending');
                            },
                            'tasks as in_progress_tasks' => function ($query) {
                                $query->where('status', 'in_progress');
                            },
                            'tasks as completed_tasks' => function ($query) {
                                $query->where('status', 'completed');
                            }])
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
