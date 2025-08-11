<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class KpiController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware(function ($request, $next) {
                if (Auth::user()->role !== 'admin') {
                    abort(403, 'Unauthorized');
                }
                return $next($request);
            })
        ];
    }

    public function index()
    {
        $kpiData = $this->calculateKpiData();
        $teamPerformance = $this->getTeamPerformance();
        $topPerformers = $this->getTopPerformers();
        $statusStats = $this->getStatusStats();
        $priorityStats = $this->getPriorityStats();
        $userStats = $this->getUserStats();
        $monthlyTrends = $this->getMonthlyTrends();
        $teamDetails = $this->getTeamDetails();

        return view('admin.kpi', compact(
            'kpiData',
            'teamPerformance',
            'topPerformers',
            'statusStats',
            'priorityStats',
            'userStats',
            'monthlyTrends',
            'teamDetails'
        ));
    }

    private function calculateKpiData()
    {
        $totalTasks = Task::count();
        $completedTasks = Task::where('status', 'completed')->count();
        $onTimeTasks = Task::where('status', 'completed')
                          ->whereRaw('DATE(updated_at) <= due_date')
                          ->count();

        $completionRate = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;
        $onTimeRate = $completedTasks > 0 ? ($onTimeTasks / $completedTasks) * 100 : 0;
        
        // Mock quality score - in real app, this would be calculated from actual quality metrics
        $qualityScore = 4.2;
        
        // Count top performers (users with >90% completion rate)
        $topPerformers = User::where('role', 'user')
            ->withCount(['assignedTasks', 'assignedTasks as completed_tasks_count' => function($query) {
                $query->where('status', 'completed');
            }])
            ->get()
            ->filter(function($user) {
                if ($user->assigned_tasks_count == 0) return false;
                $rate = ($user->completed_tasks_count / $user->assigned_tasks_count) * 100;
                return $rate >= 90;
            })
            ->count();

        return [
            'completion_rate' => $completionRate,
            'quality_score' => $qualityScore,
            'on_time_rate' => $onTimeRate,
            'top_performers' => $topPerformers
        ];
    }

    private function getTeamPerformance()
    {
        $teams = Team::with(['members'])->get();

        $labels = [];
        $data = [];

        foreach ($teams as $team) {
            $stats = $team->getStats();
            $completionRate = $stats['completion_rate'];

            $labels[] = $team->name;
            $data[] = round($completionRate, 1);
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getTopPerformers()
    {
        return User::where('role', 'user')
            ->with('team')
            ->withCount(['assignedTasks', 'assignedTasks as completed_tasks_count' => function($query) {
                $query->where('status', 'completed');
            }])
            ->get()
            ->map(function($user) {
                $completionRate = $user->assigned_tasks_count > 0
                    ? ($user->completed_tasks_count / $user->assigned_tasks_count) * 100
                    : 0;

                return [
                    'name' => $user->name,
                    'score' => round($completionRate, 1),
                    'team' => $user->team ? $user->team->name : 'Chưa có team'
                ];
            })
            ->sortByDesc('score')
            ->take(5)
            ->values();
    }

    private function getStatusStats()
    {
        $stats = Task::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $labels = [];
        $data = [];

        foreach ($stats as $stat) {
            switch ($stat->status) {
                case 'pending':
                    $labels[] = 'Chờ xử lý';
                    break;
                case 'in_progress':
                    $labels[] = 'Đang thực hiện';
                    break;
                case 'completed':
                    $labels[] = 'Hoàn thành';
                    break;
                default:
                    $labels[] = ucfirst($stat->status);
            }
            $data[] = $stat->count;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getPriorityStats()
    {
        $stats = Task::select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->get();

        $labels = [];
        $data = [];

        foreach ($stats as $stat) {
            switch ($stat->priority) {
                case 'low':
                    $labels[] = 'Thấp';
                    break;
                case 'medium':
                    $labels[] = 'Trung bình';
                    break;
                case 'high':
                    $labels[] = 'Cao';
                    break;
                default:
                    $labels[] = ucfirst($stat->priority);
            }
            $data[] = $stat->count;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getUserStats()
    {
        $totalUsers = User::count();
        $activeUsers = User::whereHas('assignedTasks', function($query) {
            $query->where('created_at', '>=', now()->subDays(30));
        })->count();

        $usersByRole = User::select('role', DB::raw('count(*) as count'))
            ->groupBy('role')
            ->get();

        $roleLabels = [];
        $roleData = [];

        foreach ($usersByRole as $role) {
            switch ($role->role) {
                case 'admin':
                    $roleLabels[] = 'Quản trị viên';
                    break;
                case 'manager':
                    $roleLabels[] = 'Quản lý';
                    break;
                case 'user':
                    $roleLabels[] = 'Nhân viên';
                    break;
                default:
                    $roleLabels[] = ucfirst($role->role);
            }
            $roleData[] = $role->count;
        }

        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'role_labels' => $roleLabels,
            'role_data' => $roleData
        ];
    }

    private function getMonthlyTrends()
    {
        $months = [];
        $tasksCreated = [];
        $tasksCompleted = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');

            $created = Task::whereYear('created_at', $date->year)
                          ->whereMonth('created_at', $date->month)
                          ->count();

            $completed = Task::where('status', 'completed')
                           ->whereYear('updated_at', $date->year)
                           ->whereMonth('updated_at', $date->month)
                           ->count();

            $tasksCreated[] = $created;
            $tasksCompleted[] = $completed;
        }

        return [
            'months' => $months,
            'tasks_created' => $tasksCreated,
            'tasks_completed' => $tasksCompleted
        ];
    }

    private function getTeamDetails()
    {
        $teams = Team::with(['leader', 'members'])->get();

        $teamDetails = [];

        foreach ($teams as $team) {
            $stats = $team->getStats();
            $members = $team->members->pluck('name')->toArray();

            $teamDetails[] = [
                'name' => $team->name,
                'leader' => $team->leader ? $team->leader->name : 'Chưa có leader',
                'total_members' => $stats['total_members'],
                'total_tasks' => $stats['total_tasks'],
                'completed_tasks' => $stats['completed_tasks'],
                'in_progress_tasks' => $stats['in_progress_tasks'],
                'pending_tasks' => $stats['pending_tasks'],
                'completion_rate' => $stats['completion_rate'],
                'members' => $members
            ];
        }

        return $teamDetails;
    }
}
