@extends('layouts.auth')

@section('title', 'Đăng nhập - Work Management')

@section('content')
<div class="flex items-center justify-center min-h-[calc(100vh-140px)] bg-gray-100 py-8">
    <div class="bg-white p-6 rounded shadow-md w-96">
        <h2 class="text-2xl mb-4">Đăng nhập</h2>

        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
        @endif

        <div id="jwt-status" class="hidden mb-4 p-3 rounded" style="display: none;"></div>

        <form method="POST" action="{{ route('login') }}" id="login-form">
            @csrf
            <div class="mb-4">
                <input
                    type="email"
                    name="email"
                    id="email"
                    placeholder="Email"
                    class="border p-2 mb-4 w-full @error('email') border-red-500 @enderror"
                    required
                    autofocus
                    value="{{ old('email') }}" />
                @error('email')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="Mật khẩu"
                    class="border p-2 mb-4 w-full @error('password') border-red-500 @enderror"
                    required />
                @error('password')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <!-- Đã loại bỏ tùy chọn "Ghi nhớ đăng nhập" vì lý do bảo mật -->

            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white p-2 w-full rounded">
                Đăng nhập
            </button>

            <div class="text-center mt-4">
                <p class="text-sm">
                    Chưa có tài khoản?
                    <a href="{{ route('register') }}" class="text-blue-500 hover:text-blue-700">
                        Đăng ký
                    </a>
                </p>
            </div>
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const apiLoginBtn = document.getElementById('api-login-btn');
                const statusDiv = document.getElementById('jwt-status');

                if (apiLoginBtn) {
                    apiLoginBtn.addEventListener('click', function(e) {
                        e.preventDefault();

                        const email = document.getElementById('email').value;
                        const password = document.getElementById('password').value;
                        const remember = document.getElementById('remember').checked;

                        if (!email || !password) {
                            alert('Vui lòng nhập email và mật khẩu');
                            return;
                        }

                        // Hiển thị thông báo đang xử lý
                        if (statusDiv) {
                            statusDiv.classList.remove('hidden');
                            statusDiv.classList.add('bg-blue-100', 'border', 'border-blue-400', 'text-blue-700');
                            statusDiv.textContent = 'Đang xử lý đăng nhập qua API...';
                        }

                        // Gọi API để lấy token
                        fetch('/get-token', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    email,
                                    password,
                                    remember
                                })
                            })
                            .then(response => {
                                console.log('API response status:', response.status);
                                if (response.ok) {
                                    return response.json();
                                } else {
                                    console.error('API response not OK:', response.status);
                                    throw new Error('Lỗi kết nối đến API: ' + response.status);
                                }
                            })
                            .then(data => {
                                console.log('API response:', data);

                                if (data.success && data.token) {
                                    // Lưu token vào localStorage
                                    localStorage.setItem('jwt_token', data.token);
                                    console.log('JWT token saved to localStorage');

                                    // Hiển thị thông báo thành công
                                    if (statusDiv) {
                                        statusDiv.classList.remove('bg-blue-100', 'border-blue-400', 'text-blue-700');
                                        statusDiv.classList.add('bg-green-100', 'border', 'border-green-400', 'text-green-700');
                                        statusDiv.textContent = 'Đăng nhập thành công. Đang chuyển hướng...';
                                    }

                                    // Chuyển hướng đến trang dashboard
                                    setTimeout(function() {
                                        window.location.href = '/dashboard';
                                    }, 1000);
                                } else {
                                    // Hiển thị thông báo lỗi
                                    if (statusDiv) {
                                        statusDiv.classList.remove('bg-blue-100', 'border-blue-400', 'text-blue-700');
                                        statusDiv.classList.add('bg-red-100', 'border', 'border-red-400', 'text-red-700');
                                        statusDiv.textContent = data.error || 'Đăng nhập thất bại';
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('Error calling API:', error);

                                // Hiển thị thông báo lỗi chi tiết hơn
                                if (statusDiv) {
                                    statusDiv.classList.remove('bg-blue-100', 'border-blue-400', 'text-blue-700');
                                    statusDiv.classList.add('bg-red-100', 'border', 'border-red-400', 'text-red-700');

                                    let errorMessage = 'Lỗi kết nối đến server';

                                    if (error.message.includes('Failed to fetch')) {
                                        errorMessage = 'Không thể kết nối đến máy chủ API. Vui lòng kiểm tra kết nối mạng.';
                                    } else if (error.message.includes('404')) {
                                        errorMessage = 'API không tồn tại (404). Vui lòng kiểm tra cấu hình API.';
                                    } else if (error.message.includes('401')) {
                                        errorMessage = 'Không có quyền truy cập API (401).';
                                    } else {
                                        errorMessage = 'Lỗi kết nối đến server: ' + error.message;
                                    }

                                    statusDiv.textContent = errorMessage;
                                }
                            });
                    });
                }
            });
        </script>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        try {
            // Kiểm tra nếu có thông báo force_logout từ server
            @if(session('force_logout'))
            console.log('Force logout detected');
            @endif

            // Xóa token JWT từ localStorage nếu có
            if (localStorage.getItem('jwt_token')) {
                console.log('Removing old JWT token from localStorage for security');
                localStorage.removeItem('jwt_token');
            }

            // Không kiểm tra phiên đăng nhập hiện tại vì lý do bảo mật
            // Người dùng phải luôn đăng nhập lại để đảm bảo an toàn
            console.log('Requiring full authentication for security reasons');
        } catch (error) {
            console.error('Error in login script:', error);
        }
    });
</script>
@endsection