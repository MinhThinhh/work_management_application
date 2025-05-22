<div class="profile-card">
    <div class="profile-card__header">
        <div class="profile-avatar">
            <div class="profile-avatar__circle">
                {{ substr(Auth::user()->name ?? Auth::user()->email, 0, 2) }}
            </div>
        </div>
        <div class="profile-info">
            <h2 class="profile-info__name">{{ Auth::user()->name ?? 'Chưa cập nhật' }}</h2>
            <p class="profile-info__role">
                @if(Auth::user()->role == 'admin')
                    <span class="badge badge-primary">Admin</span>
                @elseif(Auth::user()->role == 'manager')
                    <span class="badge badge-info">Manager</span>
                @else
                    <span class="badge badge-success">User</span>
                @endif
            </p>
        </div>
    </div>
    
    <div class="profile-card__body">
        <div class="profile-columns">
            <!-- Cột 1: Thông tin cá nhân -->
            <div class="profile-column">
                <h3 class="profile-column__title">Thông tin cá nhân</h3>
                <div class="profile-detail">
                    <div class="profile-detail__item">
                        <div class="profile-detail__label">
                            <i class="fas fa-envelope"></i>
                            <span>Email</span>
                        </div>
                        <div class="profile-detail__value">{{ Auth::user()->email }}</div>
                    </div>
                    <div class="profile-detail__item">
                        <div class="profile-detail__label">
                            <i class="fas fa-phone"></i>
                            <span>Số điện thoại</span>
                        </div>
                        <div class="profile-detail__value">{{ Auth::user()->phone ?? 'Chưa cập nhật' }}</div>
                    </div>
                    <div class="profile-detail__item">
                        <div class="profile-detail__label">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Địa chỉ</span>
                        </div>
                        <div class="profile-detail__value">{{ Auth::user()->address ?? 'Chưa cập nhật' }}</div>
                    </div>
                    <div class="profile-action">
                        <a href="#" class="btn-sm btn-primary" id="edit-profile-btn">
                            <i class="fas fa-edit mr-1"></i> Chỉnh sửa thông tin
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Cột 2: Đổi mật khẩu -->
            <div class="profile-column">
                <h3 class="profile-column__title">Đổi mật khẩu</h3>
                <form action="{{ route('user.change-password') }}" method="POST" class="password-form">
                    @csrf
                    <div class="form-group">
                        <label for="current_password">Mật khẩu hiện tại</label>
                        <input type="password" id="current_password" name="current_password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">Mật khẩu mới</label>
                        <input type="password" id="new_password" name="new_password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password_confirmation">Xác nhận mật khẩu</label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control" required>
                    </div>
                    <div class="profile-action">
                        <button type="submit" class="btn-sm btn-primary">
                            <i class="fas fa-key mr-1"></i> Cập nhật mật khẩu
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Cột 3: Đăng xuất và các tùy chọn khác -->
            <div class="profile-column">
                <h3 class="profile-column__title">Tùy chọn tài khoản</h3>
                <div class="profile-options">
                    <form action="/logout" method="POST" class="logout-form">
                        @csrf
                        <button type="submit" class="btn-sm btn-danger" id="logout-button-profile">
                            <i class="fas fa-sign-out-alt mr-1"></i> Đăng xuất
                        </button>
                    </form>
                    
                    <div class="profile-detail__item mt-3">
                        <div class="profile-detail__label">
                            <i class="fas fa-clock"></i>
                            <span>Đăng nhập lần cuối</span>
                        </div>
                        <div class="profile-detail__value">{{ Auth::user()->last_login_at ?? 'Không có dữ liệu' }}</div>
                    </div>
                    
                    <div class="profile-detail__item">
                        <div class="profile-detail__label">
                            <i class="fas fa-user-clock"></i>
                            <span>Tài khoản tạo lúc</span>
                        </div>
                        <div class="profile-detail__value">{{ Auth::user()->created_at ? Auth::user()->created_at->format('d/m/Y H:i') : 'Không có dữ liệu' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .profile-card {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        padding: 20px;
        width: 800px;
        max-width: 90vw;
    }
    
    .profile-card__header {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .profile-avatar {
        margin-right: 15px;
    }
    
    .profile-avatar__circle {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #4f46e5 0%, #2563eb 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: bold;
        text-transform: uppercase;
    }
    
    .profile-info__name {
        font-size: 20px;
        font-weight: 600;
        margin: 0 0 5px 0;
        color: #1f2937;
    }
    
    .profile-columns {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }
    
    .profile-column {
        flex: 1;
        min-width: 220px;
        padding: 15px;
        background-color: #f9fafb;
        border-radius: 8px;
    }
    
    .profile-column__title {
        font-size: 16px;
        font-weight: 600;
        margin: 0 0 15px 0;
        color: #4f46e5;
        padding-bottom: 8px;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .profile-detail__item {
        padding: 8px 0;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .profile-detail__item:last-child {
        border-bottom: none;
    }
    
    .profile-detail__label {
        display: flex;
        align-items: center;
        color: #6b7280;
        font-size: 14px;
        margin-bottom: 4px;
    }
    
    .profile-detail__label i {
        margin-right: 8px;
        width: 16px;
    }
    
    .profile-detail__value {
        color: #1f2937;
        font-weight: 500;
        font-size: 14px;
        word-break: break-word;
    }
    
    .profile-action {
        margin-top: 15px;
        display: flex;
        justify-content: center;
    }
    
    .form-group {
        margin-bottom: 12px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-size: 14px;
        color: #4b5563;
    }
    
    .form-control {
        width: 100%;
        padding: 8px;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        font-size: 14px;
    }
    
    .btn-sm {
        padding: 6px 12px;
        font-size: 14px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: none;
        text-decoration: none;
    }
    
    .btn-primary {
        background-color: #4f46e5;
        color: white;
    }
    
    .btn-primary:hover {
        background-color: #4338ca;
    }
    
    .btn-danger {
        background-color: #ef4444;
        color: white;
    }
    
    .btn-danger:hover {
        background-color: #dc2626;
    }
    
    .badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .badge-primary {
        background-color: #4f46e5;
        color: white;
    }
    
    .badge-info {
        background-color: #3b82f6;
        color: white;
    }
    
    .badge-success {
        background-color: #10b981;
        color: white;
    }
    
    .mr-1 {
        margin-right: 4px;
    }
    
    .mt-3 {
        margin-top: 12px;
    }
    
    .logout-form {
        display: flex;
        justify-content: center;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .profile-columns {
            flex-direction: column;
        }
        
        .profile-column {
            width: 100%;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý nút chỉnh sửa thông tin
        const editProfileBtn = document.getElementById('edit-profile-btn');
        if (editProfileBtn) {
            editProfileBtn.addEventListener('click', function(e) {
                e.preventDefault();
                alert('Chức năng đang được phát triển. Vui lòng quay lại sau!');
            });
        }
        
        // Xử lý đăng xuất
        const logoutButton = document.getElementById('logout-button-profile');
        if (logoutButton) {
            logoutButton.addEventListener('click', function() {
                // Xóa token từ localStorage khi đăng xuất
                localStorage.removeItem('jwt_token');
                console.log('JWT token removed from localStorage on logout');
            });
        }
    });
</script>
