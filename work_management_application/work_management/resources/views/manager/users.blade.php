@extends('manager.layout')

@section('title', 'Quản lý người dùng - Manager')

@section('header', 'Quản lý người dùng')

@section('content')
<div class="card">
    <div class="mb-4 flex justify-between items-center">
        <h2 class="text-xl font-semibold">Danh sách người dùng</h2>
    </div>

    <div class="overflow-x-auto">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Ngày tạo</th>
                    <th>Số công việc được giao</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                    <td>{{ $user->assignedTasks()->count() }}</td>
                    <td>
                        <a href="{{ route('manager.create-task', ['user_id' => $user->id]) }}" class="text-blue-600 hover:underline">Tạo công việc</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4">Không có người dùng nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
