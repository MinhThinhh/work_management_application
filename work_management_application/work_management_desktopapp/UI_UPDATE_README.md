# Cập nhật Giao diện Desktop App

## Tổng quan
Desktop app đã được cập nhật để sử dụng lại giao diện từ web app Laravel, tạo ra trải nghiệm người dùng nhất quán và hiện đại.

## Những thay đổi chính

### 1. Hệ thống màu sắc và Typography
- **Font chính**: Inter (Google Fonts)
- **Màu chính**: #3490dc (primary), #2779bd (primary-hover)
- **Màu nền**: #f8fafc
- **Màu văn bản**: #333
- **Màu viền**: #e2e8f0

### 2. Layout mới
- **Sidebar navigation**: Thanh điều hướng bên trái với logo và menu
- **Header**: Thanh tiêu đề với tên trang và các action buttons
- **Content area**: Khu vực nội dung chính với margin-left để tránh sidebar

### 3. Components được cập nhật

#### Login Page (`login.html`)
- Logo và branding mới
- Form styling hiện đại với focus states
- Loading states với spinner
- Success/error messages với styling đẹp
- Icons từ Font Awesome

#### Tasks Page (`tasks.html`)
- Sidebar với navigation links
- Header với title và add button
- Table styling hiện đại
- Modal forms với header và icons
- Confirm dialogs với warning icons
- Badge styling cho status và priority

### 4. CSS Architecture
- **common.css**: Chứa các styles chung, variables, và components
- **login.css**: Styles riêng cho trang login
- **tasks.css**: Styles riêng cho trang tasks, import common.css

### 5. JavaScript Enhancements
- Loading states cho tất cả các actions
- Better error handling với styled messages
- Success messages với auto-redirect
- Disabled states cho buttons khi đang xử lý

## Cấu trúc Files

```
src/
├── css/
│   ├── common.css      # Styles chung và components
│   ├── login.css       # Styles cho login page
│   └── tasks.css       # Styles cho tasks page
├── js/
│   ├── login.js        # Logic cho login
│   └── tasks.js        # Logic cho tasks
├── login.html          # Trang đăng nhập
└── tasks.html          # Trang quản lý tasks
```

## Features mới

### 1. Responsive Design
- Sidebar thu gọn trên mobile (chỉ hiện icons)
- Table responsive với horizontal scroll
- Modal responsive

### 2. Loading States
- Spinner animations
- Button disabled states
- Loading overlays

### 3. Better UX
- Focus states cho form inputs
- Hover effects cho buttons và links
- Smooth transitions
- Consistent spacing và typography

### 4. Icon System
- Font Awesome 6.0 icons
- Consistent icon usage
- Semantic icons cho các actions

## CSS Variables

```css
:root {
    --primary-color: #3490dc;
    --primary-hover: #2779bd;
    --secondary-color: #f8fafc;
    --text-color: #333;
    --border-color: #e2e8f0;
    --success-color: #38c172;
    --danger-color: #e3342f;
    --warning-color: #f6993f;
    --info-color: #6cb2eb;
    --sidebar-width: 250px;
}
```

## Component Classes

### Buttons
- `.btn` - Base button class
- `.btn-primary` - Primary button
- `.btn-success` - Success button
- `.btn-danger` - Danger button
- `.btn-secondary` - Secondary button

### Badges
- `.badge` - Base badge class
- `.badge-pending` - Pending status
- `.badge-in-progress` - In progress status
- `.badge-completed` - Completed status
- `.badge-high` - High priority
- `.badge-medium` - Medium priority
- `.badge-low` - Low priority

### Layout
- `.app` - Main app container
- `.sidebar` - Sidebar navigation
- `.content` - Main content area
- `.header` - Page header
- `.card` - Content card

### Forms
- `.form-group` - Form field container
- `.form-label` - Form labels
- `.form-control` - Form inputs

### Alerts
- `.alert` - Base alert class
- `.alert-success` - Success message
- `.alert-error` - Error message
- `.alert-warning` - Warning message
- `.alert-info` - Info message

## Responsive Breakpoints

```css
@media (max-width: 768px) {
    .sidebar {
        width: 70px; /* Thu gọn sidebar */
    }
    
    .sidebar .logo,
    .sidebar-link span {
        display: none; /* Ẩn text, chỉ hiện icons */
    }
    
    .content {
        margin-left: 70px;
    }
}
```

## Cách sử dụng

1. **Import common.css** trong các file CSS khác:
   ```css
   @import url('common.css');
   ```

2. **Sử dụng CSS variables**:
   ```css
   .my-element {
       color: var(--primary-color);
       background: var(--secondary-color);
   }
   ```

3. **Áp dụng component classes**:
   ```html
   <button class="btn btn-primary">
       <i class="fas fa-plus"></i>
       Thêm mới
   </button>
   ```

## Tương thích
- Tương thích với web app Laravel
- Responsive trên tất cả devices
- Cross-browser compatibility
- Accessibility friendly

## Lưu ý
- Font Awesome được load từ CDN
- Google Fonts (Inter) được load từ CDN
- CSS Variables được hỗ trợ từ IE 11+
- Flexbox và Grid được sử dụng (IE 11+ support)
