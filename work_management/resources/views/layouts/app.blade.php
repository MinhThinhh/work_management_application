<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Work Management</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/jwt-handler.js') }}"></script>
    <script>
        // Configure Axios to include CSRF token with every request
        window.axios = axios;
        window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>
    @yield('styles')
</head>
<body class="bg-gray-100">
    <nav class="bg-white p-4 shadow">
        <div class="container mx-auto flex justify-between items-center">
            <a href="/" class="font-bold text-xl">Work Management</a>

            @auth
                <div class="flex items-center space-x-4">
                    @if(Auth::user()->role == 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800">Admin Dashboard</a>
                    @elseif(Auth::user()->role == 'manager')
                        <a href="{{ route('manager.dashboard') }}" class="text-blue-600 hover:text-blue-800">Manager Dashboard</a>
                    @endif
                </div>
            @else
            @endauth
        </div>
    </nav>

    <main class="container mx-auto mt-6 p-4">
        @yield('content')
    </main>

    <script>
        // Global error handler for Axios
        axios.interceptors.response.use(
            response => response,
            error => {
                if (error.response && error.response.status === 401) {
                    // Redirect to login if unauthorized
                    window.location = '/login';
                }
                return Promise.reject(error);
            }
        );
    </script>
    @yield('scripts')
</body>
</html>