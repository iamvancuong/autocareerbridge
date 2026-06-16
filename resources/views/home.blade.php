@extends('layouts.app')

@section('content')

{{-- ===================== HERO ===================== --}}
<style>
    .hero-bg {
        background-color: #ffffff;
        background-image: radial-gradient(#e2e8f0 1.5px, transparent 1.5px);
        background-size: 35px 35px;
        position: relative;
        overflow: hidden;
    }
    .hero-glow-1 {
        position: absolute;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(13,110,253,0.1) 0%, rgba(255,255,255,0) 70%);
        top: -150px;
        left: -150px;
        border-radius: 50%;
        animation: floatOrb 8s ease-in-out infinite;
        z-index: 0;
    }
    .hero-glow-2 {
        position: absolute;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(25,135,84,0.1) 0%, rgba(255,255,255,0) 70%);
        bottom: -150px;
        right: -100px;
        border-radius: 50%;
        animation: floatOrb 12s ease-in-out infinite reverse;
        z-index: 0;
    }
    @keyframes floatOrb {
        0% { transform: translate(0, 0) scale(1); }
        33% { transform: translate(30px, -50px) scale(1.1); }
        66% { transform: translate(-20px, 20px) scale(0.9); }
        100% { transform: translate(0, 0) scale(1); }
    }
    .hero-content {
        position: relative;
        z-index: 1;
    }
    .hover-lift { transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .hover-lift:hover { transform: translateY(-5px); box-shadow: 0 1rem 3rem rgba(0,0,0,.15)!important; }
</style>

<div class="d-flex align-items-center justify-content-center hero-bg" style="min-height: calc(100vh - 120px);">
    <div class="hero-glow-1"></div>
    <div class="hero-glow-2"></div>
    
    <div class="container text-center py-5 hero-content">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-4 py-2 mb-4 rounded-pill shadow-sm" style="animation: floatOrb 4s ease-in-out infinite;">
                    <i class="fa-solid fa-bolt text-warning me-2"></i>Nền tảng Cầu nối Trường học - Doanh nghiệp
                </span>
                <h1 class="display-3 fw-bold mb-4 text-dark" style="line-height: 1.3;">
                    Kết nối <span class="text-primary position-relative">Doanh nghiệp
                        <svg class="position-absolute w-100" style="bottom: -5px; left: 0; height: 10px;" viewBox="0 0 100 10" preserveAspectRatio="none"><path d="M0 5 Q 50 15 100 5" stroke="#0d6efd" stroke-width="3" fill="transparent"/></svg>
                    </span><br>
                    & <span class="text-success position-relative">Nhà trường
                        <svg class="position-absolute w-100" style="bottom: -5px; left: 0; height: 10px;" viewBox="0 0 100 10" preserveAspectRatio="none"><path d="M0 5 Q 50 -5 100 5" stroke="#198754" stroke-width="3" fill="transparent"/></svg>
                    </span>
                </h1>
                <p class="lead text-muted mb-5 px-md-4">
                    Auto Career Bridge giúp sinh viên tìm việc làm phù hợp, hỗ trợ HR chấm điểm CV bằng AI và thiết lập mạng lưới hợp tác vững chắc giữa Doanh nghiệp và Trường đại học.
                </p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="{{ route('jobs.index') }}" class="btn btn-primary btn-lg px-5 rounded-pill shadow-lg hover-lift">
                        Khám phá việc làm <i class="fa-solid fa-arrow-right ms-2"></i>
                    </a>
                    <a href="#workshops" class="btn bg-white border-secondary btn-lg px-5 rounded-pill shadow-sm hover-lift" style="color: #6c757d !important;">
                        Xem Workshop <i class="fa-solid fa-calendar ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===================== INTRODUCTION (VỀ CHÚNG TÔI) ===================== --}}
<div class="container py-5 mt-3 mb-4">
    <div class="row align-items-center g-5">
        <div class="col-lg-6">
            <div class="position-relative">
                <!-- Decorative elements -->
                <div class="position-absolute top-0 start-0 translate-middle w-50 h-50 bg-primary rounded-circle" style="opacity: 0.05; filter: blur(40px);"></div>
                <img src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="About Us" class="img-fluid rounded-4 shadow-lg position-relative z-1" style="object-fit: cover; height: 400px; width: 100%;">
                <div class="position-absolute bottom-0 end-0 translate-middle-y bg-white p-3 rounded-4 shadow-lg z-2 me-n4 d-none d-md-block">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                            <i class="fa-solid fa-users-viewfinder fa-2x"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold">10,000+</h4>
                            <span class="text-muted small">Sinh viên tin dùng</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <span class="text-primary fw-bold text-uppercase tracking-wider small"><i class="fa-solid fa-circle-info me-2"></i>Giới thiệu về nền tảng</span>
            <h2 class="display-6 fw-bold text-dark mt-2 mb-4">Cách mạng hóa quy trình kết nối Việc làm & Thực tập</h2>
            <p class="text-muted mb-4" style="line-height: 1.8;">
                <strong>Auto Career Bridge</strong> không chỉ là một cổng thông tin việc làm thông thường. Chúng tôi tiên phong xây dựng một hệ sinh thái ba bên khép kín: <strong>Nhà trường - Sinh viên - Doanh nghiệp</strong>. 
            </p>
            <p class="text-muted mb-4" style="line-height: 1.8;">
                Bằng việc số hóa toàn bộ mạng lưới hợp tác (University-Industry Collaboration) và áp dụng công nghệ Trí tuệ nhân tạo (AI) trong khâu sàng lọc hồ sơ, hệ thống giúp tự động hóa quy trình tuyển chọn, giảm thiểu rủi ro lệch pha kỹ năng và tối đa hóa cơ hội việc làm ngay từ khi sinh viên còn ngồi trên ghế nhà trường.
            </p>
            <div class="row g-4 mt-2">
                <div class="col-sm-6">
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-success"><i class="fa-solid fa-circle-check fa-xl"></i></div>
                        <span class="fw-semibold text-dark">Kết nối Trực tiếp</span>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-primary"><i class="fa-solid fa-circle-check fa-xl"></i></div>
                        <span class="fw-semibold text-dark">AI Phân tích CV</span>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-warning"><i class="fa-solid fa-circle-check fa-xl"></i></div>
                        <span class="fw-semibold text-dark">Dữ liệu Chuẩn hóa</span>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-info"><i class="fa-solid fa-circle-check fa-xl"></i></div>
                        <span class="fw-semibold text-dark">Matching Tự động</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===================== FEATURES ===================== --}}
