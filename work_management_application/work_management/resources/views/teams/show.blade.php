@extends('layouts.app')

@section('title', 'Chi tiết Team: ' . $team->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1>{{ $team->name }}</h1>
                    <p class="text-muted">{{ $team->description }}</p>
                </div>
                <div>
                    <a href="{{ route('teams.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <!-- Team Info -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3>Thông tin Team</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Manager:</strong><br>
                                {{ $team->manager->email ?? 'N/A' }}
                            </div>
                            <div class="mb-3">
                                <strong>Trạng thái:</strong><br>
                                <span class="badge bg-{{ $team->is_active ? 'success' : 'secondary' }}">
                                    {{ $team->is_active ? 'Hoạt động' : 'Không hoạt động' }}
                                </span>
                            </div>
                            <div class="mb-3">
                                <strong>Số thành viên:</strong><br>
                                {{ $team->activeMembers->count() }}
                            </div>
                            <div class="mb-3">
                                <strong>Số công việc:</strong><br>
                                {{ $team->tasks->count() }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Team Members -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3>Thành viên Team</h3>
                            @if(auth()->user()->role === 'admin' || $team->manager_id === auth()->id())
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                                <i class="fas fa-plus"></i> Thêm thành viên
                            </button>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Email</th>
                                            <th>Vai trò trong team</th>
                                            <th>Ngày tham gia</th>
                                            @if(auth()->user()->role === 'admin' || $team->manager_id === auth()->id())
                                            <th>Thao tác</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($team->activeMembers as $member)
                                        <tr>
                                            <td>{{ $member->user->email }}</td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ ucfirst(str_replace('_', ' ', $member->role_in_team)) }}
                                                </span>
                                            </td>
                                            <td>{{ $member->created_at->format('d/m/Y') }}</td>
                                            @if(auth()->user()->role === 'admin' || $team->manager_id === auth()->id())
                                            <td>
                                                <form method="POST" action="{{ route('teams.remove-member', $team) }}" 
                                                      style="display: inline;" 
                                                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa thành viên này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="user_id" value="{{ $member->user_id }}">
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                            @endif
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="{{ (auth()->user()->role === 'admin' || $team->manager_id === auth()->id()) ? '4' : '3' }}" class="text-center">
                                                Chưa có thành viên nào
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Team Tasks -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3>Công việc của Team</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Tiêu đề</th>
                                            <th>Trạng thái</th>
                                            <th>Độ ưu tiên</th>
                                            <th>Hạn chót</th>
                                            <th>Người tạo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($team->tasks as $task)
                                        <tr>
                                            <td>{{ $task->title }}</td>
                                            <td>
                                                <span class="badge bg-{{ $task->status === 'completed' ? 'success' : ($task->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($task->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $task->priority === 'high' ? 'danger' : ($task->priority === 'medium' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($task->priority) }}
                                                </span>
                                            </td>
                                            <td>{{ $task->due_date ? $task->due_date->format('d/m/Y') : 'N/A' }}</td>
                                            <td>{{ $task->creator->email ?? 'N/A' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Chưa có công việc nào</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->role === 'admin' || $team->manager_id === auth()->id())
<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm thành viên vào Team</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('teams.add-member', $team) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Chọn người dùng *</label>
                        <select class="form-select" id="user_id" name="user_id" required>
                            <option value="">Chọn người dùng</option>
                            @foreach($availableUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->email }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="role_in_team" class="form-label">Vai trò trong team *</label>
                        <select class="form-select" id="role_in_team" name="role_in_team" required>
                            <option value="member">Member</option>
                            <option value="senior_member">Senior Member</option>
                            <option value="team_lead">Team Lead</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm thành viên</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
