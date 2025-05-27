<div class="edit-profile-container">
    <div class="edit-profile-header">
        <h2>Chỉnh sửa thông tin cá nhân</h2>
        <button type="button" id="close-edit-profile" class="close-button">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <form action="@if(Auth::user()->role == 'manager'){{ route('manager.update-profile') }}@elseif(Auth::user()->role == 'admin'){{ route('admin.update-profile') }}@else{{ route('user.update-profile') }}@endif" method="POST" id="edit-profile-form">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Họ và tên</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ Auth::user()->name }}" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" value="{{ Auth::user()->email }}" readonly>
            <small class="form-text text-muted">Email không thể thay đổi.</small>
        </div>

        <div class="form-group">
            <label for="phone">Số điện thoại</label>
            <input type="text" id="phone" name="phone" class="form-control" value="{{ Auth::user()->phone }}">
        </div>

        <div class="form-group">
            <label for="address">Địa chỉ</label>
            <input type="text" id="address" name="address" class="form-control" value="{{ Auth::user()->address }}">
        </div>

        <div class="form-actions">
            <button type="button" class="btn btn-secondary" id="cancel-edit-profile">Hủy</button>
            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        </div>
    </form>
</div>

<style>
    .edit-profile-container {
        background-color: white;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        width: 400px;
        max-width: 95vw;
        margin: 0;
        padding-top: 0;
    }

    .edit-profile-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        padding-bottom: 8px;
        border-bottom: 1px solid #e5e7eb;
        margin-top: 0;
        padding-top: 0;
    }

    .edit-profile-header h2 {
        font-size: 18px;
        font-weight: 600;
        margin: 0;
        color: #1f2937;
    }

    .close-button {
        background: none;
        border: none;
        color: #6b7280;
        cursor: pointer;
        font-size: 16px;
        padding: 4px;
    }

    .close-button:hover {
        color: #1f2937;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
        color: #4b5563;
        font-size: 14px;
    }

    .form-control {
        width: 100%;
        padding: 8px 10px;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        font-size: 14px;
        transition: border-color 0.2s;
    }

    .form-control:focus {
        border-color: #4f46e5;
        outline: none;
        box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
    }

    .form-control[readonly] {
        background-color: #f9fafb;
        cursor: not-allowed;
    }

    .form-text {
        font-size: 13px;
        color: #6b7280;
        margin-top: 4px;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px solid #e5e7eb;
    }

    .btn {
        padding: 8px 15px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        border: none;
        transition: background-color 0.2s;
    }

    .btn-primary {
        background-color: #4f46e5;
        color: white;
    }

    .btn-primary:hover {
        background-color: #4338ca;
    }

    .btn-secondary {
        background-color: #9ca3af;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #6b7280;
    }

    .text-danger {
        color: #ef4444;
        font-size: 13px;
        margin-top: 4px;
        display: block;
    }

    .alert {
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 20px;
    }

    .alert-success {
        background-color: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .alert-danger {
        background-color: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const closeButton = document.getElementById('close-edit-profile');
        const cancelButton = document.getElementById('cancel-edit-profile');

        const closeEditProfile = function() {
            const editProfileContainer = document.getElementById('edit-profile-container');
            if (editProfileContainer) {
                editProfileContainer.classList.add('hidden');
            }

            // Hiển thị lại form thông tin cá nhân
            const profileInfoContainer = document.getElementById('profile-info-container');
            if (profileInfoContainer) {
                profileInfoContainer.classList.remove('hidden');
            }
        };

        if (closeButton) {
            closeButton.addEventListener('click', closeEditProfile);
        }

        if (cancelButton) {
            cancelButton.addEventListener('click', closeEditProfile);
        }
    });
</script>
