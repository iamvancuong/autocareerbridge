@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-dark mb-1">Báo cáo & Thống kê Sinh viên</h2>
            <p class="text-muted mb-0 small">Theo dõi tình hình học tập và việc làm của sinh viên toàn trường</p>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="glass-card p-4 border-start border-primary border-4 text-center">
                <i class="fa-solid fa-users fa-2x text-primary mb-2"></i>
                <h3 class="display-5 fw-bold text-dark">{{ $totalStudents }}</h3>
                <span class="text-muted">Tổng Sinh viên</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card p-4 border-start border-warning border-4 text-center">
                <i class="fa-solid fa-paper-plane fa-2x text-warning mb-2"></i>
                <h3 class="display-5 fw-bold text-dark">{{ $totalApplications }}</h3>
                <span class="text-muted">Lượt Ứng tuyển</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card p-4 border-start border-success border-4 text-center">
                <i class="fa-solid fa-briefcase fa-2x text-success mb-2"></i>
                <h3 class="display-5 fw-bold text-dark">{{ $totalAccepted }}</h3>
                <span class="text-muted">Sinh viên có việc làm</span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Major Distribution Chart -->
        <div class="col-lg-6">
            <div class="glass-card p-4 h-100">
                <h5 class="text-dark border-bottom border-secondary pb-2 mb-4">Phân bố Sinh viên theo Chuyên ngành</h5>
                @if(empty($majorLabels))
                    <div class="text-center text-muted py-5">
                        <i class="fa-solid fa-chart-pie fa-3x mb-3 opacity-50"></i>
                        <p>Chưa có dữ liệu chuyên ngành.</p>
                    </div>
                @else
                    <div style="height: 300px; position: relative;">
                        <canvas id="majorChart"></canvas>
                    </div>
                @endif
            </div>
        </div>

        <!-- Application Status Chart -->
        <div class="col-lg-6">
            <div class="glass-card p-4 h-100">
                <h5 class="text-dark border-bottom border-secondary pb-2 mb-4">Tình trạng Ứng tuyển Việc làm</h5>
                @if(empty($statusLabels))
                    <div class="text-center text-muted py-5">
                        <i class="fa-solid fa-chart-bar fa-3x mb-3 opacity-50"></i>
                        <p>Chưa có đơn ứng tuyển nào từ sinh viên.</p>
                    </div>
                @else
                    <div style="height: 300px; position: relative;">
                        <canvas id="statusChart"></canvas>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        @if(!empty($majorLabels))
        // Major Chart
        const ctxMajor = document.getElementById('majorChart').getContext('2d');
        new Chart(ctxMajor, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($majorLabels) !!},
                datasets: [{
                    data: {!! json_encode($majorCounts) !!},
                    backgroundColor: [
                        '#0d6efd', '#6610f2', '#6f42c1', '#d63384', 
                        '#dc3545', '#fd7e14', '#ffc107', '#198754', '#20c997', '#0dcaf0'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right' }
                }
            }
        });
        @endif

        @if(!empty($statusLabels))
        // Status Chart
        const ctxStatus = document.getElementById('statusChart').getContext('2d');
        new Chart(ctxStatus, {
            type: 'bar',
            data: {
                labels: {!! json_encode($statusLabels) !!},
                datasets: [{
                    label: 'Số lượng hồ sơ',
                    data: {!! json_encode($statusCounts) !!},
                    backgroundColor: {!! json_encode($statusColors) !!},
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
        @endif
    });
</script>
@endsection
