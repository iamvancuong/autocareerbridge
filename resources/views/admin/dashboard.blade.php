@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h2 class="text-dark mb-4">Admin Dashboard</h2>
    <div class="row g-3 mb-5">
        <div class="col-md-3"><div class="glass-card p-4 border-start border-primary border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div><h3 class="text-dark mb-0">{{ $stats['companies'] }}</h3><span class="text-muted small">Doanh nghiệp</span></div>
                <i class="fa-solid fa-building fa-2x text-primary opacity-50"></i>
            </div>
        </div></div>
        <div class="col-md-3"><div class="glass-card p-4 border-start border-info border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div><h3 class="text-dark mb-0">{{ $stats['universities'] }}</h3><span class="text-muted small">Trường học</span></div>
                <i class="fa-solid fa-school fa-2x text-info opacity-50"></i>
            </div>
        </div></div>
        <div class="col-md-3"><div class="glass-card p-4 border-start border-success border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div><h3 class="text-dark mb-0">{{ $stats['students'] }}</h3><span class="text-muted small">Sinh viên</span></div>
                <i class="fa-solid fa-user-graduate fa-2x text-success opacity-50"></i>
            </div>
        </div></div>
        <div class="col-md-3"><div class="glass-card p-4 border-start border-warning border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div><h3 class="text-dark mb-0">{{ $stats['pending_accounts'] }}</h3><span class="text-muted small">Chờ duyệt TK</span></div>
                <i class="fa-solid fa-clock fa-2x text-warning opacity-50"></i>
            </div>
        </div></div>
        <div class="col-md-3"><div class="glass-card p-4 border-start border-danger border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div><h3 class="text-dark mb-0">{{ $stats['jobs_pending'] }}</h3><span class="text-muted small">Tin Chờ duyệt</span></div>
                <i class="fa-solid fa-file-circle-exclamation fa-2x text-danger opacity-50"></i>
            </div>
        </div></div>
        <div class="col-md-3"><div class="glass-card p-4 border-start border-success border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div><h3 class="text-dark mb-0">{{ $stats['jobs_approved'] }}</h3><span class="text-muted small">Tin Đã duyệt</span></div>
                <i class="fa-solid fa-briefcase fa-2x text-success opacity-50"></i>
            </div>
        </div></div>
        <div class="col-md-3"><div class="glass-card p-4 border-start border-secondary border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div><h3 class="text-dark mb-0">{{ $stats['applications'] }}</h3><span class="text-muted small">Lượt ứng tuyển</span></div>
                <i class="fa-solid fa-paper-plane fa-2x text-secondary opacity-50"></i>
            </div>
        </div></div>
        <div class="col-md-3"><div class="glass-card p-4 border-start border-primary border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div><h3 class="text-dark mb-0">{{ $stats['collaborations'] }}</h3><span class="text-muted small">Hợp tác đang hoạt động</span></div>
                <i class="fa-solid fa-handshake fa-2x text-primary opacity-50"></i>
            </div>
        </div></div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="glass-card p-4">
                <h6 class="text-dark border-bottom border-secondary pb-2 mb-3">Tin tuyển dụng mới nhất</h6>
                <ul class="list-unstyled">
                    @foreach($recentJobs as $job)
                    <li class="d-flex justify-content-between align-items-center py-2 border-bottom border-light">
                        <div><p class="mb-0 text-dark small fw-semibold">{{ $job->title }}</p><span class="text-muted" style="font-size:0.75rem">{{ $job->company->company_name ?? '' }}</span></div>
                        @if($job->is_approved)<span class="badge bg-success">Đã duyệt</span>@else<span class="badge bg-warning text-dark">Chờ duyệt</span>@endif
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('admin.jobs.index') }}" class="btn btn-outline-primary btn-sm mt-2">Duyệt tin tuyển dụng</a>
            </div>
        </div>
        <div class="col-md-6">
            <div class="glass-card p-4">
                <h6 class="text-dark border-bottom border-secondary pb-2 mb-3">Tài khoản đăng ký gần đây</h6>
                <ul class="list-unstyled">
                    @foreach($recentUsers as $user)
                    <li class="d-flex justify-content-between align-items-center py-2 border-bottom border-light">
                        <div><p class="mb-0 text-dark small fw-semibold">{{ $user->name }}</p><span class="text-muted" style="font-size:0.75rem">{{ $user->email }}</span></div>
                        <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm mt-2">Quản lý tài khoản</a>
            </div>
        </div>
    </div>
</div>
@endsection