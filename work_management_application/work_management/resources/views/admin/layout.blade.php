<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Quản lý công việc - Admin')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fa;
        }

        .sidebar {
            width: 280px;
            min-height: 100vh;
            background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 100%);
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .content {
            margin-left: 280px;
            padding: 30px;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 20px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-header .logo {
            font-size: 24px;
            font-weight: 700;
            margin-left: 10px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            margin: 5px 0;
        }

        .sidebar-link i {
            margin-right: 10px;
            font-size: 18px;
            width: 25px;
            text-align: center;
        }

        .sidebar-link:hover, .sidebar-link.active {
            background-color: rgba(255,255,255,0.1);
            color: white;
            border-left-color: white;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .btn i {
            margin-right: 8px;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 14px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .btn-success {
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            color: white;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #15803d 0%, #166534 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            padding: 25px;
            margin-bottom: 25px;
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transform: translateY(-5px);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 15px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }

        th {
            background-color: #f9fafb;
            font-weight: 600;
            color: #4b5563;
            position: sticky;
            top: 0;
        }

        tr:hover {
            background-color: #f9fafb;
        }

        .table-container {
            overflow-x: auto;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #4b5563;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            display: flex;
            align-items: center;
        }

        .alert i {
            margin-right: 10px;
            font-size: 20px;
        }

        .alert-success {
            background-color: #dcfce7;
            color: #16a34a;
            border-left: 4px solid #16a34a;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: #dc2626;
            border-left: 4px solid #dc2626;
        }

        .alert-info {
            background-color: #dbeafe;
            color: #2563eb;
            border-left: 4px solid #2563eb;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            padding: 25px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .stat-card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transform: translateY(-5px);
        }

        .stat-card .icon {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 48px;
            opacity: 0.1;
            color: #4b5563;
        }

        .stat-number {
            font-size: 36px;
            font-weight: 700;
            margin: 10px 0;
            background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-label {
            color: #6b7280;
            font-size: 16px;
            font-weight: 500;
        }

        .progress-bar {
            height: 8px;
            border-radius: 4px;
            background-color: #e5e7eb;
            margin-top: 15px;
            overflow: hidden;
        }

        .progress-value {
            height: 100%;
            border-radius: 4px;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #d97706;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .badge-info {
            background-color: #dbeafe;
            color: #2563eb;
        }

        .badge-primary {
            background-color: #e0e7ff;
            color: #4f46e5;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #4b5563;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            min-width: 160px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            z-index: 1;
            border-radius: 6px;
            overflow: hidden;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown-item {
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            color: #4b5563;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: #f9fafb;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f0f0f0;
        }

        .header-title {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
        }

        .header-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        /* Profile Button & Dropdown Styles */
        .profile-button {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            transition: transform 0.2s;
        }

        .profile-button:hover {
            transform: scale(1.05);
        }

        .profile-icon {
            width: 40px;
            height: 40px;
            background: #f0f0f0;
            color: #333;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            border: 2px solid #e0e0e0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .profile-dropdown {
            position: absolute;
            top: 50px;
            right: 0;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            min-width: 200px;
            border: 1px solid #f0f0f0;
        }

        .profile-dropdown-menu {
            background-color: white;
            border-radius: 12px;
            padding: 8px 0;
        }

        .profile-dropdown-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            transition: background-color 0.2s;
            cursor: pointer;
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

        .password-change-container,
        .edit-profile-container {
            position: absolute;
            top: 0;
            right: 0;
            z-index: 1000;
            margin-top: 0;
            padding-top: 0;
        }

        .hidden {
            display: none;
        }

        .relative {
            position: relative;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }

            .sidebar .logo, .sidebar-link span {
                display: none;
            }

            .content {
                margin-left: 70px;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-shield-alt"></i>
            <span class="logo">Admin Panel</span>
        </div>
        <nav class="mt-6">
            <a href="{{ route('admin.users') }}" class="sidebar-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Quản lý người dùng</span>
            </a>
            <a href="{{ route('admin.teams.index') }}" class="sidebar-link {{ request()->routeIs('admin.teams.*') ? 'active' : '' }}">
                <i class="fas fa-users-cog"></i>
                <span>Quản lý Team</span>
            </a>
            <a href="{{ route('admin.kpi.index') }}" class="sidebar-link {{ request()->routeIs('admin.kpi.*') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i>
                <span>KPI Dashboard</span>
            </a>
            <a href="{{ route('admin.all-tasks') }}" class="sidebar-link {{ request()->routeIs('admin.all-tasks') ? 'active' : '' }}">
                <i class="fas fa-tasks"></i>
                <span>Xem công việc</span>
            </a>
        </nav>
    </div>

    <div class="content">
        <div class="header">
            <h1 class="header-title">@yield('header', 'Dashboard')</h1>
            <div class="header-actions">
                <div class="relative">
                    <button id="profile-button" class="profile-button">
                        <div class="profile-icon">
                            {{ substr(Auth::user()->name ?? Auth::user()->email, 0, 2) }}
                        </div>
                    </button>
                    <div id="profile-dropdown" class="profile-dropdown hidden">
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
                                <a href="{{ route('logout') }}" id="logout-button-dropdown">Đăng xuất</a>
                            </div>
                        </div>
                    </div>
                    <div id="password-change-container" class="password-change-container hidden">
                        @include('profile.password-form')
                    </div>

                    <div id="edit-profile-container" class="edit-profile-container hidden">
                        @include('profile.edit-profile')
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                {{ session('info') }}
            </div>
        @endif

        @yield('content')
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        // Configure Axios to include CSRF token with every request
        window.axios = axios;
        window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>

    <script>
        // Xử lý hiển thị/ẩn profile dropdown
        document.addEventListener('DOMContentLoaded', function() {
            const profileButton = document.getElementById('profile-button');
            const profileDropdown = document.getElementById('profile-dropdown');

            // Hiển thị/ẩn dropdown khi nhấn vào profile button
            if (profileButton && profileDropdown) {
                profileButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    profileDropdown.classList.toggle('hidden');
                });

                // Ẩn dropdown khi nhấn ra ngoài
                document.addEventListener('click', function(e) {
                    if (!profileDropdown.contains(e.target) && e.target !== profileButton) {
                        profileDropdown.classList.add('hidden');
                    }
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

    @yield('scripts')
</body>
</html>
