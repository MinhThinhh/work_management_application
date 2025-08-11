@extends('admin.layout')

@section('title', 'KPI Dashboard')

@section('content')
<div class="container-fluid">
    <!-- KPI Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ number_format($kpiData['completion_rate'], 1) }}%</h4>
                            <p class="mb-0">Tỷ lệ hoàn thành TB</p>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-tasks fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ number_format($kpiData['quality_score'], 1) }}</h4>
                            <p class="mb-0">Điểm chất lượng TB</p>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-star fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ number_format($kpiData['on_time_rate'], 1) }}%</h4>
                            <p class="mb-0">Tỷ lệ đúng hạn</p>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $kpiData['top_performers'] }}</h4>
                            <p class="mb-0">Nhân viên xuất sắc</p>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Team Performance Chart -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Hiệu suất theo Team</h3>
                </div>
                <div class="card-body">
                    <canvas id="teamPerformanceChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Top Performers -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3>Top Performers</h3>
                </div>
                <div class="card-body">
                    @forelse($topPerformers as $index => $performer)
                    <div class="d-flex align-items-center mb-3">
                        <div class="rank-badge me-3">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0">{{ $performer['name'] }}</h6>
                            <small class="text-muted">{{ $performer['score'] }}% hoàn thành</small>
                            @if(isset($performer['team']))
                            <br><small class="text-info">Team: {{ $performer['team'] }}</small>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="text-muted">Chưa có dữ liệu</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Statistics -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>Thống kê theo trạng thái</h3>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>Thống kê theo độ ưu tiên</h3>
                </div>
                <div class="card-body">
                    <canvas id="priorityChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Details -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Chi tiết Teams</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Team</th>
                                    <th>Leader</th>
                                    <th>Số thành viên</th>
                                    <th>Tổng tasks</th>
                                    <th>Hoàn thành</th>
                                    <th>Đang thực hiện</th>
                                    <th>Chờ xử lý</th>
                                    <th>Tỷ lệ hoàn thành</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($teamDetails))
                                @foreach($teamDetails as $team)
                                <tr>
                                    <td><strong>{{ $team['name'] }}</strong></td>
                                    <td>{{ $team['leader'] }}</td>
                                    <td>{{ $team['total_members'] }}</td>
                                    <td>{{ $team['total_tasks'] }}</td>
                                    <td><span class="badge bg-success">{{ $team['completed_tasks'] }}</span></td>
                                    <td><span class="badge bg-info">{{ $team['in_progress_tasks'] }}</span></td>
                                    <td><span class="badge bg-warning">{{ $team['pending_tasks'] }}</span></td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar"
                                                 style="width: {{ $team['completion_rate'] }}%"
                                                 aria-valuenow="{{ $team['completion_rate'] }}"
                                                 aria-valuemin="0" aria-valuemax="100">
                                                {{ $team['completion_rate'] }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @if(count($team['members']) > 0)
                                <tr>
                                    <td colspan="8" class="bg-light">
                                        <small><strong>Thành viên:</strong>
                                        @foreach($team['members'] as $member)
                                            <span class="badge bg-secondary me-1">{{ $member }}</span>
                                        @endforeach
                                        </small>
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.rank-badge {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Team Performance Chart
const teamCtx = document.getElementById('teamPerformanceChart').getContext('2d');
new Chart(teamCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($teamPerformance['labels']) !!},
        datasets: [{
            label: 'Tỷ lệ hoàn thành (%)',
            data: {!! json_encode($teamPerformance['data']) !!},
            backgroundColor: 'rgba(54, 162, 235, 0.8)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                max: 100
            }
        }
    }
});

// Status Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($statusStats['labels']) !!},
        datasets: [{
            data: {!! json_encode($statusStats['data']) !!},
            backgroundColor: [
                '#ffc107',
                '#17a2b8', 
                '#28a745'
            ]
        }]
    },
    options: {
        responsive: true
    }
});

// Priority Chart
const priorityCtx = document.getElementById('priorityChart').getContext('2d');
new Chart(priorityCtx, {
    type: 'pie',
    data: {
        labels: {!! json_encode($priorityStats['labels']) !!},
        datasets: [{
            data: {!! json_encode($priorityStats['data']) !!},
            backgroundColor: [
                '#28a745',
                '#ffc107',
                '#dc3545'
            ]
        }]
    },
    options: {
        responsive: true
    }
});
</script>
@endsection
