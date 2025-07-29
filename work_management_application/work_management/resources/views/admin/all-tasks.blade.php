@extends('admin.layout')

@section('title', 'Xem công việc - Admin')

@section('header', 'Xem danh sách công việc')

@section('content')
<div class="card">
    <div class="mb-4 flex justify-between items-center">
        <h2 class="text-xl font-semibold">Danh sách công việc (Chỉ xem)</h2>
        <div class="text-sm text-gray-500">
            <i class="fas fa-info-circle mr-1"></i> Admin chỉ có thể xem danh sách công việc, không thể thêm/sửa/xóa
        </div>
    </div>

    <div class="overflow-x-auto">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tiêu đề</th>
                    <th>Người dùng</th>
                    <th>Ngày bắt đầu</th>
                    <th>Ngày kết thúc</th>
                    <th>Trạng thái</th>
                    <th>Mức độ ưu tiên</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $task)
                <tr>
                    <td>{{ $task->id }}</td>
                    <td>{{ $task->title }}</td>
                    <td>{{ $task->creator->email }}</td>
                    <td>{{ $task->start_date ? date('d/m/Y', strtotime($task->start_date)) : 'N/A' }}</td>
                    <td>{{ $task->due_date ? date('d/m/Y', strtotime($task->due_date)) : 'N/A' }}</td>
                    <td>
                        @if($task->status == 'pending')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Chờ xử lý</span>
                        @elseif($task->status == 'in_progress')
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Đang thực hiện</span>
                        @elseif($task->status == 'completed')
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Hoàn thành</span>
                        @endif
                    </td>
                    <td>
                        @if($task->priority == 'low')
                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Thấp</span>
                        @elseif($task->priority == 'medium')
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Trung bình</span>
                        @elseif($task->priority == 'high')
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Cao</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4">Không có công việc nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
