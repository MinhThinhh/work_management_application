<div class="password-form-container">
    <div class="password-form-header">
        <h2>Đổi mật khẩu</h2>
        <button type="button" id="close-password-form" class="close-button">
            <i class="fas fa-times"></i>
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="@if(Auth::user()->role == 'manager'){{ route('manager.change-password') }}@elseif(Auth::user()->role == 'admin'){{ route('admin.change-password') }}@else{{ route('user.change-password') }}@endif" method="POST">
        @csrf

        <div class="form-group">
            <label for="current_password">Mật khẩu hiện tại</label>
            <input type="password" id="current_password" name="current_password" class="form-control" required>
            @error('current_password')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="new_password">Mật khẩu mới</label>
            <input type="password" id="new_password" name="new_password" class="form-control" required>
            @error('new_password')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="new_password_confirmation">Xác nhận mật khẩu mới</label>
            <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control" required>
        </div>

        <div class="form-actions">
            <button type="button" class="btn btn-secondary" id="cancel-password-change">Hủy</button>
            <button type="submit" class="btn btn-primary">Cập nhật</button>
        </div>
    </form>
</div>

<style>
    .password-form-container {
        background-color: white;
        border-radius: 8px;
        padding: 25px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        width: 600px;
        max-width: 95vw;
        margin: 0 auto;
    }

    .password-form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e5e7eb;
    }

    .password-form-header h2 {
        font-size: 20px;
        font-weight: 600;
        margin: 0;
        color: #1f2937;
    }

    .close-button {
        background: none;
        border: none;
        color: #6b7280;
        cursor: pointer;
        font-size: 18px;
        padding: 4px;
    }

    .close-button:hover {
        color: #1f2937;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #4b5563;
        font-size: 15px;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 15px;
    }

    .form-control:focus {
        border-color: #4f46e5;
        outline: none;
        box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 25px;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
    }

    .btn {
        padding: 10px 18px;
        border-radius: 6px;
        font-size: 15px;
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
        font-size: 12px;
        margin-top: 4px;
        display: block;
    }

    .alert {
        padding: 10px;
        border-radius: 4px;
        margin-bottom: 15px;
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
        const closeButton = document.getElementById('close-password-form');
        const cancelButton = document.getElementById('cancel-password-change');

        const closePasswordForm = function() {
            const passwordChangeContainer = document.getElementById('password-change-container');
            if (passwordChangeContainer) {
                passwordChangeContainer.classList.add('hidden');
            }
        };

        if (closeButton) {
            closeButton.addEventListener('click', closePasswordForm);
        }

        if (cancelButton) {
            cancelButton.addEventListener('click', closePasswordForm);
        }
    });
</script>
