@extends('admin.layout')

@section('title', 'Dashboard - Admin')

@section('header', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <div class="stat-card">
        <div class="icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-label">Tổng số người dùng</div>
        <div class="stat-number">{{ $totalUsers }}</div>
        <div class="mt-4">
            <div class="flex justify-between items-center mb-2">
                <span class="text-gray-600">Admin:</span>
                <span class="badge badge-primary">{{ $usersByRole['admin'] }}</span>
            </div>
            <div class="progress-bar">
                <div class="progress-value" style="width: {{ ($usersByRole['admin'] / $totalUsers) * 100 }}%; background-color: #4f46e5;"></div>
            </div>

            <div class="flex justify-between items-center mt-3 mb-2">
                <span class="text-gray-600">Manager:</span>
                <span class="badge badge-info">{{ $usersByRole['manager'] }}</span>
            </div>
            <div class="progress-bar">
                <div class="progress-value" style="width: {{ ($usersByRole['manager'] / $totalUsers) * 100 }}%; background-color: #2563eb;"></div>
            </div>

            <div class="flex justify-between items-center mt-3 mb-2">
                <span class="text-gray-600">User:</span>
                <span class="badge badge-success">{{ $usersByRole['user'] }}</span>
            </div>
            <div class="progress-bar">
                <div class="progress-value" style="width: {{ ($usersByRole['user'] / $totalUsers) * 100 }}%; background-color: #16a34a;"></div>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="icon">
            <i class="fas fa-user-shield"></i>
        </div>
        <div class="stat-label">Quản lý người dùng</div>
        <div class="stat-number">
            <i class="fas fa-user-cog" style="font-size: 32px;"></i>
        </div>
        <p class="mb-4 text-gray-600">Quản lý tài khoản người dùng trong hệ thống.</p>
        <div class="mt-2">
            <a href="{{ route('admin.users') }}" class="btn btn-primary w-full">
                <i class="fas fa-users-cog"></i> Quản lý người dùng
            </a>
        </div>
    </div>

    <div class="stat-card">
        <div class="icon">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-label">Báo cáo & Thống kê</div>
        <div class="stat-number">
            <i class="fas fa-chart-pie" style="font-size: 32px;"></i>
        </div>
        <p class="mb-4 text-gray-600">Xem báo cáo chi tiết về người dùng và công việc.</p>
        <div class="mt-2">
            <a href="{{ route('admin.reports') }}" class="btn btn-primary w-full">
                <i class="fas fa-chart-bar"></i> Xem báo cáo
            </a>
        </div>
    </div>
</div>



<div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-bolt mr-2"></i>Tác vụ quản lý người dùng
            </h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('admin.create-user') }}" class="btn btn-success">
                <i class="fas fa-user-plus"></i> Tạo người dùng mới
            </a>
            <a href="{{ route('admin.users') }}" class="btn btn-primary">
                <i class="fas fa-users-cog"></i> Quản lý người dùng
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-info-circle mr-2"></i>Thông tin hệ thống
            </h2>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div class="p-4 bg-blue-50 rounded-lg">
                <div class="text-blue-600 font-semibold">PHP Version</div>
                <div class="text-gray-700">{{ phpversion() }}</div>
            </div>
            <div class="p-4 bg-green-50 rounded-lg">
                <div class="text-green-600 font-semibold">Laravel Version</div>
                <div class="text-gray-700">{{ app()->version() }}</div>
            </div>
            <div class="p-4 bg-purple-50 rounded-lg">
                <div class="text-purple-600 font-semibold">Môi trường</div>
                <div class="text-gray-700">{{ app()->environment() }}</div>
            </div>
            <div class="p-4 bg-yellow-50 rounded-lg">
                <div class="text-yellow-600 font-semibold">Thời gian máy chủ</div>
                <div class="text-gray-700">{{ now()->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="mt-8">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-chart-pie mr-2"></i>Phân bố người dùng
            </h2>
        </div>
        <div class="flex justify-center">
            <div style="width: 400px; height: 300px;">
                <canvas id="userDistributionChart"></canvas>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('userDistributionChart').getContext('2d');

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Admin', 'Manager', 'User'],
                datasets: [{
                    data: [
                        {{ $usersByRole['admin'] }},
                        {{ $usersByRole['manager'] }},
                        {{ $usersByRole['user'] }}
                    ],
                    backgroundColor: [
                        '#4f46e5',
                        '#2563eb',
                        '#16a34a'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });
</script>
@endsection
