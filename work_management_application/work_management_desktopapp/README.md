# Work Management Desktop App

Ứng dụng Desktop để tương tác với API của ứng dụng Quản lý Công việc, minh họa quá trình bảo mật API.

## Tính năng

- Đăng nhập và xác thực với JWT
- Lưu token trong keychain sử dụng thư viện keytar
- Xem danh sách công việc
- Thêm công việc mới
- Xử lý lỗi (đăng nhập sai, token hết hạn, không kết nối được API)
- Kiểm tra kết nối API

## Cài đặt

### Yêu cầu

- Node.js (>= 14.x)
- npm (>= 6.x)

### Các bước cài đặt

1. Clone repository
2. Cài đặt các dependencies:

```bash
cd work_management_desktopapp
npm install
```

## Chạy ứng dụng

```bash
npm start
```

Ứng dụng sẽ tự động kiểm tra kết nối với API server. Đảm bảo rằng API server đang chạy tại địa chỉ http://127.0.0.1:8000.

## Đóng gói ứng dụng

```bash
npm run build
```

## Cấu trúc dự án

- `main.js`: Entry point của ứng dụng Electron
- `preload.js`: Script preload để kết nối giữa renderer process và main process
- `src/`: Chứa các file HTML, CSS, JS cho giao diện người dùng
  - `login.html`: Trang đăng nhập
  - `tasks.html`: Trang danh sách công việc

## Bảo mật

Ứng dụng sử dụng các phương pháp bảo mật sau:

1. **JWT Authentication**: Sử dụng JSON Web Token để xác thực người dùng
2. **Secure Token Storage**: Lưu token trong keychain của hệ điều hành sử dụng thư viện keytar
3. **Token Refresh**: Tự động làm mới token khi hết hạn
4. **Error Handling**: Xử lý các lỗi bảo mật như token không hợp lệ, hết hạn
5. **API Connection Check**: Kiểm tra kết nối với API server trước khi thực hiện các thao tác

## Tương tác với API

Ứng dụng tương tác với các API endpoint sau:

- `POST /api/login`: Đăng nhập và lấy token
- `GET /api/token-info`: Kiểm tra thông tin token
- `GET /api/tasks`: Lấy danh sách công việc
- `POST /api/tasks`: Thêm công việc mới
- `GET /api/health-check`: Kiểm tra kết nối API

## Phát triển

### Cấu hình API

Bạn có thể thay đổi URL của API trong file `main.js`:

```javascript
const API_BASE_URL = 'http://127.0.0.1:8000/api';
```

### Thêm tính năng mới

Để thêm tính năng mới:

1. Thêm phương thức xử lý trong `main.js`
2. Expose phương thức đó trong `preload.js`
3. Sử dụng phương thức trong file HTML thông qua `window.api`

## Xử lý lỗi

Ứng dụng có khả năng xử lý các loại lỗi sau:

1. **Lỗi kết nối API**: Hiển thị thông báo khi không thể kết nối đến API server
2. **Lỗi xác thực**: Xử lý khi đăng nhập sai hoặc token không hợp lệ
3. **Lỗi token hết hạn**: Tự động đăng xuất và chuyển về trang đăng nhập khi token hết hạn

