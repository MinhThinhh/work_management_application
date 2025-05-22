// JWT Handler - Xử lý JWT token

// Không lưu token vào localStorage vì lý do bảo mật
// Token sẽ được lưu trong HttpOnly cookie bởi server
function saveToken(token) {
    if (!token) {
        console.error('Không thể xử lý token vì token rỗng');
        return;
    }

    // Chỉ ghi log thông báo, không thực hiện lưu token vào localStorage
    console.log('Token đã được xử lý. Token được lưu an toàn trong HttpOnly cookie.');
}

// Không lấy token từ localStorage vì lý do bảo mật
// Token sẽ được gửi tự động trong cookie với mỗi request
function getToken() {
    // Trả về null vì chúng ta không lưu token trong localStorage nữa
    // Token sẽ được gửi tự động trong cookie với mỗi request
    console.log('Token được lưu trong HttpOnly cookie và sẽ tự động gửi với mỗi request');
    return null;
}

// Không cần xóa token từ localStorage vì chúng ta không lưu ở đó
// Thay vào đó, chúng ta sẽ gọi API logout để server xóa cookie
function removeToken() {
    console.log('Token được quản lý bởi server thông qua HttpOnly cookie');

    // Gọi API logout để server xóa cookie
    fetch('/logout', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        credentials: 'same-origin' // Đảm bảo cookie được gửi
    })
    .then(response => {
        if (response.ok) {
            console.log('Đăng xuất thành công, cookie đã được xóa');
        } else {
            console.error('Lỗi khi đăng xuất:', response.status);
        }
    })
    .catch(error => {
        console.error('Lỗi khi gọi API logout:', error);
    });
}

// Kiểm tra phiên đăng nhập có hợp lệ không
function checkToken() {
    // Sử dụng API session verify để kiểm tra phiên đăng nhập
    // Cookie sẽ tự động được gửi đi
    return fetch('/api/auth/session', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin' // Đảm bảo cookie được gửi
    })
    .then(response => {
        // Chỉ log status khi debug, không log dữ liệu nhạy cảm
        if (response.ok) {
            return response.json();
        } else {
            console.error('Session check response not OK:', response.status);
            throw new Error('Lỗi kết nối đến API: ' + response.status);
        }
    })
    .then(data => {
        if (data && data.valid === true) {
            console.log('Phiên đăng nhập hợp lệ');
            return data;
        } else {
            console.error('Phiên đăng nhập không hợp lệ:', data && data.error ? data.error : 'Không rõ lỗi');
            throw new Error(data && data.error ? data.error : 'Phiên đăng nhập không hợp lệ');
        }
    })
    .catch(error => {
        console.error('Error checking session:', error);

        // Tạo thông báo lỗi chi tiết hơn
        let errorMessage = 'Lỗi kiểm tra phiên đăng nhập';

        if (error.message.includes('Failed to fetch')) {
            errorMessage = 'Không thể kết nối đến máy chủ API. Vui lòng kiểm tra kết nối mạng.';
        } else if (error.message.includes('404')) {
            errorMessage = 'API không tồn tại (404). Vui lòng kiểm tra cấu hình API.';
        } else if (error.message.includes('401')) {
            errorMessage = 'Không có quyền truy cập API (401). Phiên đăng nhập không hợp lệ.';
        } else {
            errorMessage = 'Lỗi kiểm tra phiên đăng nhập: ' + error.message;
        }

        const customError = new Error(errorMessage);
        customError.originalError = error;
        throw customError;
    });
}

