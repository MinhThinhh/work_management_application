@extends('admin.layout')

@section('title', 'Quản lý đội nhóm - Admin')

@section('header', 'Quản lý đội nhóm')

@section('content')
<div class="card">
    <div class="mb-4 flex justify-between items-center">
        <h2 class="text-xl font-semibold">Danh sách đội nhóm</h2>
        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addTeamModal">
            <i class="fas fa-plus"></i> Tạo nhóm mới
        </button>
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

    <div class="overflow-x-auto">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên nhóm</th>
                    <th>Người quản lý</th>
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
                    <td>{{ $team->leader->name ?? 'Chưa có leader' }}</td>
                    <td>{{ $team->members->count() }}</td>
                    <td>
                        @if($team->status === 'active')
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Hoạt động</span>
                        @else
                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Không hoạt động</span>
                        @endif
                    </td>
                    <td class="flex space-x-2">
                        <button class="btn btn-sm btn-primary" onclick="manageMembers({{ $team->id }})" title="Quản lý thành viên">
                            <i class="fas fa-users"></i> Thành viên
                        </button>
                        <form action="{{ route('admin.teams.destroy', $team) }}" method="POST"
                              onsubmit="return confirm('Bạn có chắc chắn muốn xóa đội nhóm này? Tất cả thành viên sẽ bị loại khỏi nhóm.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i> Xóa
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4">Chưa có đội nhóm nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add Team Modal -->
<div class="modal fade" id="addTeamModal" tabindex="-1" aria-labelledby="addTeamModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTeamModalLabel">Tạo đội nhóm mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.teams.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên đội nhóm <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required
                               placeholder="Nhập tên đội nhóm">
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                                  placeholder="Mô tả về đội nhóm (tùy chọn)"></textarea>
                        @error('description')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="leader_id" class="form-label">Trưởng nhóm <span class="text-danger">*</span></label>
                        <select class="form-select" id="leader_id" name="leader_id" required>
                            <option value="">Chọn trưởng nhóm</option>
                            @foreach($managers as $manager)
                                <option value="{{ $manager->id }}">{{ $manager->name }} ({{ $manager->email }})</option>
                            @endforeach
                        </select>
                        @error('leader_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Chỉ có thể chọn người dùng có vai trò Manager</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-plus"></i> Tạo đội nhóm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<!-- Manage Members Modal -->
<div class="modal fade" id="manageMembersModal" tabindex="-1" aria-labelledby="manageMembersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manageMembersModalLabel">
                    <i class="fas fa-users"></i> Quản lý thành viên - <span id="teamName" class="text-primary"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Current Members -->
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0"><i class="fas fa-user-check text-success"></i> Thành viên hiện tại</h6>
                            <span id="memberCount" class="badge bg-primary">0</span>
                        </div>
                        <div id="currentMembers" class="border rounded p-3 bg-light" style="min-height: 300px; max-height: 400px; overflow-y: auto;">
                            <div class="text-center text-muted">
                                <i class="fas fa-spinner fa-spin"></i> Đang tải...
                            </div>
                        </div>
                    </div>
                    <!-- Available Users -->
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0"><i class="fas fa-user-plus text-info"></i> Người dùng có thể thêm</h6>
                            <span id="availableCount" class="badge bg-info">0</span>
                        </div>
                        <div id="availableUsers" class="border rounded p-3 bg-light" style="min-height: 300px; max-height: 400px; overflow-y: auto;">
                            <div class="text-center text-muted">
                                <i class="fas fa-spinner fa-spin"></i> Đang tải...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Đóng
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.member-item {
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    background: white;
}

.member-item:hover {
    background-color: #f8f9fa;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border-color: #007bff;
}

.member-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(45deg, #007bff, #0056b3);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 14px;
}

.member-info {
    flex: 1;
    margin-left: 12px;
}

.member-name {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 2px;
}

.member-email {
    color: #6c757d;
    font-size: 0.875rem;
}

.btn-action {
    transition: all 0.2s ease;
    border-radius: 6px;
    font-size: 0.875rem;
    padding: 6px 12px;
}

.btn-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.btn-add {
    background: linear-gradient(45deg, #28a745, #20c997);
    border: none;
    color: white;
}

.btn-add:hover {
    background: linear-gradient(45deg, #218838, #1ea080);
    color: white;
}

.btn-remove {
    background: linear-gradient(45deg, #dc3545, #e74c3c);
    border: none;
    color: white;
}

.btn-remove:hover {
    background: linear-gradient(45deg, #c82333, #dc2626);
    color: white;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #6c757d;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.loading-spinner {
    text-align: center;
    padding: 40px 20px;
    color: #6c757d;
}

.modal-header {
    background: linear-gradient(45deg, #007bff, #0056b3);
    color: white;
    border-bottom: none;
}

.modal-header .btn-close {
    filter: invert(1);
}

.badge-count {
    font-size: 0.75rem;
    padding: 4px 8px;
}
</style>
@endpush

@push('scripts')
<script>
let currentTeamId = null;

function manageMembers(teamId) {
    currentTeamId = teamId;
    loadTeamMembers(teamId);
    const modal = new bootstrap.Modal(document.getElementById('manageMembersModal'));
    modal.show();
}

function loadTeamMembers(teamId) {
    // Show loading state
    document.getElementById('currentMembers').innerHTML = `
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <div class="mt-2">Đang tải dữ liệu...</div>
        </div>
    `;
    document.getElementById('availableUsers').innerHTML = `
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <div class="mt-2">Đang tải dữ liệu...</div>
        </div>
    `;

    // Load team info and members
    fetch(`/admin/teams/${teamId}/members`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('teamName').textContent = data.team.name;
                renderCurrentMembers(data.members);
                renderAvailableUsers(data.availableUsers);

                // Update counts
                document.getElementById('memberCount').textContent = data.members.length;
                document.getElementById('availableCount').textContent = data.availableUsers.length;
            } else {
                showError('Lỗi: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Có lỗi xảy ra khi tải dữ liệu');
        });
}

function renderCurrentMembers(members) {
    const container = document.getElementById('currentMembers');

    if (members.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <div>Chưa có thành viên nào</div>
                <small>Thêm thành viên từ danh sách bên phải</small>
            </div>
        `;
        return;
    }

    let html = '';
    members.forEach(member => {
        const initials = member.name.split(' ').map(n => n[0]).join('').toUpperCase();
        html += `
            <div class="member-item d-flex align-items-center mb-3 p-3">
                <div class="member-avatar">${initials}</div>
                <div class="member-info">
                    <div class="member-name">${member.name}</div>
                    <div class="member-email">${member.email}</div>
                </div>
                <button class="btn btn-action btn-remove" onclick="removeMember(${member.id})" title="Xóa khỏi nhóm">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
    });
    container.innerHTML = html;
}

function renderAvailableUsers(users) {
    const container = document.getElementById('availableUsers');

    if (users.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-user-slash"></i>
                <div>Không có người dùng nào có thể thêm</div>
                <small>Tất cả người dùng đã có nhóm hoặc là admin</small>
            </div>
        `;
        return;
    }

    let html = '';
    users.forEach(user => {
        const initials = user.name.split(' ').map(n => n[0]).join('').toUpperCase();
        html += `
            <div class="member-item d-flex align-items-center mb-3 p-3">
                <div class="member-avatar">${initials}</div>
                <div class="member-info">
                    <div class="member-name">${user.name}</div>
                    <div class="member-email">${user.email}</div>
                </div>
                <button class="btn btn-action btn-add" onclick="addMember(${user.id})" title="Thêm vào nhóm">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        `;
    });
    container.innerHTML = html;
}

function addMember(userId) {
    // Disable button to prevent double clicks
    const button = event.target.closest('button');
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch(`/admin/teams/${currentTeamId}/add-member`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ user_id: userId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess('Thành viên đã được thêm vào nhóm thành công!');
            loadTeamMembers(currentTeamId); // Reload data
            // Update main table after a short delay
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showError('Lỗi: ' + data.message);
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-plus"></i>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Có lỗi xảy ra khi thêm thành viên');
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-plus"></i>';
    });
}

function removeMember(userId) {
    if (!confirm('Bạn có chắc chắn muốn xóa thành viên này khỏi nhóm?')) {
        return;
    }

    // Disable button to prevent double clicks
    const button = event.target.closest('button');
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch(`/admin/teams/${currentTeamId}/remove-member`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ user_id: userId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess('Thành viên đã được xóa khỏi nhóm thành công!');
            loadTeamMembers(currentTeamId); // Reload data
            // Update main table after a short delay
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showError('Lỗi: ' + data.message);
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-times"></i>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Có lỗi xảy ra khi xóa thành viên');
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-times"></i>';
    });
}

function showSuccess(message) {
    // Create and show success toast
    const toast = document.createElement('div');
    toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed';
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-check-circle me-2"></i>${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    document.body.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();

    // Remove toast after it's hidden
    toast.addEventListener('hidden.bs.toast', () => {
        document.body.removeChild(toast);
    });
}

function showError(message) {
    // Create and show error toast
    const toast = document.createElement('div');
    toast.className = 'toast align-items-center text-white bg-danger border-0 position-fixed';
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-exclamation-circle me-2"></i>${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    document.body.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();

    // Remove toast after it's hidden
    toast.addEventListener('hidden.bs.toast', () => {
        document.body.removeChild(toast);
    });
}
</script>
@endpush
