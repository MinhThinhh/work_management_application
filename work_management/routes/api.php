<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AuthController;
use Tymon\JWTAuth\Facades\JWTAuth;

// routes/api.php
// Public routes
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// Desktop app endpoints
Route::post('desktop-login', [AuthController::class, 'desktopLogin']);

// Desktop app add task endpoint
Route::post('desktop-tasks', function (Request $request) {
    try {
        // Lấy token từ request
        $token = $request->bearerToken();
        if (!$token) {
            \Log::error('Desktop add task: Token is absent in request');
            return response()->json([
                'success' => false,
                'error' => 'Token is absent'
            ]);
        }

        \Log::info('Desktop add task: Token received: ' . substr($token, 0, 10) . '...');

        // Thiết lập token cho JWTAuth
        JWTAuth::setToken($token);

        // Xác thực token
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                \Log::error('Desktop add task: User not found');
                return response()->json([
                    'success' => false,
                    'error' => 'User not found'
                ]);
            }

            \Log::info('Desktop add task: User authenticated: ' . $user->id);

            // Chuẩn bị dữ liệu để kiểm tra và điều chỉnh ngày
            $taskData = [
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'due_date' => $request->due_date,
                'status' => $request->status,
                'priority' => $request->priority,
            ];

            // Kiểm tra và điều chỉnh ngày nếu due_date < start_date
            $dateMessage = \App\Models\Task::validateAndAdjustDates($taskData);

            // Tạo công việc mới
            $task = new \App\Models\Task();
            $task->title = $taskData['title'];
            $task->description = $taskData['description'];
            $task->start_date = $taskData['start_date'];
            $task->due_date = $taskData['due_date'];
            $task->status = $taskData['status'];
            $task->priority = $taskData['priority'];
            $task->creator_id = $user->id;
            $task->save();

            \Log::info('Desktop add task: Task created with ID: ' . $task->id);

            $response = [
                'success' => true,
                'task' => $task
            ];

            if ($dateMessage) {
                $response['warning'] = $dateMessage;
            }

            return response()->json($response);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            \Log::error('Desktop add task: Token has expired: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Token has expired',
                'tokenExpired' => true
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            \Log::error('Desktop add task: Token is invalid: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Token is invalid'
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            \Log::error('Desktop add task: JWT Exception: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Token error: ' . $e->getMessage()
            ]);
        }
    } catch (\Exception $e) {
        \Log::error('Desktop add task: Error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

// Desktop app tasks endpoint
Route::get('desktop-tasks', function (Request $request) {
    try {
        // Lấy token từ request
        $token = $request->bearerToken();
        if (!$token) {
            \Log::error('Desktop tasks: Token is absent in request');
            return response()->json([
                'success' => false,
                'error' => 'Token is absent'
            ]);
        }

        \Log::info('Desktop tasks: Token received: ' . substr($token, 0, 10) . '...');

        // Thiết lập token cho JWTAuth
        JWTAuth::setToken($token);

        // Xác thực token
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                \Log::error('Desktop tasks: User not found');
                return response()->json([
                    'success' => false,
                    'error' => 'User not found'
                ]);
            }

            \Log::info('Desktop tasks: User authenticated: ' . $user->id);

            // Lấy danh sách công việc
            $tasks = \App\Models\Task::where('creator_id', $user->id)->get();
            \Log::info('Desktop tasks: Found ' . $tasks->count() . ' tasks for user ' . $user->id);

            return response()->json([
                'success' => true,
                'tasks' => $tasks
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            \Log::error('Desktop tasks: Token has expired: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Token has expired',
                'tokenExpired' => true
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            \Log::error('Desktop tasks: Token is invalid: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Token is invalid'
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            \Log::error('Desktop tasks: JWT Exception: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Token error: ' . $e->getMessage()
            ]);
        }
    } catch (\Exception $e) {
        \Log::error('Desktop tasks: Error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

// Desktop app update task endpoint
Route::put('desktop-tasks/{id}', function (Request $request, $id) {
    try {
        // Lấy token từ request
        $token = $request->bearerToken();
        if (!$token) {
            \Log::error('Desktop update task: Token is absent in request');
            return response()->json([
                'success' => false,
                'error' => 'Token is absent'
            ]);
        }

        \Log::info('Desktop update task: Token received: ' . substr($token, 0, 10) . '...');

        // Thiết lập token cho JWTAuth
        JWTAuth::setToken($token);

        // Xác thực token
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                \Log::error('Desktop update task: User not found');
                return response()->json([
                    'success' => false,
                    'error' => 'User not found'
                ]);
            }

            \Log::info('Desktop update task: User authenticated: ' . $user->id);

            // Tìm task
            $task = \App\Models\Task::where('id', $id)->where('creator_id', $user->id)->first();
            if (!$task) {
                \Log::error('Desktop update task: Task not found or not owned by user');
                return response()->json([
                    'success' => false,
                    'error' => 'Task not found or you do not have permission to update it'
                ]);
            }

            // Validate dữ liệu
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_date' => 'required|date',
                'due_date' => 'required|date',
                'priority' => 'required|in:low,medium,high',
                'status' => 'required|in:pending,in_progress,completed',
            ]);

            // Kiểm tra và điều chỉnh ngày nếu due_date < start_date
            $dateMessage = \App\Models\Task::validateAndAdjustDates($validatedData);

            // Cập nhật task
            $task->update($validatedData);

            \Log::info('Desktop update task: Task updated with ID: ' . $task->id);

            $response = [
                'success' => true,
                'task' => $task
            ];

            if ($dateMessage) {
                $response['warning'] = $dateMessage;
            }

            return response()->json($response);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            \Log::error('Desktop update task: Token has expired: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Token has expired',
                'tokenExpired' => true
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            \Log::error('Desktop update task: Token is invalid: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Token is invalid'
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            \Log::error('Desktop update task: JWT error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'JWT error: ' . $e->getMessage()
            ]);
        }
    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Desktop update task: Validation error: ' . json_encode($e->errors()));
        return response()->json([
            'success' => false,
            'error' => 'Validation failed',
            'errors' => $e->errors()
        ]);
    } catch (\Exception $e) {
        \Log::error('Desktop update task: Error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

// Desktop app delete task endpoint
Route::delete('desktop-tasks/{id}', function (Request $request, $id) {
    try {
        // Lấy token từ request
        $token = $request->bearerToken();
        if (!$token) {
            \Log::error('Desktop delete task: Token is absent in request');
            return response()->json([
                'success' => false,
                'error' => 'Token is absent'
            ]);
        }

        \Log::info('Desktop delete task: Token received: ' . substr($token, 0, 10) . '...');

        // Thiết lập token cho JWTAuth
        JWTAuth::setToken($token);

        // Xác thực token
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                \Log::error('Desktop delete task: User not found');
                return response()->json([
                    'success' => false,
                    'error' => 'User not found'
                ]);
            }

            \Log::info('Desktop delete task: User authenticated: ' . $user->id);

            // Tìm task
            $task = \App\Models\Task::where('id', $id)->where('creator_id', $user->id)->first();
            if (!$task) {
                \Log::error('Desktop delete task: Task not found or not owned by user');
                return response()->json([
                    'success' => false,
                    'error' => 'Task not found or you do not have permission to delete it'
                ]);
            }

            // Xóa task
            $task->delete();

            \Log::info('Desktop delete task: Task deleted with ID: ' . $id);

            return response()->json([
                'success' => true,
                'message' => 'Task deleted successfully'
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            \Log::error('Desktop delete task: Token has expired: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Token has expired',
                'tokenExpired' => true
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            \Log::error('Desktop delete task: Token is invalid: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Token is invalid'
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            \Log::error('Desktop delete task: JWT error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'JWT error: ' . $e->getMessage()
            ]);
        }
    } catch (\Exception $e) {
        \Log::error('Desktop delete task: Error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

// Health check endpoint
Route::get('health-check', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API is running',
        'timestamp' => now()->toIso8601String()
    ]);
});

// Route lấy token mới
Route::post('get-token', function (Request $request) {
    try {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        \Log::info('API get-token: Đang xử lý cho email: ' . $credentials['email']);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            \Log::info('API get-token: Xác thực thành công cho user ID: ' . $user->id);

            try {
                $token = JWTAuth::fromUser($user);
                \Log::info('API get-token: Token đã được tạo: ' . substr($token, 0, 10) . '...');

                // Kiểm tra token đã được tạo
                try {
                    JWTAuth::setToken($token);
                    $payload = JWTAuth::getPayload()->toArray();
                    \Log::info('API get-token: Token hợp lệ, expires at: ' . date('Y-m-d H:i:s', $payload['exp']));

                    return response()->json([
                        'success' => true,
                        'token' => $token,
                        'user' => $user,
                        'expires_at' => date('Y-m-d H:i:s', $payload['exp'])
                    ], 200);
                } catch (\Exception $e) {
                    \Log::error('API get-token: Token không hợp lệ sau khi tạo: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'error' => 'Lỗi xác thực token: ' . $e->getMessage()
                    ], 200);
                }
            } catch (\Exception $e) {
                \Log::error('API get-token: Lỗi tạo token: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'error' => 'Lỗi tạo token: ' . $e->getMessage()
                ], 200);
            }
        }

        \Log::error('API get-token: Xác thực thất bại cho email: ' . $credentials['email']);
        return response()->json([
            'success' => false,
            'error' => 'Thông tin đăng nhập không chính xác'
        ], 200);
    } catch (\Exception $e) {
        \Log::error('API get-token: Lỗi: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 200);
    }
});

// Route kiểm tra token không cần middleware
Route::get('token-info', function () {
    try {
        // Lấy token từ header Authorization
        $token = null;
        $request = request();

        if ($request->hasHeader('Authorization')) {
            $authHeader = $request->header('Authorization');
            if (strpos($authHeader, 'Bearer ') === 0) {
                $token = substr($authHeader, 7);
                \Log::info('API token-info: Token nhận được từ header: ' . substr($token, 0, 10) . '...');
            } else {
                \Log::error('API token-info: Header Authorization không đúng định dạng Bearer');
            }
        } else {
            \Log::error('API token-info: Không có header Authorization');
        }

        if (!$token) {
            \Log::error('API token-info: Token không tồn tại trong request');
            return response()->json(['valid' => false, 'error' => 'Token không tồn tại'], 200);
        }

        try {
            // Thiết lập token cho JWTAuth
            JWTAuth::setToken($token);

            // Kiểm tra token có hợp lệ không
            if (!JWTAuth::check()) {
                \Log::error('API token-info: Token không hợp lệ (JWTAuth::check() trả về false)');
                return response()->json(['valid' => false, 'error' => 'Token không hợp lệ'], 200);
            }

            // Lấy payload từ token
            $payload = JWTAuth::getPayload()->toArray();
            \Log::info('API token-info: Token hợp lệ, payload: ' . json_encode($payload));

            return response()->json([
                'valid' => true,
                'payload' => $payload,
                'expires_at' => date('Y-m-d H:i:s', $payload['exp'])
            ], 200);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            \Log::error('API token-info: Token đã hết hạn');
            return response()->json(['valid' => false, 'error' => 'Token đã hết hạn'], 200);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            \Log::error('API token-info: Token không hợp lệ: ' . $e->getMessage());
            return response()->json(['valid' => false, 'error' => 'Token không hợp lệ'], 200);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            \Log::error('API token-info: Lỗi JWT: ' . $e->getMessage());
            return response()->json(['valid' => false, 'error' => 'Lỗi xử lý token: ' . $e->getMessage()], 200);
        }
    } catch (\Exception $e) {
        \Log::error('API token-info: Lỗi: ' . $e->getMessage());
        return response()->json(['valid' => false, 'error' => $e->getMessage()], 200);
    }
});

// Protected routes for all authenticated users
Route::middleware('jwt.verify')->group(function () {
    Route::get('tasks', [TaskController::class, 'index']);
    Route::post('tasks', [TaskController::class, 'store']);
    Route::get('tasks/{task}', [TaskController::class, 'show']);
    Route::put('tasks/{task}', [TaskController::class, 'update']);
    Route::delete('tasks/{task}', [TaskController::class, 'destroy']);

    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);  // Thêm route để refresh token
    Route::get('user', [AuthController::class, 'me']);  // Lấy thông tin user hiện tại

    // Route kiểm tra token
    Route::get('check-token', function () {
        return response()->json(['status' => 'Token is valid', 'user' => auth()->user()]);
    });
});

// API routes for managers and admins
Route::prefix('manager')->group(function () {
    Route::get('users', function () {
        try {
            // Lấy token từ request
            $token = request()->bearerToken();
            if (!$token) {
                return response()->json(['success' => false, 'error' => 'Token is absent'], 401);
            }

            // Thiết lập token cho JWTAuth
            JWTAuth::setToken($token);
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user || ($user->role !== 'manager' && $user->role !== 'admin')) {
                return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
            }

            $users = \App\Models\User::where('role', '!=', 'admin')->get();
            return response()->json(['success' => true, 'users' => $users]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 401);
        }
    });

    Route::get('tasks/all', function () {
        try {
            // Lấy token từ request
            $token = request()->bearerToken();
            if (!$token) {
                return response()->json(['success' => false, 'error' => 'Token is absent'], 401);
            }

            // Thiết lập token cho JWTAuth
            JWTAuth::setToken($token);
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user || ($user->role !== 'manager' && $user->role !== 'admin')) {
                return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
            }

            $tasks = \App\Models\Task::with('creator')->get();
            return response()->json(['success' => true, 'tasks' => $tasks]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 401);
        }
    });

    Route::get('reports/summary', function () {
        try {
            // Lấy token từ request
            $token = request()->bearerToken();
            if (!$token) {
                return response()->json(['success' => false, 'error' => 'Token is absent'], 401);
            }

            // Thiết lập token cho JWTAuth
            JWTAuth::setToken($token);
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user || ($user->role !== 'manager' && $user->role !== 'admin')) {
                return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
            }

            $totalTasks = \App\Models\Task::count();
            $completedTasks = \App\Models\Task::where('status', 'completed')->count();
            $pendingTasks = \App\Models\Task::where('status', 'pending')->count();
            $inProgressTasks = \App\Models\Task::where('status', 'in_progress')->count();

            return response()->json([
                'success' => true,
                'summary' => [
                    'total' => $totalTasks,
                    'completed' => $completedTasks,
                    'pending' => $pendingTasks,
                    'in_progress' => $inProgressTasks,
                    'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 401);
        }
    });

    // Manager task operations
    Route::post('tasks', function (Request $request) {
        try {
            // Lấy token từ request
            $token = request()->bearerToken();
            if (!$token) {
                return response()->json(['success' => false, 'error' => 'Token is absent'], 401);
            }

            // Thiết lập token cho JWTAuth
            JWTAuth::setToken($token);
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user || ($user->role !== 'manager' && $user->role !== 'admin')) {
                return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
            }

            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_date' => 'required|date',
                'due_date' => 'required|date',
                'priority' => 'required|in:low,medium,high',
                'status' => 'required|in:pending,in_progress,completed',
                'user_id' => 'required|exists:users,id',
            ]);

            // Kiểm tra và điều chỉnh ngày nếu due_date < start_date
            $dateMessage = \App\Models\Task::validateAndAdjustDates($validatedData);

            $task = \App\Models\Task::create([
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'start_date' => $validatedData['start_date'],
                'due_date' => $validatedData['due_date'],
                'status' => $validatedData['status'],
                'priority' => $validatedData['priority'],
                'creator_id' => $validatedData['user_id'],
            ]);

            $response = ['success' => true, 'task' => $task];
            if ($dateMessage) {
                $response['warning'] = $dateMessage;
            }

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    });

    Route::put('tasks/{id}', function (Request $request, $id) {
        try {
            // Lấy token từ request
            $token = request()->bearerToken();
            if (!$token) {
                return response()->json(['success' => false, 'error' => 'Token is absent'], 401);
            }

            // Thiết lập token cho JWTAuth
            JWTAuth::setToken($token);
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user || ($user->role !== 'manager' && $user->role !== 'admin')) {
                return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
            }

            $task = \App\Models\Task::findOrFail($id);

            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_date' => 'required|date',
                'due_date' => 'required|date',
                'priority' => 'required|in:low,medium,high',
                'status' => 'required|in:pending,in_progress,completed',
                'user_id' => 'required|exists:users,id',
            ]);

            // Kiểm tra và điều chỉnh ngày nếu due_date < start_date
            $dateMessage = \App\Models\Task::validateAndAdjustDates($validatedData);

            $task->update([
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'start_date' => $validatedData['start_date'],
                'due_date' => $validatedData['due_date'],
                'status' => $validatedData['status'],
                'priority' => $validatedData['priority'],
                'creator_id' => $validatedData['user_id'],
            ]);

            $response = ['success' => true, 'task' => $task];
            if ($dateMessage) {
                $response['warning'] = $dateMessage;
            }

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    });

    Route::delete('tasks/{id}', function (Request $request, $id) {
        try {
            // Lấy token từ request
            $token = request()->bearerToken();
            if (!$token) {
                return response()->json(['success' => false, 'error' => 'Token is absent'], 401);
            }

            // Thiết lập token cho JWTAuth
            JWTAuth::setToken($token);
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user || ($user->role !== 'manager' && $user->role !== 'admin')) {
                return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
            }

            $task = \App\Models\Task::findOrFail($id);
            $task->delete();

            return response()->json(['success' => true, 'message' => 'Task deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    });
});

// API routes for admins only
Route::prefix('admin')->group(function () {
    Route::get('users', function () {
        $users = \App\Models\User::all();
        return response()->json(['success' => true, 'users' => $users]);
    });

    Route::post('users', function (Request $request) {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,manager,user'
        ]);

        $user = new \App\Models\User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = bcrypt($data['password']);
        $user->role = $data['role'];
        $user->save();

        return response()->json(['success' => true, 'user' => $user, 'message' => 'Người dùng đã được tạo thành công']);
    });

    Route::put('users/{id}', function (Request $request, $id) {
        $user = \App\Models\User::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'role' => 'sometimes|in:admin,manager,user'
        ]);

        if ($request->has('password') && !empty($request->password)) {
            $data['password'] = bcrypt($request->password);
        }

        // Không cho phép cập nhật email vì lý do bảo mật
        unset($data['email']);

        $user->update($data);

        return response()->json(['success' => true, 'user' => $user]);
    });

    Route::delete('users/{id}', function ($id) {
        $user = \App\Models\User::findOrFail($id);
        $user->delete();

        return response()->json(['success' => true, 'message' => 'User deleted successfully']);
    });

    Route::get('reports', function () {
        // Detailed reports for admin
        $userStats = \App\Models\User::withCount('tasks')->get()->groupBy('role')->map(function ($users) {
            return [
                'count' => $users->count(),
                'tasks_count' => $users->sum('tasks_count')
            ];
        });

        $taskStats = \App\Models\Task::select('status', \DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        $priorityStats = \App\Models\Task::select('priority', \DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->get()
            ->pluck('count', 'priority');

        return response()->json([
            'success' => true,
            'reports' => [
                'users' => $userStats,
                'tasks_by_status' => $taskStats,
                'tasks_by_priority' => $priorityStats
            ]
        ]);
    });
});