// Thiết lập Axios interceptors
function setupAxiosInterceptors() {
    if (typeof axios !== 'undefined') {
        // Đảm bảo cookie được gửi với mỗi request
        axios.defaults.withCredentials = true;

        // Xử lý lỗi 401 (Unauthorized)
        axios.interceptors.response.use(
            response => response,
            error => {
                if (error.response && error.response.status === 401) {
                    console.log('Phiên đăng nhập đã hết hạn');

                    // Gọi API logout để xóa cookie
                    removeToken();

                    // Hiển thị thông báo lỗi nếu có thể
                    try {
                        const statusDiv = document.getElementById('jwt-status');
                        if (statusDiv) {
                            statusDiv.classList.remove('hidden');
                            statusDiv.classList.add('bg-red-100', 'border', 'border-red-400', 'text-red-700');
                            statusDiv.textContent = 'Phiên đăng nhập đã hết hạn';

                            // Thêm nút để chuyển hướng đến trang đăng nhập
                            const loginBtn = document.createElement('button');
                            loginBtn.type = 'button';
                            loginBtn.className = 'mt-2 ml-2 bg-blue-500 hover:bg-blue-700 text-white p-2 rounded';
                            loginBtn.textContent = 'Đăng nhập lại';
                            loginBtn.onclick = function() {
                                window.location.href = '/login';
                            };
                            statusDiv.appendChild(loginBtn);
                        }
                    } catch (e) {
                        console.error('Error showing status:', e);
                        // Nếu không thể hiển thị thông báo, chuyển hướng đến trang đăng nhập
                        window.location.href = '/login';
                    }
                }
                return Promise.reject(error);
            }
        );
    }
}

// Gọi API login - không sử dụng remember token vì lý do bảo mật
function loginApi(email, password) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    return fetch('/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            email,
            password
            // Đã loại bỏ tham số remember vì lý do bảo mật
        }),
        credentials: 'same-origin' // Đảm bảo cookie được gửi và nhận
    })
    .then(response => response.json())
    .then(data => {
        if (data.success !== false) {
            // Token đã được lưu trong HttpOnly cookie bởi server
            console.log('Đăng nhập thành công, phiên đăng nhập đã được thiết lập');
            setupAxiosInterceptors();
            return data;
        } else {
            throw new Error(data.error || 'Đăng nhập thất bại');
        }
    });
}

// Khởi tạo khi trang được load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Session Handler initialized');

    // Thiết lập Axios interceptors
    setupAxiosInterceptors();

    // Kiểm tra nếu đang ở trang login
    if (window.location.pathname.includes('login')) {
        const statusDiv = document.getElementById('jwt-status');

        // Kiểm tra phiên đăng nhập
        checkToken()
            .then(data => {
                if (statusDiv) {
                    statusDiv.classList.remove('hidden');
                    statusDiv.classList.add('bg-blue-100', 'border-blue-400', 'text-blue-700');
                    statusDiv.textContent = 'Bạn đã có phiên đăng nhập. Vui lòng đăng nhập lại hoặc nhấn nút bên dưới để tiếp tục.';
                }

                // Thêm nút để chuyển hướng đến dashboard
                const loginForm = document.querySelector('form[action*="login"]');
                if (loginForm && !document.querySelector('.continue-session-btn')) {
                    const continueBtn = document.createElement('button');
                    continueBtn.type = 'button';
                    continueBtn.className = 'mt-2 bg-blue-500 hover:bg-blue-700 text-white p-2 w-full rounded continue-session-btn';
                    continueBtn.textContent = 'Tiếp tục phiên đăng nhập';
                    continueBtn.onclick = function() {
                        window.location.href = '/dashboard';

                        // Reload trang sau 1 giây nếu chuyển hướng không thành công
                        setTimeout(function() {
                            if (window.location.pathname !== '/dashboard') {
                                window.location.reload();
                            }
                        }, 1000);
                    };
                    loginForm.appendChild(continueBtn);
                }
            })
            .catch(error => {
                console.log('Không có phiên đăng nhập hiện tại hoặc phiên đã hết hạn');
                // Không cần hiển thị lỗi, người dùng sẽ đăng nhập bình thường
            });

        // Thêm event listener cho form login
        const loginForm = document.querySelector('form[action*="login"]');
        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                if (statusDiv) {
                    // Hiển thị thông báo đang xử lý
                    statusDiv.classList.remove('hidden');
                    statusDiv.classList.add('bg-blue-100', 'border', 'border-blue-400', 'text-blue-700');
                    statusDiv.textContent = 'Đang xử lý đăng nhập...';
                }
            });
        }
    }
});
