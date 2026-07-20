<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Career Bridge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-light navbar-glass sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="{{ route('home') }}">
            <i class="fa-solid fa-bridge-water me-2"></i>CareerBridge
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('jobs.index') }}">Việc làm</a></li>
                @auth
                    @if(auth()->user()->role == 'student')
                        <li class="nav-item"><a class="nav-link" href="{{ route('student.resumes.index') }}">Quản lý CV</a></li>
                    @elseif(auth()->user()->hasCompanyRole())
                        <li class="nav-item"><a class="nav-link" href="{{ route('company.jobs.index') }}">Quản lý Tin</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('company.applications.index') }}">Duyệt Ứng viên</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Kết nối</a>
                            <ul class="dropdown-menu shadow">
                                <li><a class="dropdown-item" href="{{ route('universities.index') }}">Tìm Trường học</a></li>
                                <li><a class="dropdown-item" href="{{ route('collaborations.index') }}">Quản lý Hợp tác</a></li>
                            </ul>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('company.reports.index') }}">Báo cáo</a></li>
                        @if(auth()->user()->role == 'company')
                            <li class="nav-item"><a class="nav-link text-primary fw-semibold" href="{{ route('company.hirings.index') }}">Nhân sự HR</a></li>
                        @endif
                    @elseif(auth()->user()->hasUniversityRole())
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Quản lý</a>
                            <ul class="dropdown-menu shadow">
                                <li><a class="dropdown-item" href="{{ route('university.students.index') }}">Sinh viên</a></li>
                                <li><a class="dropdown-item" href="{{ route('university.workshops.index') }}">Workshop</a></li>
                                <li><a class="dropdown-item" href="{{ route('university.majors.index') }}">Ngành đào tạo</a></li>
                                @if(auth()->user()->role == 'university')
                                    <li><a class="dropdown-item text-primary" href="{{ route('university.academic_affairs.index') }}">Tài khoản Giáo vụ</a></li>
                                @endif
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Kết nối</a>
                            <ul class="dropdown-menu shadow">
                                <li><a class="dropdown-item" href="{{ route('companies.index') }}">Tìm Doanh nghiệp</a></li>
                                <li><a class="dropdown-item" href="{{ route('collaborations.index') }}">Quản lý Hợp tác</a></li>
                            </ul>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('university.reports.index') }}">Báo cáo</a></li>
                    @elseif(auth()->user()->role == 'admin')
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.users.index') }}">Tài khoản</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.jobs.index') }}">Duyệt Tin</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.catalog.index') }}">Danh mục</a></li>
                    @endif
                @endauth
            </ul>
            <ul class="navbar-nav align-items-center">
                @auth
                    @php
                        $unreadNotifs = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count();
                    @endphp
                    <li class="nav-item me-3 d-flex align-items-center">
                        <a class="nav-link position-relative" href="{{ route('notifications.index') }}">
                            <i class="fa-solid fa-bell fa-lg text-dark"></i>
                            @if($unreadNotifs > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                                    {{ $unreadNotifs > 99 ? '99+' : $unreadNotifs }}
                                </span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                            @if(auth()->user()->avatar)
                                <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="Avatar" class="rounded-circle object-fit-cover border border-primary shadow-sm" style="width: 32px; height: 32px;">
                            @else
                                <i class="fa-solid fa-circle-user text-primary fa-lg"></i>
                            @endif
                            <span class="text-dark fw-semibold">{{ auth()->user()->name }}</span>
                            <span class="badge bg-primary">{{ ucfirst(auth()->user()->role) }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="fa-solid fa-user me-2"></i>Hồ sơ của tôi</a></li>
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="fa-solid fa-gauge me-2"></i>Dashboard</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">@csrf
                                    <button type="submit" class="dropdown-item text-danger"><i class="fa-solid fa-right-from-bracket me-2"></i>Đăng xuất</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item me-2"><a class="btn btn-outline-primary" href="{{ route('login') }}">Đăng nhập</a></li>
                    <li class="nav-item"><a class="btn btn-gradient" href="{{ route('register') }}">Đăng ký</a></li>
                @endauth
            </ul>
            {{-- Dev-only quick login bar --}}
            @env('local')
            @guest
            <div class="ms-3 d-flex gap-1">
                <a href="{{ route('mock.login', ['role' => 'admin']) }}" class="btn btn-sm btn-outline-warning" title="Dev: Login Admin">A</a>
                <a href="{{ route('mock.login', ['role' => 'company']) }}" class="btn btn-sm btn-outline-primary" title="Dev: Login Company">C</a>
                <a href="{{ route('mock.login', ['role' => 'university']) }}" class="btn btn-sm btn-outline-info" title="Dev: Login University">U</a>
                <a href="{{ route('mock.login', ['role' => 'student']) }}" class="btn btn-sm btn-outline-success" title="Dev: Login Student">S</a>
            </div>
            @endguest
            @endenv
        </div>
    </div>
</nav>

<main class="py-4">
    @if(session('success'))
        <div class="container mb-3">
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="container mb-3">
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="fa-solid fa-circle-xmark me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif
    @if($errors->any())
        <div class="container mb-3">
            <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @yield('content')
</main>

{{-- ===================== FOOTER ===================== --}}
<footer class="bg-dark text-white pt-5 pb-3 mt-auto">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-4 col-md-6">
                <h5 class="text-white mb-3 fw-bold"><i class="fa-solid fa-bridge-water me-2 text-primary"></i>Auto Career Bridge</h5>
                <p class="text-secondary small" style="line-height: 1.8;">
                    Nền tảng tiên phong kết nối sinh viên với mạng lưới doanh nghiệp thông qua sự hợp tác trực tiếp từ các trường Đại học. Tích hợp AI để tối ưu hóa quy trình tuyển dụng.
                </p>
                <div class="d-flex gap-3 mt-3">
                    <a href="#" class="text-secondary text-decoration-none hover-primary"><i class="fa-brands fa-facebook fa-lg"></i></a>
                    <a href="#" class="text-secondary text-decoration-none hover-primary"><i class="fa-brands fa-linkedin fa-lg"></i></a>
                    <a href="#" class="text-secondary text-decoration-none hover-primary"><i class="fa-brands fa-github fa-lg"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <h6 class="text-white mb-3">Dành cho Sinh viên</h6>
                <ul class="list-unstyled text-secondary small">
                    <li class="mb-2"><a href="{{ route('jobs.index') }}" class="text-secondary text-decoration-none hover-primary">Việc làm nổi bật</a></li>
                    <li class="mb-2"><a href="#" class="text-secondary text-decoration-none hover-primary">Quản lý CV</a></li>
                    <li class="mb-2"><a href="{{ route('companies.index') }}" class="text-secondary text-decoration-none hover-primary">Danh sách Công ty</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6">
                <h6 class="text-white mb-3">Đối tác</h6>
                <ul class="list-unstyled text-secondary small">
                    <li class="mb-2"><a href="{{ route('universities.index') }}" class="text-secondary text-decoration-none hover-primary">Mạng lưới Trường học</a></li>
                    <li class="mb-2"><a href="{{ route('workshops.index') }}" class="text-secondary text-decoration-none hover-primary">Sự kiện Workshop</a></li>
                    <li class="mb-2"><a href="#" class="text-secondary text-decoration-none hover-primary">Đăng ký Hợp tác</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-6">
                <h6 class="text-white mb-3">Liên hệ</h6>
                <ul class="list-unstyled text-secondary small">
                    <li class="mb-2"><i class="fa-solid fa-location-dot me-2 text-primary"></i>Lê Duẩn, Quảng Trị</li>
                    <li class="mb-2"><i class="fa-solid fa-envelope me-2 text-primary"></i>contact@autocareerbridge.edu.vn</li>
                    <li class="mb-2"><i class="fa-solid fa-phone me-2 text-primary"></i>(028) 38 123 456</li>
                </ul>
            </div>
        </div>
        <hr class="border-secondary mt-4 mb-3">
        <div class="row text-secondary small">
            <div class="col-md-6 text-center text-md-start">
                &copy; {{ date('Y') }} Auto Career Bridge. All rights reserved.
            </div>
            <div class="col-md-6 text-center text-md-end mt-2 mt-md-0">
                Phát triển đồ án bởi <i class="fa-solid fa-heart text-danger mx-1"></i> Sinh viên IT
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>