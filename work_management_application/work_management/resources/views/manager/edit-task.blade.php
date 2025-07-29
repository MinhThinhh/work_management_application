@extends('manager.layout')

@section('title', 'Chỉnh sửa công việc - Manager')

@section('header', 'Chỉnh sửa công việc')

@section('content')
<div class="card">
    <form action="{{ route('manager.update-task', $task->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="user_id" class="block mb-2">Người dùng</label>
            <select name="user_id" id="user_id" class="form-control" required>
                <option value="">-- Chọn người dùng --</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $task->creator_id == $user->id ? 'selected' : '' }}>
                        {{ $user->email }}
                    </option>
                @endforeach
            </select>
            @error('user_id')
                <div class="text-red-500 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="title" class="block mb-2">Tiêu đề</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $task->title) }}" required>
            @error('title')
                <div class="text-red-500 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="description" class="block mb-2">Mô tả</label>
            <textarea name="description" id="description" class="form-control" rows="4">{{ old('description', $task->description) }}</textarea>
            @error('description')
                <div class="text-red-500 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-group">
                <label for="start_date" class="block mb-2">Ngày bắt đầu</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', $task->start_date) }}">
                @error('start_date')
                    <div class="text-red-500 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="due_date" class="block mb-2">Ngày kết thúc</label>
                <input type="date" name="due_date" id="due_date" class="form-control" value="{{ old('due_date', $task->due_date) }}">
                @error('due_date')
                    <div class="text-red-500 mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-group">
                <label for="status" class="block mb-2">Trạng thái</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="pending" {{ old('status', $task->status) == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                    <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>Đang thực hiện</option>
                    <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                </select>
                @error('status')
                    <div class="text-red-500 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="priority" class="block mb-2">Mức độ ưu tiên</label>
                <select name="priority" id="priority" class="form-control" required>
                    <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>Thấp</option>
                    <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>Trung bình</option>
                    <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>Cao</option>
                </select>
                @error('priority')
                    <div class="text-red-500 mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mt-6 flex space-x-4">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Cập nhật công việc
            </button>
            <a href="{{ route('manager.all-tasks') }}" class="btn btn-danger">
                <i class="fas fa-times"></i> Hủy
            </a>
        </div>
    </form>
</div>
@endsection
