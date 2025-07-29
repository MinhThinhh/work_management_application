<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KpiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->role !== 'admin') {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $kpiData = $this->calculateKpiData();
        $teamPerformance = $this->getTeamPerformance();
        $topPerformers = $this->getTopPerformers();
        $statusStats = $this->getStatusStats();
        $priorityStats = $this->getPriorityStats();

        return view('admin.kpi', compact(
            'kpiData', 
            'teamPerformance', 
            'topPerformers', 
            'statusStats', 
            'priorityStats'
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
        $teams = Team::with(['tasks' => function($query) {
            $query->select('team_id', 'status');
        }])->get();

        $labels = [];
        $data = [];

        foreach ($teams as $team) {
            $totalTasks = $team->tasks->count();
            $completedTasks = $team->tasks->where('status', 'completed')->count();
            $completionRate = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;

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
                    'score' => round($completionRate, 1)
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
}
