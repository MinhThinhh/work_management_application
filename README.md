# Work Management Application

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)

Hệ thống Quản lý Công việc toàn diện với ứng dụng Web và Desktop, hỗ trợ quản lý nhiệm vụ, lịch trình, và phân quyền người dùng với bảo mật JWT.

## 📋 Tổng quan

Work Management Application là một giải pháp quản lý công việc toàn diện, bao gồm:

- **Ứng dụng Web** (Laravel): Hệ thống quản lý công việc với giao diện web đầy đủ tính năng
- **Ứng dụng Desktop** (Electron): Ứng dụng desktop tương tác với API của ứng dụng web

Dự án này được thiết kế với trọng tâm về bảo mật API, xác thực JWT, và trải nghiệm người dùng liền mạch trên cả nền tảng web và desktop.

## 🚀 Tính năng chính

### Ứng dụng Web (Laravel)

- **Xác thực và Phân quyền**
  - Đăng nhập/Đăng ký tài khoản
  - Phân quyền: Admin, Manager, User
  - JWT Authentication với token refresh và blacklist
  - Bảo vệ CSRF

- **Quản lý Công việc**
  - Tạo, xem, cập nhật, xóa công việc
  - Phân công công việc cho người dùng
  - Theo dõi trạng thái công việc
  - Lịch trình và deadline

- **Quản lý Người dùng**
  - Quản lý hồ sơ người dùng
  - Phân quyền và vai trò
  - Quản lý thông tin cá nhân

- **Thông báo và Báo cáo**
  - Hệ thống thông báo
  - Báo cáo và thống kê

### Ứng dụng Desktop (Electron)

- **Tích hợp với API**
  - Đăng nhập và xác thực với JWT
  - Lưu token an toàn trong keychain
  - Kiểm tra kết nối API

- **Quản lý Công việc**
  - Xem danh sách công việc
  - Thêm công việc mới
  - Cập nhật trạng thái

- **Bảo mật Nâng cao**
  - Lưu trữ token an toàn với keytar
  - Xử lý token hết hạn
  - Bảo vệ khỏi các cuộc tấn công phổ biến

## 🛠️ Công nghệ sử dụng

### Ứng dụng Web
- **Backend**: PHP 8.x, Laravel 10.x
- **Database**: MySQL 8.0
- **Authentication**: JWT (JSON Web Tokens)
- **Frontend**: Blade, JavaScript, Bootstrap 5

### Ứng dụng Desktop
- **Framework**: Electron
- **Language**: JavaScript (Node.js)
- **Security**: keytar (secure credential storage)

## 📦 Cài đặt và Chạy ứng dụng

### Yêu cầu hệ thống
- PHP >= 8.0
- Composer
- MySQL >= 8.0
- Node.js >= 14.x
- npm >= 6.x

### Cài đặt Ứng dụng Web

1. Clone repository
2. Cài đặt dependencies:
```bash
cd work_management
composer install
npm install
```

3. Cấu hình môi trường:
```bash
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

4. Cấu hình database trong file .env
5. Chạy migrations:
```bash
php artisan migrate --seed
```

6. Chạy ứng dụng:
```bash
php artisan serve
```

### Cài đặt Ứng dụng Desktop

1. Di chuyển vào thư mục ứng dụng desktop:
```bash
cd work_management_desktopapp
```

2. Cài đặt dependencies:
```bash
npm install
```

3. Chạy ứng dụng:
```bash
npm start
```

## 🔒 Bảo mật

Dự án này tập trung vào bảo mật với các tính năng:

- **JWT Authentication**: Xác thực an toàn với JSON Web Tokens
- **Token Blacklist**: Vô hiệu hóa token khi đăng xuất
- **Token Refresh**: Tự động làm mới token khi hết hạn
- **Secure Storage**: Lưu trữ token an toàn trong keychain (desktop app)
- **CSRF Protection**: Bảo vệ khỏi tấn công Cross-Site Request Forgery
- **Role-based Access Control**: Kiểm soát quyền truy cập dựa trên vai trò

## 📁 Cấu trúc dự án

```
Work Management Application/
├── work_management/               # Ứng dụng Web (Laravel)
│   ├── app/                       # Core application code
│   │   ├── Http/Controllers/      # Controllers
│   │   ├── Models/                # Database models
│   │   └── ...
│   ├── database/                  # Migrations and seeders
│   ├── resources/                 # Views and assets
│   ├── routes/                    # API and web routes
│   └── ...
│
└── work_management_desktopapp/    # Ứng dụng Desktop (Electron)
    ├── main.js                    # Main process
    ├── preload.js                 # Preload script
    ├── src/                       # Renderer process
    │   ├── login.html             # Login page
    │   └── tasks.html             # Tasks page
    └── ...
```

## 🚀 Chạy ứng dụng nhanh

- Chạy Work Management Web: `php artisan serve`
- Chạy Work Management Desktop App: `npm start`

---

&copy; 2025 Work Management Application. All rights reserved.
