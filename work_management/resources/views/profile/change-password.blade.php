<div class="change-password-form">
    <h2 class="form-title">Đổi mật khẩu</h2>
    
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
    
    <form action="{{ route('user.change-password') }}" method="POST">
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
            <button type="submit" class="btn btn-primary">Cập nhật mật khẩu</button>
        </div>
    </form>
</div>

<style>
    .change-password-form {
        background-color: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        max-width: 500px;
        margin: 0 auto;
    }
    
    .form-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #1f2937;
        text-align: center;
    }
    
    .form-group {
        margin-bottom: 15px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
        color: #4b5563;
    }
    
    .form-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        font-size: 14px;
    }
    
    .form-control:focus {
        border-color: #4f46e5;
        outline: none;
        box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
    }
    
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 20px;
    }
    
    .btn {
        padding: 8px 16px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        border: none;
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
        const cancelButton = document.getElementById('cancel-password-change');
        if (cancelButton) {
            cancelButton.addEventListener('click', function() {
                // Ẩn form đổi mật khẩu
                document.querySelector('.change-password-form').style.display = 'none';
                
                // Hiển thị lại profile dropdown nếu cần
                const profileDropdown = document.getElementById('profile-dropdown');
                if (profileDropdown) {
                    profileDropdown.classList.remove('hidden');
                }
            });
        }
    });
</script>