<div class="container pb-5">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="glass-card p-4 h-100 text-center">
                <div class="mb-3"><i class="fa-solid fa-robot fa-3x text-primary"></i></div>
                <h3 class="h5 text-dark">AI Chấm điểm CV</h3>
                <p class="text-muted small">Phân tích và đánh giá CV tự động bằng OpenAI, giúp HR tiết kiệm thời gian sàng lọc ứng viên.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card p-4 h-100 text-center">
                <div class="mb-3"><i class="fa-solid fa-handshake fa-3x text-success"></i></div>
                <h3 class="h5 text-dark">Mạng lưới Hợp tác</h3>
                <p class="text-muted small">Doanh nghiệp và Trường học kết nối trực tiếp, tạo cơ hội thực tập và workshop độc quyền.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card p-4 h-100 text-center">
                <div class="mb-3"><i class="fa-solid fa-bullseye fa-3x text-warning"></i></div>
                <h3 class="h5 text-dark">Job Matching</h3>
                <p class="text-muted small">Gợi ý việc làm phù hợp nhất dựa trên kỹ năng và ngành học của sinh viên.</p>
            </div>
        </div>
    </div>
</div>

{{-- ===================== FEATURED JOBS ===================== --}}
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-dark mb-1">Việc làm nổi bật</h2>
            <p class="text-muted mb-0 small">Các cơ hội việc làm mới nhất từ doanh nghiệp đối tác</p>
        </div>
        <a href="{{ route('jobs.index') }}" class="btn btn-outline-primary rounded-pill">Xem tất cả <i class="fa-solid fa-arrow-right ms-1"></i></a>
    </div>
    <div class="row g-3">
        @foreach($featuredJobs as $job)
        <div class="col-md-4">
            <div class="glass-card p-4 h-100 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h5 class="text-dark h6 mb-0">{{ $job->title }}</h5>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary">{{ $job->major->name ?? 'IT' }}</span>
                </div>
                <p class="text-muted small mb-1"><i class="fa-solid fa-building me-1"></i>{{ $job->company->company_name ?? '' }}</p>
                <p class="text-muted small flex-grow-1" style="white-space: pre-line;">{{ Str::limit($job->description, 80) }}</p>
                <a href="{{ route('jobs.show', $job->id) }}" class="btn btn-sm btn-outline-primary rounded-pill mt-2">Xem chi tiết</a>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- ===================== WORKSHOPS ===================== --}}
<div class="bg-light py-5" id="workshops" style="border-radius: 24px; margin: 0 12px;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="text-dark mb-1">Sự kiện & Workshop</h2>
                <p class="text-muted mb-0 small">Các sự kiện kết nối từ các trường đại học đối tác</p>
            </div>
            <a href="{{ route('workshops.index') }}" class="btn btn-outline-success rounded-pill">Xem tất cả <i class="fa-solid fa-arrow-right ms-1"></i></a>
        </div>

        @if($upcomingWorkshops->count())
        <div class="row g-4">
            @foreach($upcomingWorkshops as $ws)
            <div class="col-md-4">
                <div class="glass-card p-4 h-100 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2">
                            <i class="fa-solid fa-calendar me-1"></i>
                            {{ $ws->date ? \Carbon\Carbon::parse($ws->date)->format('d/m/Y') : 'Sắp có' }}
                        </span>
                        <span class="badge bg-success">Sắp diễn ra</span>
                    </div>
                    <h5 class="text-dark h6 mb-2">{{ $ws->title }}</h5>
                    <p class="text-muted small flex-grow-1">{{ Str::limit($ws->description, 100) }}</p>
                    <div class="border-top border-light pt-2 mt-2">
                        <span class="text-muted small"><i class="fa-solid fa-school text-primary me-1"></i>{{ $ws->university->university_name ?? '' }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-4 text-muted">
            <i class="fa-solid fa-calendar-plus fa-3x mb-3 d-block opacity-50"></i>
            <p>Chưa có Workshop sắp diễn ra. Hãy quay lại sau!</p>
        </div>
        @endif
    </div>
</div>

<div class="py-4"></div>

@endsection