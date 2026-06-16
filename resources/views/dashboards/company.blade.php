@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-dark mb-1">Dashboard - {{ auth()->user()->company->company_name ?? auth()->user()->name }}</h2>
            <p class="text-muted mb-0 small">Tổng quan hoạt động tuyển dụng của bạn</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="glass-card p-4 border-start border-primary border-4">
            <h3 class="text-dark mb-0">{{ $stats['total_jobs'] }}</h3>
            <span class="text-muted small">Tin tuyển dụng</span>
        </div></div>
        <div class="col-md-3"><div class="glass-card p-4 border-start border-warning border-4">
            <h3 class="text-dark mb-0">{{ $stats['pending_jobs'] }}</h3>
            <span class="text-muted small">Đang chờ duyệt</span>
        </div></div>
        <div class="col-md-3"><div class="glass-card p-4 border-start border-success border-4">
            <h3 class="text-dark mb-0">{{ $stats['total_applications'] }}</h3>
            <span class="text-muted small">Tổng ứng viên</span>
        </div></div>
        <div class="col-md-3"><div class="glass-card p-4 border-start border-info border-4">
            <h3 class="text-dark mb-0">{{ $stats['collaborations'] }}</h3>
            <span class="text-muted small">Trường đang hợp tác</span>
        </div></div>
    </div>

    <div class="row g-4">
        {{-- Recent Jobs --}}
        <div class="col-md-6">
            <div class="glass-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-dark mb-0 fw-semibold">Tin tuyển dụng gần đây</h6>
                    <a href="{{ route('company.jobs.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">Quản lý</a>
                </div>
                @foreach($recentJobs as $job)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-light">
                    <div>
                        <p class="mb-0 text-dark small fw-semibold">{{ $job->title }}</p>
                        <span class="text-muted" style="font-size:0.75rem">{{ $job->created_at->format('d/m/Y') }}</span>
                    </div>
                    @if($job->is_approved)<span class="badge bg-success">Đã duyệt</span>
                    @else<span class="badge bg-warning text-dark">Chờ duyệt</span>@endif
                </div>
                @endforeach
                @if($recentJobs->isEmpty())<p class="text-muted text-center py-3 small">Chưa có tin nào. <a href="{{ route('company.jobs.create') }}">Đăng tin ngay</a></p>@endif
            </div>
        </div>

        {{-- Recent Applications --}}
        <div class="col-md-6">
            <div class="glass-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-dark mb-0 fw-semibold">Ứng viên gần đây</h6>
                    <a href="{{ route('company.applications.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">Xem tất cả</a>
                </div>
                @foreach($recentApps as $app)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-light">
                    <div>
                        <p class="mb-0 text-dark small fw-semibold">{{ $app->student->user->name ?? 'Student' }}</p>
                        <span class="text-muted" style="font-size:0.75rem">{{ $app->job->title ?? '' }}</span>
                    </div>
                    @if($app->status=='pending')<span class="badge bg-warning text-dark">Chờ duyệt</span>
                    @elseif($app->status=='accepted')<span class="badge bg-success">Chấp nhận</span>
                    @else<span class="badge bg-danger">Từ chối</span>@endif
                </div>
                @endforeach
                @if($recentApps->isEmpty())<p class="text-muted text-center py-3 small">Chưa có ứng viên nào.</p>@endif
            </div>
        </div>
    </div>
</div>
@endsection