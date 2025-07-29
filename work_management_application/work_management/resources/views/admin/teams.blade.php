@extends('admin.layout')

@section('title', 'Quản lý Team')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Danh sách Team</h3>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTeamModal">
                        <i class="fas fa-plus"></i> Tạo Team mới
                    </button>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên Team</th>
                                    <th>Manager</th>
                                    <th>Số thành viên</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teams as $team)
                                <tr>
                                    <td>{{ $team->id }}</td>
                                    <td>{{ $team->name }}</td>
                                    <td>{{ $team->manager->name ?? 'N/A' }}</td>
                                    <td>{{ $team->activeMembers->count() }}</td>
                                    <td>
                                        <span class="badge bg-{{ $team->is_active ? 'success' : 'secondary' }}">
                                            {{ $team->is_active ? 'Hoạt động' : 'Không hoạt động' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="editTeam({{ $team->id }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-info" onclick="manageMembers({{ $team->id }})">
                                            <i class="fas fa-users"></i>
                                        </button>
                                        <form method="POST" action="{{ route('admin.teams.destroy', $team) }}" 
                                              style="display: inline;" 
                                              onsubmit="return confirm('Bạn có chắc chắn muốn xóa team này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Chưa có team nào</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3>Thống kê Team</h3>
                </div>
                <div class="card-body">
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Tổng số Team:</span>
                            <strong>{{ $teams->count() }}</strong>
                        </div>
                    </div>
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Tổng số Manager:</span>
                            <strong>{{ $teams->pluck('manager_id')->unique()->count() }}</strong>
                        </div>
                    </div>
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Tổng thành viên:</span>
                            <strong>{{ $teams->sum(function($team) { return $team->activeMembers->count(); }) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Team Modal -->
<div class="modal fade" id="addTeamModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tạo Team mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.teams.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên Team *</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="manager_id" class="form-label">Manager *</label>
                        <select class="form-select" id="manager_id" name="manager_id" required>
                            <option value="">Chọn Manager</option>
                            @foreach($managers as $manager)
                                <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Tạo Team</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function editTeam(teamId) {
    // TODO: Implement edit team functionality
    alert('Chức năng chỉnh sửa team sẽ được thêm sau');
}

function manageMembers(teamId) {
    // TODO: Implement manage members functionality
    alert('Chức năng quản lý thành viên sẽ được thêm sau');
}
</script>
@endsection
