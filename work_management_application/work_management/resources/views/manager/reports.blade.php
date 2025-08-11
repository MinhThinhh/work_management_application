@extends('manager.layout')

@section('title', 'Báo cáo & Thống kê - Manager')

@section('header', 'Báo cáo & Thống kê')

@section('styles')
<style>
    .stat-card {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 20px;
        text-align: center;
    }
    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        margin: 10px 0;
    }
    .stat-label {
        color: #6b7280;
        font-size: 0.875rem;
    }
    .progress-bar {
        height: 8px;
        border-radius: 4px;
        background-color: #e5e7eb;
        margin-top: 10px;
        overflow: hidden;
    }
    .progress-value {
        height: 100%;
        border-radius: 4px;
    }
</style>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="stat-card">
        <div class="stat-label">Tổng số công việc</div>
        <div class="stat-number">{{ $totalTasks }}</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-label">Chờ xử lý</div>
        <div class="stat-number">{{ $pendingTasks }}</div>
        <div class="progress-bar">
            <div class="progress-value bg-yellow-500" style="width: {{ $totalTasks > 0 ? ($pendingTasks / $totalTasks * 100) : 0 }}%"></div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-label">Đang thực hiện</div>
        <div class="stat-number">{{ $inProgressTasks }}</div>
        <div class="progress-bar">
            <div class="progress-value bg-blue-500" style="width: {{ $totalTasks > 0 ? ($inProgressTasks / $totalTasks * 100) : 0 }}%"></div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-label">Hoàn thành</div>
        <div class="stat-number">{{ $completedTasks }}</div>
        <div class="progress-bar">
            <div class="progress-value bg-green-500" style="width: {{ $totalTasks > 0 ? ($completedTasks / $totalTasks * 100) : 0 }}%"></div>
        </div>
    </div>
</div>

<div class="card">
    <h2 class="text-xl font-semibold mb-4">Thống kê theo người dùng</h2>
    
    <div class="overflow-x-auto">
        <table>
            <thead>
                <tr>
                    <th>Người dùng</th>
                    <th>Tổng số công việc</th>
                    <th>Chờ xử lý</th>
                    <th>Đang thực hiện</th>
                    <th>Hoàn thành</th>
                    <th>Tỷ lệ hoàn thành</th>
                </tr>
            </thead>
            <tbody>
                @forelse($userStats as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->total_tasks }}</td>
                    <td>{{ $user->pending_tasks }}</td>
                    <td>{{ $user->in_progress_tasks }}</td>
                    <td>{{ $user->completed_tasks }}</td>
                    <td>
                        @if($user->total_tasks > 0)
                            {{ round(($user->completed_tasks / $user->total_tasks) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4">Không có dữ liệu.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
