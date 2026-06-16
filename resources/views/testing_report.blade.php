@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row mb-4 text-center">
        <div class="col-12">
            <h1 class="display-5 fw-bold text-dark mb-3">
                <i class="fa-solid fa-microchip text-primary me-2"></i>Báo Cáo Kiểm Thử (QA Report)
            </h1>
            <p class="lead text-muted">Dữ liệu kiểm thử hệ thống được trích xuất trực tiếp từ mã nguồn</p>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav nav-pills mb-5 justify-content-center" id="reportTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4 shadow-sm" id="auto-tab" data-bs-toggle="tab" data-bs-target="#auto" type="button" role="tab">
                <i class="fa-solid fa-robot me-2"></i>Kiểm thử Tự động (Unit Test)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 shadow-sm ms-3" id="manual-tab" data-bs-toggle="tab" data-bs-target="#manual" type="button" role="tab">
                <i class="fa-solid fa-hand-pointer me-2"></i>Kiểm thử Thủ công (Manual Testing)
            </button>
        </li>
    </ul>

    <div class="tab-content" id="reportTabsContent">
        <!-- TAB 1: AUTOMATED UNIT TEST (DYNAMIC) -->
        <div class="tab-pane fade show active" id="auto" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">Live PHPUnit Execution</h4>
                <button id="runTestsBtn" class="btn btn-primary rounded-pill px-4 shadow-sm">
                    <i class="fa-solid fa-play me-2"></i>Chạy Test Ngay (Run Tests)
                </button>
            </div>

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card border-0 bg-primary bg-opacity-10 text-primary shadow-sm rounded-4 h-100 p-4 text-center">
                        <h1 class="display-4 fw-bold mb-0" id="statTotal">-</h1>
                        <p class="mb-0 fw-semibold">Tổng số Bài Test</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 bg-success bg-opacity-10 text-success shadow-sm rounded-4 h-100 p-4 text-center">
                        <h1 class="display-4 fw-bold mb-0" id="statPassed">-</h1>
                        <p class="mb-0 fw-semibold">Passed (Thành công)</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 bg-danger bg-opacity-10 text-danger shadow-sm rounded-4 h-100 p-4 text-center">
                        <h1 class="display-4 fw-bold mb-0" id="statFailed">-</h1>
                        <p class="mb-0 fw-semibold">Failed (Lỗi phát hiện)</p>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-dark text-white p-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-terminal me-2"></i>Console Output</h5>
                    <span class="badge bg-success" id="timestampBadge">Chưa chạy test</span>
                </div>
                <div class="card-body bg-dark text-success p-4" style="max-height: 500px; overflow-y: auto; position: relative;">
                    <div id="loadingOverlay" class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-75 d-none d-flex flex-column justify-content-center align-items-center z-3">
                        <div class="spinner-border text-success mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
                        <h5 class="text-white">Đang thực thi lệnh PHPUnit...</h5>
                    </div>
                    <pre class="mb-0" id="consoleOutput" style="font-family: 'Consolas', 'Courier New', monospace; font-size: 0.9rem; min-height: 200px;">Bấm nút "Chạy Test Ngay" để xem kết quả.</pre>
                </div>
            </div>
        </div>

        <!-- TAB 2: MANUAL TESTING (HARDCODED CHARTS) -->
        <div class="tab-pane fade" id="manual" role="tabpanel">
            <!-- Charts Row -->
            <div class="row g-4 mb-5">
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden glass-card">
                        <div class="card-header bg-white border-0 pt-4 pb-0 text-center">
                            <h5 class="fw-bold text-dark">Lỗi theo Phân hệ chức năng</h5>
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center p-4">
                            <canvas id="moduleChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden glass-card">
                        <div class="card-header bg-white border-0 pt-4 pb-0 text-center">
                            <h5 class="fw-bold text-dark">Lỗi theo Phân loại Nguồn gốc</h5>
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center p-4">
                            <canvas id="typeChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden glass-card">
                        <div class="card-header bg-white border-0 pt-4 pb-0 text-center">
                            <h5 class="fw-bold text-dark">Lỗi theo Kích thước màn hình (Google Chrome)</h5>
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center p-4">
                            <canvas id="browserChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Troubleshooting Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-header bg-primary bg-gradient text-white p-3">
                            <h5 class="mb-0 fw-bold"><i class="fa-solid fa-clipboard-check me-2"></i>Nhật ký Xử lý Sự cố tiêu biểu (Troubleshooting Log)</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="px-4 py-3">Mã Test Case</th>
                                            <th class="py-3">Mô tả Lỗi (Lần 1 - FAIL)</th>
                                            <th class="py-3">Giải pháp Khắc phục (Coding)</th>
                                            <th class="px-4 py-3 text-center">Kết quả (Lần 2)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="px-4 fw-bold text-primary">TC_06</td>
                                            <td>Upload file PDF vào ô Avatar gây lỗi <strong>500 Server Error</strong>.</td>
                                            <td>Thêm rule <code>validate(['avatar' => 'image'])</code> tại ProfileController để chặn file tài liệu.</td>
                                            <td class="px-4 text-center"><span class="badge bg-success rounded-pill px-3 py-2"><i class="fa-solid fa-check me-1"></i>PASS</span></td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 fw-bold text-primary">TC_29</td>
                                            <td>Xóa Danh mục Lĩnh vực bị lỗi <strong>Integrity constraint violation</strong> do dính khóa ngoại.</td>
                                            <td>Bổ sung hàm <code>cascadeOnDelete()</code> vào file Migration của bảng Ngành học (Majors).</td>
                                            <td class="px-4 text-center"><span class="badge bg-success rounded-pill px-3 py-2"><i class="fa-solid fa-check me-1"></i>PASS</span></td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 fw-bold text-primary">TC_30</td>
                                            <td>Lỗi giao diện <strong>Sticky Footer</strong>: Footer lơ lửng giữa màn hình khi trang ít nội dung.</td>
                                            <td>Cấu hình lại CSS Flexbox: Thêm class <code>min-vh-100 d-flex flex-column</code> vào thẻ Body.</td>
                                            <td class="px-4 text-center"><span class="badge bg-success rounded-pill px-3 py-2"><i class="fa-solid fa-check me-1"></i>PASS</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Thêm thư viện Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Chart.defaults.font.family = "'Inter', 'Segoe UI', Roboto, Helvetica, Arial, sans-serif";
        Chart.defaults.color = '#6c757d';

        // AJAX Run Tests logic
        const runBtn = document.getElementById('runTestsBtn');
        const loadingOverlay = document.getElementById('loadingOverlay');
        const consoleOutput = document.getElementById('consoleOutput');
        
        runBtn.addEventListener('click', function() {
            // UI Loading state
            runBtn.disabled = true;
            runBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Đang chạy...';
            loadingOverlay.classList.remove('d-none');
            
            fetch('{{ route("api.run.tests") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Update stats
                document.getElementById('statTotal').innerText = data.testsCount;
                document.getElementById('statPassed').innerText = data.passedCount;
                document.getElementById('statFailed').innerText = data.failedCount;
                
                // Update Console Output
                consoleOutput.innerText = data.output;
                
                // Update Timestamp
                document.getElementById('timestampBadge').innerText = 'Trích xuất lúc: ' + data.time;
            })
            .catch(error => {
                consoleOutput.innerText = "Lỗi kết nối hoặc timeout khi chạy PHPUnit.\n" + error;
            })
            .finally(() => {
                // Restore UI state
                runBtn.disabled = false;
                runBtn.innerHTML = '<i class="fa-solid fa-play me-2"></i>Chạy Test Lại';
                loadingOverlay.classList.add('d-none');
            });
        });

        // Initialize charts only when tab is shown to prevent rendering bugs
        let chartsRendered = false;
        
        const manualTab = document.getElementById('manual-tab');
        manualTab.addEventListener('shown.bs.tab', function () {
            if(chartsRendered) return;
            chartsRendered = true;

            new Chart(document.getElementById('moduleChart'), {
                type: 'pie',
                data: {
                    labels: ['Doanh nghiệp', 'Quản trị (Admin)', 'Sinh viên', 'Auth'],
                    datasets: [{
                        data: [6, 4, 3, 2],
                        backgroundColor: ['#0dcaf0', '#ffc107', '#fd7e14', '#6f42c1'],
                        borderWidth: 2,
                        hoverOffset: 6
                    }]
                },
                options: { plugins: { legend: { position: 'bottom' } } }
            });

            new Chart(document.getElementById('typeChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Lỗi Giao diện', 'Lỗi Backend', 'Lỗi Database'],
                    datasets: [{
                        data: [7, 5, 3],
                        backgroundColor: ['#e83e8c', '#20c997', '#0d6efd'],
                        borderWidth: 2,
                        cutout: '65%',
                        hoverOffset: 6
                    }]
                },
                options: { plugins: { legend: { position: 'bottom' } } }
            });

            new Chart(document.getElementById('browserChart'), {
                type: 'pie',
                data: {
                    labels: ['Mobile (Phone - 390x844)', 'Desktop (1080p)', 'Tablet (iPad - 768x1024)'],
                    datasets: [{
                        data: [8, 4, 3],
                        backgroundColor: ['#0d6efd', '#ffc107', '#dc3545'],
                        borderWidth: 2,
                        hoverOffset: 6
                    }]
                },
                options: { 
                    plugins: { 
                        legend: { position: 'bottom' } 
                    } 
                }
            });
        });
    });
</script>
@endsection
