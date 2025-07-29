@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Chi tiết công việc</h1>
        <a href="{{ route('dashboard') }}" class="text-blue-500 hover:underline">
            &larr; Quay lại danh sách
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold mb-4">{{ $task->title }}</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-gray-700 font-semibold">Trạng thái:</p>
                    <span class="px-2 py-1 rounded task-status-{{ $task->status }}">
                        {{ $task->status == 'completed' ? 'Hoàn thành' : ($task->status == 'in_progress' ? 'Đang thực hiện' : 'Chờ xử lý') }}
                    </span>
                </div>
                
                <div>
                    <p class="text-gray-700 font-semibold">Mức độ ưu tiên:</p>
                    <span class="px-2 py-1 rounded text-white task-priority-{{ $task->priority }}">
                        {{ $task->priority == 'high' ? 'Cao' : ($task->priority == 'medium' ? 'Trung bình' : 'Thấp') }}
                    </span>
                </div>
                
                <div>
                    <p class="text-gray-700 font-semibold">Ngày hết hạn:</p>
                    <p>{{ $task->due_date }}</p>
                </div>
                
                <div>
                    <p class="text-gray-700 font-semibold">Ngày tạo:</p>
                    <p>{{ $task->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            
            <div class="mb-6">
                <p class="text-gray-700 font-semibold">Mô tả:</p>
                <p class="mt-2">{{ $task->description ?: 'Không có mô tả' }}</p>
            </div>
            
            <div class="flex space-x-4">
                <a href="{{ route('tasks.edit', $task->id) }}" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Chỉnh sửa
                </a>
                <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa công việc này?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white px-4 py-2 rounded">
                        Xóa
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 