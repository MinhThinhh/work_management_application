<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\AdminController;

// Authentication Routes
Route::get('/', [AuthController::class, 'showLoginForm'])->name('home');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout.post');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Protected Routes (require JWT authentication)
Route::middleware(\App\Http\Middleware\WebAuthenticate::class)->group(function () {
    // Dashboard
    Route::get('/dashboard', [TaskController::class, 'dashboard'])->name('dashboard');

    // Task Routes
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    // User Profile Routes
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('user.change-password');
    Route::put('/update-profile', [AuthController::class, 'updateProfile'])->name('user.update-profile');
});

// Protected API Routes (require JWT authentication)
Route::middleware([\App\Http\Middleware\WebAuthenticate::class])->group(function () {
    // Calendar API Routes
    Route::get('/api/events', [TaskController::class, 'getEvents'])->name('events.all');
    Route::get('/api/events/day', [TaskController::class, 'getDayEvents'])->name('events.day');
    Route::get('/api/events/month', [TaskController::class, 'getMonthEvents'])->name('events.month');

    // API Routes (for compatibility with existing code)
    Route::get('/tasks', [TaskController::class, 'index']);

    // Vanilla Calendar API Routes
    Route::get('/api/calendar/events', [TaskController::class, 'getCalendarEvents'])->name('calendar.events');
    Route::post('/api/calendar/events', [TaskController::class, 'store'])->name('calendar.events.store');
    Route::get('/api/calendar/events/{id}', [TaskController::class, 'show'])->name('calendar.events.show');
    Route::put('/api/calendar/events/{id}', [TaskController::class, 'update'])->name('calendar.events.update');
    Route::delete('/api/calendar/events/{id}', [TaskController::class, 'destroy'])->name('calendar.events.destroy');
});

// Manager Routes - Chỉ quản lý công việc (require JWT authentication)
Route::middleware([\App\Http\Middleware\WebAuthenticate::class])->prefix('manager')->name('manager.')->group(function () {
    // Chuyển hướng dashboard đến tasks
    Route::get('/dashboard', function() {
        return redirect()->route('manager.all-tasks');
    })->name('dashboard');
    Route::get('/users', [ManagerController::class, 'users'])->name('users'); // Chuyển hướng về dashboard với thông báo
    Route::get('/tasks', [ManagerController::class, 'allTasks'])->name('all-tasks');
    Route::get('/tasks/create', [ManagerController::class, 'createTaskForm'])->name('create-task');
    Route::post('/tasks', [ManagerController::class, 'storeTask'])->name('store-task');
    Route::get('/tasks/{id}/edit', [ManagerController::class, 'editTask'])->name('edit-task');
    Route::put('/tasks/{id}', [ManagerController::class, 'updateTask'])->name('update-task');
    Route::delete('/tasks/{id}', [ManagerController::class, 'deleteTask'])->name('delete-task');
    Route::get('/reports', [ManagerController::class, 'reports'])->name('reports');
});

// Admin Routes - Chỉ quản lý người dùng (require JWT authentication)
Route::middleware([\App\Http\Middleware\WebAuthenticate::class])->prefix('admin')->name('admin.')->group(function () {
    // Chuyển hướng dashboard đến users
    Route::get('/dashboard', function() {
        return redirect()->route('admin.users');
    })->name('dashboard');

    // User Management
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminController::class, 'createUserForm'])->name('create-user');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('store-user');
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('edit-user');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('update-user');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('delete-user');

    // Admin không quản lý công việc - chuyển hướng về users
    Route::get('/tasks', function() {
        return redirect()->route('admin.users');
    })->name('all-tasks');

    // Chuyển hướng reports đến users
    Route::get('/reports', function() {
        return redirect()->route('admin.users');
    })->name('reports');

    // Access to Manager Dashboard
    Route::get('/manager-dashboard', function() {
        return redirect()->route('manager.all-tasks');
    })->name('manager-dashboard');
});

// Public API Routes - session verify requires JWT authentication
Route::get('/api/auth/session', [AuthController::class, 'me'])->middleware('jwt.verify')->name('session.verify');

// Health check endpoint for desktop app
Route::get('/health-check', function() {
    return response()->json([
        'status' => 'ok',
        'message' => 'API is running',
        'timestamp' => now()->toIso8601String()
    ]);
});