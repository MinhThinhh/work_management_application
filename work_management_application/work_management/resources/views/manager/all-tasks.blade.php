@extends('manager.layout')

@section('title', 'Quản lý công việc - Manager')

@section('header', 'Quản lý công việc')



@section('content')
<div class="card">
    <div class="mb-4 flex justify-between items-center">
        <h2 class="text-xl font-semibold">Danh sách công việc</h2>
        <a href="{{ route('manager.create-task') }}" class="btn btn-sm btn-success">
            <i class="fas fa-plus"></i> Tạo công việc mới
        </a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

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
                    <th>Thao tác</th>
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
                    <td>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('manager.edit-task', $task->id) }}" class="btn-sm btn-primary flex items-center justify-center" title="Sửa công việc">
                                <i class="fas fa-edit mr-1"></i> Sửa
                            </a>
                            <form action="{{ route('manager.delete-task', $task->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa công việc này?')">
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
                    <td colspan="8" class="text-center py-4">Không có công việc nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection