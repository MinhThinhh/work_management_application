<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class TaskController extends Controller
{
    public function index()
    {
        try {
            // Kiểm tra authentication
            if (!Auth::check()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Lấy user hiện tại
            $user = Auth::user();

            // Phân quyền: Manager xem tất cả tasks, User chỉ xem tasks của mình, Admin không xem tasks
            if ($user->role === 'manager') {
                $tasks = Task::with('creator')->get();
            } elseif ($user->role === 'admin') {
                return response()->json(['error' => 'Admin không có quyền xem tasks. Admin chỉ quản lý users.'], 403);
            } else {
                $tasks = Task::where('creator_id', $user->id)->get();
            }

            return response()->json($tasks);
        } catch (\Exception $e) {
            Log::error('Error in TaskController@index: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function dashboard(Request $request)
    {
        try {
            // Lấy user hiện tại
            $user = Auth::user();

            // Lấy các task của user hiện tại
            $tasks = Task::where('creator_id', $user->id)->get();

            if ($request->query('view') == 'calendar') {
                $calendarEvents = $tasks->map(function ($task) {
                    return [
                        'id' => $task->id,
                        'title' => $task->title,
                        'start' => $task->due_date,
                        'backgroundColor' => $this->getPriorityColor($task->priority),
                        'borderColor' => $this->getPriorityBorderColor($task->priority),
                        'extendedProps' => [
                            'description' => $task->description,
                            'priority' => $task->priority,
                            'status' => $task->status
                        ]
                    ];
                });
            } else {
                $calendarEvents = [];
            }

            return view('dashboard', compact('tasks', 'calendarEvents'));

        } catch (\Exception $e) {
            Log::error('Error in TaskController@dashboard: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_date' => 'required|date',
                'due_date' => 'required|date',
                'priority' => 'required|in:low,medium,high',
                'status' => 'nullable|in:pending,in_progress,completed',
            ]);

            if (!isset($validatedData['status'])) {
                $validatedData['status'] = 'pending';
            }

            // Kiểm tra authentication
            if (!Auth::check()) {
                if ($request->wantsJson()) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                }
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
            }

            $task = Task::create([
                'title' => $validatedData['title'],
                'description' => $validatedData['description'] ?? null,
                'start_date' => $validatedData['start_date'],
                'due_date' => $validatedData['due_date'],
                'status' => $validatedData['status'],
                'priority' => $validatedData['priority'],
                'creator_id' => Auth::id(),
            ]);

            Log::info('Task created', ['task_id' => $task->id]);

            if ($request->wantsJson()) {
                return response()->json($task, 201);
            }

            return redirect()->route('dashboard')->with('success', 'Công việc đã được tạo thành công!');
        } catch (\Exception $e) {
            Log::error('Error in TaskController@store: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        try {
            $task = Task::findOrFail($id);

            if (request()->wantsJson()) {
                return response()->json($task);
            }

            return view('tasks.show', compact('task'));
        } catch (\Exception $e) {
            Log::error('Error in TaskController@show: ' . $e->getMessage());

            if (request()->wantsJson()) {
                return response()->json(['error' => 'Task not found'], 404);
            }

            return redirect()->back()->with('error', 'Công việc không tồn tại');
        }
    }

    public function edit($id)
    {
        try {
            $task = Task::findOrFail($id);

            if (request()->wantsJson()) {
                return response()->json($task);
            }

            return view('tasks.edit', compact('task'));
        } catch (\Exception $e) {
            Log::error('Error in TaskController@edit: ' . $e->getMessage());

            if (request()->wantsJson()) {
                return response()->json(['error' => 'Task not found'], 404);
            }

            return redirect()->back()->with('error', 'Công việc không tồn tại');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Kiểm tra authentication
            if (!Auth::check()) {
                if ($request->wantsJson()) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                }
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
            }

            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_date' => 'required|date',
                'due_date' => 'required|date',
                'priority' => 'required|in:low,medium,high',
                'status' => 'required|in:pending,in_progress,completed',
            ]);

            $task = Task::findOrFail($id);
            $task->update($validatedData);

            Log::info('Task updated', ['task_id' => $task->id]);

            if ($request->wantsJson()) {
                return response()->json($task);
            }

            return redirect()->route('dashboard')->with('success', 'Công việc đã được cập nhật thành công!');
        } catch (\Exception $e) {
            Log::error('Error in TaskController@update: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            // Kiểm tra authentication
            if (!Auth::check()) {
                if (request()->wantsJson()) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                }
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
            }

            $task = Task::findOrFail($id);
            $task->delete();

            Log::info('Task deleted', ['task_id' => $id]);

            if (request()->wantsJson()) {
                return response()->json(['success' => true]);
            }

            return redirect()->route('dashboard')->with('success', 'Công việc đã được xóa thành công!');
        } catch (\Exception $e) {
            Log::error('Error in TaskController@destroy: ' . $e->getMessage());

            if (request()->wantsJson()) {
                return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    public function myTasks()
    {
        try {
            $user = Auth::user();
            $tasks = Task::where('creator_id', $user->id)->get();
            return response()->json($tasks);
        } catch (\Exception $e) {
            Log::error('Error in TaskController@myTasks: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function getEvents(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            // Lấy user hiện tại
            $user = Auth::user();

            $query = Task::query();

            // Lọc theo user hiện tại
            $query->where('creator_id', $user->id);

            if ($startDate) {
                $query->where('due_date', '>=', $startDate);
            }

            if ($endDate) {
                $query->where('start_date', '<=', $endDate);
            }

            $tasks = $query->get();

            $events = $tasks->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'start' => $task->start_date,
                    'end' => $task->due_date,
                    'color' => $this->getPriorityColor($task->priority),
                    'priority' => $task->priority,
                    'status' => $task->status
                ];
            });

            return response()->json($events);
        } catch (\Exception $e) {
            Log::error('Error in TaskController@getEvents: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function getDayEvents(Request $request)
    {
        try {
            $date = $request->input('date');

            // Lấy user hiện tại
            $user = Auth::user();

            if (!$date) {
                return response()->json(['error' => 'Date is required'], 400);
            }

            $events = Task::whereDate('start_date', '<=', $date)
                ->whereDate('due_date', '>=', $date)
                ->where('creator_id', $user->id) // Lọc theo user hiện tại
                ->get()
                ->map(function ($task) {
                    return [
                        'id' => $task->id,
                        'title' => $task->title,
                        'description' => $task->description,
                        'start' => $task->start_date,
                        'end' => $task->due_date,
                        'color' => $this->getPriorityColor($task->priority),
                        'priority' => $task->priority,
                        'status' => $task->status
                    ];
                });

            return response()->json($events);
        } catch (\Exception $e) {
            Log::error('Error in TaskController@getDayEvents: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function getMonthEvents(Request $request)
    {
        try {
            $year = $request->input('year');
            $month = $request->input('month');

            if (!$year || !$month) {
                return response()->json(['error' => 'Year and month are required'], 400);
            }

            $startDate = "{$year}-{$month}-01";
            $endDate = date('Y-m-t', strtotime($startDate));

            // Lấy user hiện tại
            $user = Auth::user();

            $events = Task::where('creator_id', $user->id) // Lọc theo user hiện tại
            ->where(function($query) use ($startDate, $endDate) {
                $query->where(function($q) use ($startDate, $endDate) {
                    $q->whereDate('start_date', '>=', $startDate)
                      ->whereDate('start_date', '<=', $endDate);
                })->orWhere(function($q) use ($startDate, $endDate) {
                    $q->whereDate('due_date', '>=', $startDate)
                      ->whereDate('due_date', '<=', $endDate);
                })->orWhere(function($q) use ($startDate, $endDate) {
                    $q->whereDate('start_date', '<=', $startDate)
                      ->whereDate('due_date', '>=', $endDate);
                });
            })->get()->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'start' => $task->start_date,
                    'end' => $task->due_date,
                    'color' => $this->getPriorityColor($task->priority),
                    'priority' => $task->priority,
                    'status' => $task->status
                ];
            });

            return response()->json($events);
        } catch (\Exception $e) {
            Log::error('Error in TaskController@getMonthEvents: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    private function getPriorityColor($priority)
    {
        switch($priority) {
            case 'high': return '#EF4444';
            case 'medium': return '#3B82F6';
            case 'low': return '#9CA3AF';
            default: return '#6B7280';
        }
    }

    private function getPriorityBorderColor($priority)
    {
        switch($priority) {
            case 'high': return '#B91C1C';
            case 'medium': return '#2563EB';
            case 'low': return '#4B5563';
            default: return '#4B5563';
        }
    }

    public function getCalendarEvents(Request $request)
    {
        try {
            $startDate = $request->input('start');
            $endDate = $request->input('end');
            $date = $request->input('date');

            // Lấy user hiện tại
            $user = Auth::user();

            // Log để debug
            Log::info('Calendar API request params:', [
                'start' => $startDate,
                'end' => $endDate,
                'date' => $date,
                'user_id' => $user->id,
                'user_role' => $user->role
            ]);

            $query = Task::query();

            // Phân quyền: Manager xem tất cả tasks, User chỉ xem tasks của mình, Admin không xem tasks
            if ($user->role === 'manager') {
                // Manager xem tất cả tasks
                $query->with('creator');
            } elseif ($user->role === 'admin') {
                return response()->json(['error' => 'Admin không có quyền xem tasks. Admin chỉ quản lý users.'], 403);
            } else {
                // User chỉ xem tasks của mình
                $query->where('creator_id', $user->id);
            }

            // Không lọc theo ngày nếu không có tham số
            if ($startDate) {
                $query->where('start_date', '>=', $startDate);
            }

            if ($endDate) {
                $query->where('due_date', '<=', $endDate);
            }

            if ($date) {
                $query->whereDate('start_date', '<=', $date)
                      ->whereDate('due_date', '>=', $date);
            }

            $tasks = $query->get();

            Log::info('Calendar events query returned ' . $tasks->count() . ' tasks');

            // Log chi tiết các task để debug
            foreach ($tasks as $index => $task) {
                Log::info("Task #{$index}: ID={$task->id}, Title={$task->title}, Start={$task->start_date}, Due={$task->due_date}");
            }

            $events = $tasks->map(function ($task) {
                // Assign different time slots based on priority
                $startHour = 9; // Default 9:00 AM
                $endHour = 10; // Default 10:00 AM (1 hour duration)

                // Adjust time based on priority
                switch ($task->priority) {
                    case 'high':
                        $startHour = 8; // 8:00 AM
                        $endHour = 10; // 10:00 AM (2 hours)
                        break;
                    case 'medium':
                        $startHour = 12; // 12:00 PM
                        $endHour = 14; // 2:00 PM (2 hours)
                        break;
                    case 'low':
                        $startHour = 15; // 3:00 PM
                        $endHour = 16; // 4:00 PM (1 hour)
                        break;
                }

                // Convert hours to minutes for the calendar
                $startTime = $startHour * 60;
                $endTime = $endHour * 60;

                // Format the event data - Sử dụng start_date thay vì due_date cho ngày hiển thị
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'date' => date('Y-m-d', strtotime($task->start_date)), // Sử dụng start_date
                    'start_date' => $task->start_date,
                    'due_date' => $task->due_date,
                    'startTime' => $startTime,
                    'endTime' => $endTime,
                    'priority' => $task->priority,
                    'status' => $task->status,
                    'color' => $this->getPriorityColor($task->priority)
                ];
            });

            Log::info('Returning ' . count($events) . ' calendar events');

            return response()->json($events);
        } catch (\Exception $e) {
            Log::error('Error in TaskController@getCalendarEvents: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}