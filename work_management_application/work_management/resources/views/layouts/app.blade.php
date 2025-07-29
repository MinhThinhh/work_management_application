<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Work Management</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <h1 class="text-xl font-bold text-gray-800">Work Management</h1>
                    @auth
                    <div class="hidden md:flex space-x-4">
                        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900">Dashboard</a>
                        <a href="{{ route('teams.index') }}" class="text-gray-600 hover:text-gray-900">Teams</a>
                        @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.users') }}" class="text-gray-600 hover:text-gray-900">Users</a>
                        <a href="{{ route('admin.kpi.index') }}" class="text-gray-600 hover:text-gray-900">KPI</a>
                        @endif
                    </div>
                    @endauth
                </div>

                @auth
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">{{ auth()->user()->email }}</span>
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm">{{ ucfirst(auth()->user()->role) }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-900">Logout</button>
                    </form>
                </div>
                @else
                <div class="space-x-4">
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">Login</a>
                </div>
                @endauth
            </div>
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