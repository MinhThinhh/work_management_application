<div class="profile-card">
    <div class="profile-card__header">
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
        </div>
    </div>
    <div class="profile-card__footer">
        <div class="profile-actions">
            <a href="#" class="btn-sm btn-primary" id="edit-profile-btn">
                <i class="fas fa-edit mr-1"></i> Chỉnh sửa thông tin
            </a>
            <a href="#" class="btn-sm btn-secondary" id="change-password-btn">
                <i class="fas fa-key mr-1"></i> Đổi mật khẩu
            </a>
        </div>
    </div>
</div>
