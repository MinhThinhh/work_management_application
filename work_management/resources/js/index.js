// Xử lý JWT và authentication

// Lưu token vào localStorage khi đăng nhập thành công
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - Checking for login form');

    // Kiểm tra nếu đang ở trang login
    const loginForm = document.querySelector('form[action*="login"]');
    if (loginForm) {
        console.log('Login form found - Adding event listener');

        // Thêm event listener cho form login
        loginForm.addEventListener('submit', function(e) {
            console.log('Login form submitted');

            // Lấy dữ liệu từ form
            const formData = new FormData(loginForm);
            const email = formData.get('email');
            const password = formData.get('password');
            const remember = formData.get('remember') ? true : false;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Không ngăn chặn form submit mặc định
            // Thay vào đó, thêm một event listener để xử lý sau khi form được submit

            // Lưu thông tin đăng nhập vào sessionStorage để sử dụng sau
            sessionStorage.setItem('login_email', email);

            // Sau khi form được submit, gọi API để lấy token JWT
            setTimeout(function() {
                fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        email,
                        password,
                        remember
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('API response:', data);
                    if (data.token) {
                        // Lưu token vào localStorage
                        localStorage.setItem('jwt_token', data.token);
                        console.log('JWT token saved to localStorage');
                    }
                })
                .catch(error => {
                    console.error('Error calling login API:', error);
                });
            }, 500);
        });
    }

    // Thiết lập interceptors cho Axios nếu đã đăng nhập
    setupAxiosInterceptors();
});

// Thiết lập interceptors cho Axios
function setupAxiosInterceptors() {
    // Kiểm tra xem Axios có được định nghĩa không
    if (typeof axios !== 'undefined') {
        // Lấy token từ localStorage
        const token = localStorage.getItem('jwt_token');

        if (token) {
            // Thêm token vào header Authorization cho tất cả các request
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

            // Xử lý lỗi 401 (Unauthorized)
            axios.interceptors.response.use(
                response => response,
                error => {
                    if (error.response && error.response.status === 401) {
                        // Xóa token và chuyển hướng đến trang đăng nhập
                        localStorage.removeItem('jwt_token');
                        window.location.href = '/login';
                    }
                    return Promise.reject(error);
                }
            );
        }
    }
}

// Xử lý đăng xuất
document.addEventListener('DOMContentLoaded', function() {
    const logoutForm = document.querySelector('form[action*="logout"]');
    if (logoutForm) {
        logoutForm.addEventListener('submit', function(e) {
            // Xóa token khỏi localStorage khi đăng xuất
            localStorage.removeItem('jwt_token');
        });
    }
});
