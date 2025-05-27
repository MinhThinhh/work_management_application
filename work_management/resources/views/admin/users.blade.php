@extends('admin.layout')

@section('title', 'Quản lý người dùng - Admin')

@section('header', 'Quản lý người dùng')

@section('content')
<div class="card">
    <div class="mb-4 flex justify-between items-center">
        <h2 class="text-xl font-semibold">Danh sách người dùng</h2>
        <a href="{{ route('admin.create-user') }}" class="btn btn-sm btn-success">
            <i class="fas fa-user-plus"></i> Tạo người dùng mới
        </a>
    </div>

    <div class="overflow-x-auto">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Vai trò</th>
                    <th>Ngày tạo</th>
                    <th>Số công việc</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if($user->role == 'admin')
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Admin</span>
                        @elseif($user->role == 'manager')
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Manager</span>
                        @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">User</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                    <td>{{ $user->tasks()->count() }}</td>
                    <td class="flex space-x-2">
                        <a href="{{ route('admin.edit-user', $user->id) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i> Sửa
                        </a>
                        @if($user->id !== Auth::id())
                            <form action="{{ route('admin.delete-user', $user->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này? Tất cả công việc của họ cũng sẽ bị xóa.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4">Không có người dùng nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
