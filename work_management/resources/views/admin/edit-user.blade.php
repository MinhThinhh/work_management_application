@extends('admin.layout')

@section('title', 'Chỉnh sửa người dùng - Admin')

@section('header', 'Chỉnh sửa người dùng')

@section('content')
<div class="card">
    <form action="{{ route('admin.update-user', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name" class="block mb-2">Họ và tên</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            @error('name')
            <div class="text-red-500 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="email" class="block mb-2">Email</label>
            <input type="email" name="email" id="email" class="form-control bg-gray-100 cursor-not-allowed" value="{{ $user->email }}" readonly disabled>
            <small class="text-gray-600 mt-1"><i class="fas fa-lock mr-1"></i>Email không thể thay đổi vì lý do bảo mật.</small>
        </div>

        <div class="form-group">
            <label for="password" class="block mb-2">Mật khẩu mới (để trống nếu không thay đổi)</label>
            <input type="password" name="password" id="password" class="form-control">
            @error('password')
            <div class="text-red-500 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="block mb-2">Xác nhận mật khẩu mới</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
        </div>

        <div class="form-group">
            <label for="role" class="block mb-2">Vai trò</label>
            <select name="role" id="role" class="form-control" required>
                <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User</option>
                <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>Manager</option>
                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
            @error('role')
            <div class="text-red-500 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mt-6 flex space-x-4">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Cập nhật người dùng
            </button>
            <a href="{{ route('admin.users') }}" class="btn btn-danger">
                <i class="fas fa-times"></i> Hủy
            </a>
        </div>
    </form>
</div>
@endsection