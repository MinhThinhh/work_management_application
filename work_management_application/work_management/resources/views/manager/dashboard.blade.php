@extends('manager.layout')

@section('title', 'Dashboard - Manager')

@section('header', 'Dashboard')



@section('content')


<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
    <div class="stat-card">
        <div class="icon">
            <i class="fas fa-tasks"></i>
        </div>
        <div class="stat-label">Quản lý công việc</div>
        <div class="stat-number">
            <i class="fas fa-clipboard-list" style="font-size: 32px;"></i>
        </div>
        <p class="mb-4 text-gray-600">Xem và quản lý tất cả công việc của người dùng.</p>
    </div>

    <div class="stat-card">
        <div class="icon">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-label">Báo cáo & Thống kê</div>
        <div class="stat-number">
            <i class="fas fa-chart-pie" style="font-size: 32px;"></i>
        </div>
        <p class="mb-4 text-gray-600">Xem báo cáo và thống kê về công việc trong hệ thống.</p>
        <div class="mt-2">
            <a href="{{ route('manager.reports') }}" class="btn btn-primary w-full">
                <i class="fas fa-chart-bar"></i> Xem báo cáo
            </a>
        </div>
    </div>
</div>

<div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-bolt mr-2"></i>Tác vụ nhanh
            </h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('manager.create-task') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Tạo công việc mới
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-info-circle mr-2"></i>Trạng thái hệ thống
            </h2>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div class="p-4 bg-blue-50 rounded-lg">
                <div class="text-blue-600 font-semibold">Thời gian hiện tại</div>
                <div class="text-gray-700">{{ now()->format('d/m/Y H:i') }}</div>
            </div>
            <div class="p-4 bg-green-50 rounded-lg">
                <div class="text-green-600 font-semibold">Trạng thái</div>
                <div class="text-gray-700">
                    <span class="inline-block w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                    Hoạt động
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-8">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-calendar-check mr-2"></i>Công việc gần đây
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr>
                        <th>Tiêu đề</th>
                        <th>Người thực hiện</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày kết thúc</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTasks as $task)
                    <tr>
                        <td>{{ $task->title }}</td>
                        <td>
                            <div class="flex items-center">
                                <div class="user-avatar mr-2">
                                    {{ $task->creator ? substr($task->creator->email, 0, 2) : 'NA' }}
                                </div>
                                <span>{{ $task->creator ? $task->creator->email : 'Không có' }}</span>
                            </div>
                        </td>
                        <td>{{ $task->start_date ? date('d/m/Y', strtotime($task->start_date)) : 'Chưa đặt' }}</td>
                        <td>{{ $task->due_date ? date('d/m/Y', strtotime($task->due_date)) : 'Chưa đặt' }}</td>
                        <td>
                            @if($task->status == 'pending')
                            <span class="badge badge-warning">Chưa hoàn thành</span>
                            @elseif($task->status == 'in_progress')
                            <span class="badge badge-info">Đang thực hiện</span>
                            @elseif($task->status == 'completed')
                            <span class="badge badge-success">Hoàn thành</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('manager.edit-task', $task->id) }}" class="btn-sm btn-primary flex items-center justify-center" title="Sửa công việc">
                                    <i class="fas fa-edit mr-1"></i> Sửa
                                </a>
                                <form action="{{ route('manager.delete-task', $task->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa công việc này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-sm btn-danger flex items-center justify-center" title="Xóa công việc">
                                        <i class="fas fa-trash-alt mr-1"></i> Xóa
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">Không có công việc nào gần đây</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 text-center">
            <a href="{{ route('manager.all-tasks') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                Xem tất cả công việc <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
</div>
@endsection