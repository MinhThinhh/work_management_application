@extends('admin.layout')

@section('title', 'Tạo người dùng mới - Admin')

@section('header', 'Tạo người dùng mới')

@section('content')
<div class="card">
    <form action="{{ route('admin.store-user') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="email" class="block mb-2">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
            @error('email')
                <div class="text-red-500 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="block mb-2">Mật khẩu</label>
            <input type="password" name="password" id="password" class="form-control" required>
            @error('password')
                <div class="text-red-500 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="block mb-2">Xác nhận mật khẩu</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="role" class="block mb-2">Vai trò</label>
            <select name="role" id="role" class="form-control" required>
                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
            @error('role')
                <div class="text-red-500 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mt-6 flex space-x-4">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-user-plus"></i> Tạo người dùng
            </button>
            <a href="{{ route('admin.users') }}" class="btn btn-danger">
                <i class="fas fa-times"></i> Hủy
            </a>
        </div>
    </form>
</div>
@endsection
