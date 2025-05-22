<div class="profile-dropdown-menu">
    <div class="profile-dropdown-item">
        <i class="fas fa-user"></i>
        <a href="#" id="profile-link">Thông tin cá nhân</a>
    </div>

    <div class="profile-dropdown-item">
        <i class="fas fa-key"></i>
        <a href="#" id="change-password-link">Đổi mật khẩu</a>
    </div>

    <div class="profile-dropdown-divider"></div>

    <div class="profile-dropdown-item">
        <i class="fas fa-sign-out-alt"></i>
        <form action="/logout" method="POST" class="logout-form">
            @csrf
            <button type="submit" id="logout-button-dropdown">Đăng xuất</button>
        </form>
    </div>
</div>

<style>
    .profile-dropdown-menu {
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 8px 0;
        min-width: 180px;
        border: 1px solid #f0f0f0;
    }

    .profile-dropdown-item {
        display: flex;
        align-items: center;
        padding: 10px 16px;
        transition: background-color 0.2s;
    }

    .profile-dropdown-item:hover {
        background-color: #f8f9fa;
    }

    .profile-dropdown-item i {
        color: #6b7280;
        width: 20px;
        margin-right: 12px;
        font-size: 16px;
    }

    .profile-dropdown-item a,
    .profile-dropdown-item button {
        color: #333;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        background: none;
        border: none;
        padding: 0;
        cursor: pointer;
        width: 100%;
        text-align: left;
    }

    .profile-dropdown-divider {
        height: 1px;
        background-color: #f0f0f0;
        margin: 8px 0;
    }

    .logout-form {
        width: 100%;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý đăng xuất
        const logoutButton = document.getElementById('logout-button-dropdown');
        if (logoutButton) {
            logoutButton.addEventListener('click', function() {
                // Xóa token từ localStorage khi đăng xuất
                localStorage.removeItem('jwt_token');
                console.log('JWT token removed from localStorage on logout');
            });
        }

        // Xử lý thông tin cá nhân
        const profileLink = document.getElementById('profile-link');
        if (profileLink) {
            profileLink.addEventListener('click', function(e) {
                e.preventDefault();

                // Hiển thị form chỉnh sửa thông tin cá nhân trực tiếp
                const editProfileContainer = document.getElementById('edit-profile-container');
                if (editProfileContainer) {
                    // Ẩn dropdown
                    const profileDropdown = document.getElementById('profile-dropdown');
                    if (profileDropdown) {
                        profileDropdown.classList.add('hidden');
                    }

                    // Hiển thị form chỉnh sửa thông tin cá nhân
                    editProfileContainer.classList.remove('hidden');
                } else {
                    alert('Chức năng thông tin cá nhân đang được phát triển. Vui lòng quay lại sau!');
                }
            });
        }

        // Xử lý đổi mật khẩu
        const changePasswordLink = document.getElementById('change-password-link');
        if (changePasswordLink) {
            changePasswordLink.addEventListener('click', function(e) {
                e.preventDefault();

                // Hiển thị form đổi mật khẩu
                const passwordChangeContainer = document.getElementById('password-change-container');
                if (passwordChangeContainer) {
                    // Ẩn dropdown
                    const profileDropdown = document.getElementById('profile-dropdown');
                    if (profileDropdown) {
                        profileDropdown.classList.add('hidden');
                    }

                    // Hiển thị form đổi mật khẩu
                    passwordChangeContainer.classList.remove('hidden');
                } else {
                    alert('Chức năng đổi mật khẩu đang được phát triển. Vui lòng quay lại sau!');
                }
            });
        }
    });
</script>
