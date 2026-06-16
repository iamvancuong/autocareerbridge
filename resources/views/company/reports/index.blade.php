@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-dark"><i class="fa-solid fa-chart-pie me-2 text-primary"></i>Báo cáo Thống kê</h2>
    </div>

    <div class="row g-4">
        <!-- Status Chart -->
        <div class="col-md-4">
            <div class="glass-card p-4 h-100">
                <h5 class="text-dark mb-4 text-center">Tỷ lệ Trạng thái CV</h5>
                <div style="position: relative; height:250px; width:100%">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Trend Chart -->
        <div class="col-md-8">
            <div class="glass-card p-4 h-100">
                <h5 class="text-dark mb-4">Lượng CV Nộp (30 Ngày Qua)</h5>
                <div style="position: relative; height:250px; width:100%">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Jobs Chart -->
        <div class="col-md-12">
            <div class="glass-card p-4">
                <h5 class="text-dark mb-4">Top Việc làm thu hút ứng viên</h5>
                <div style="position: relative; height:300px; width:100%">
                    <canvas id="topJobsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Colors
    const colors = {
        primary: '#0d6efd',
        success: '#198754',
        warning: '#ffc107',
        danger: '#dc3545',
        info: '#0dcaf0'
    };

    // 1. Status Chart
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Chờ duyệt', 'Đang xem xét', 'Chấp nhận', 'Từ chối'],
            datasets: [{
                data: {{ json_encode($statusData) }},
                backgroundColor: [colors.warning, colors.info, colors.success, colors.danger],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // 2. Trend Chart
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [{
                label: 'Số lượng CV',
                data: {{ json_encode($trendData) }},
                borderColor: colors.primary,
                backgroundColor: colors.primary + '33', // 20% opacity
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });

    // 3. Top Jobs Chart
    new Chart(document.getElementById('topJobsChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($jobLabels) !!},
            datasets: [{
                label: 'Số lượng ứng viên',
                data: {{ json_encode($jobData) }},
                backgroundColor: colors.primary,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });
});
</script>
@endsection