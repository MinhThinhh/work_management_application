@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Thêm công việc mới</h1>
        <a href="{{ route('dashboard') }}" class="text-blue-500 hover:underline">
            &larr; Quay lại danh sách
        </a>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form action="{{ route('tasks.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="title">Tiêu đề</label>
                <input
                    type="text"
                    name="title"
                    id="title"
                    value="{{ old('title') }}"
                    class="w-full border border-gray-300 rounded px-3 py-2 @error('title') border-red-500 @enderror"
                    required
                />
                @error('title')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="description">Mô tả</label>
                <textarea
                    name="description"
                    id="description"
                    class="w-full border border-gray-300 rounded px-3 py-2 @error('description') border-red-500 @enderror"
                    rows="3"
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="due_date">Ngày hết hạn</label>
                <input
                    type="date"
                    name="due_date"
                    id="due_date"
                    value="{{ old('due_date') }}"
                    class="w-full border border-gray-300 rounded px-3 py-2 @error('due_date') border-red-500 @enderror"
                    required
                />
                @error('due_date')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="priority">Mức độ ưu tiên</label>
                <select
                    name="priority"
                    id="priority"
                    class="w-full border border-gray-300 rounded px-3 py-2 @error('priority') border-red-500 @enderror"
                >
                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Thấp</option>
                    <option value="medium" {{ old('priority') == 'medium' || !old('priority') ? 'selected' : '' }}>Trung bình</option>
                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Cao</option>
                </select>
                @error('priority')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex space-x-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Lưu
                </button>
                <a href="{{ route('dashboard') }}" class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded">
                    Hủy
                </a>
            </div>
        </form>
    </div>
</div>
@endsection 