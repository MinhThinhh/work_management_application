@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Form ẩn để lưu dữ liệu task -->
    <form style="display: none;" data-task-id="{{ $task->id }}" action="{{ route('tasks.update', $task->id) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="text" id="title" name="title" value="{{ old('title', $task->title) }}">
        <textarea id="description" name="description">{{ old('description', $task->description) }}</textarea>
        <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $task->start_date) }}">
        <input type="date" id="due_date" name="due_date" value="{{ old('due_date', $task->due_date) }}">
        <select id="priority" name="priority">
            <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>Thấp</option>
            <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>Trung bình</option>
            <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>Cao</option>
        </select>
        <select id="status" name="status">
            <option value="pending" {{ old('status', $task->status) == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
            <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>Đang thực hiện</option>
            <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
        </select>
    </form>
</div>

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endsection

@section('scripts')
    <script src="{{ asset('js/edit-task.js') }}"></script>
@endsection
@endsection