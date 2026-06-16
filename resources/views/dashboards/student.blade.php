@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-dark mb-1">Dashboard - {{ auth()->user()->name }}</h2>
            <p class="text-muted mb-0 small">Theo dõi tiến trình tìm việc của bạn</p>
        </div>
        <a href="{{ route('student.resumes.index') }}" class="btn btn-gradient"><i class="fa-solid fa-file-pdf me-2"></i>Quản lý CV</a>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="glass-card p-4 border-start border-primary border-4">
            <h3 class="text-dark mb-0">{{ $stats['total_apps'] }}</h3>
            <span class="text-muted small">Đơn đã nộp</span>
        </div></div>
        <div class="col-md-3"><div class="glass-card p-4 border-start border-warning border-4">
            <h3 class="text-dark mb-0">{{ $stats['pending'] }}</h3>
            <span class="text-muted small">Đang chờ xét duyệt</span>
        </div></div>
        <div class="col-md-3"><div class="glass-card p-4 border-start border-success border-4">
            <h3 class="text-dark mb-0">{{ $stats['accepted'] }}</h3>
            <span class="text-muted small">Được chấp nhận</span>
        </div></div>
        <div class="col-md-3"><div class="glass-card p-4 border-start border-secondary border-4">
            <h3 class="text-dark mb-0">{{ $stats['resumes'] }}</h3>
            <span class="text-muted small">CV đã tải lên</span>
        </div></div>
    </div>

    <div class="row g-4">
        {{-- My Applications --}}
        <div class="col-md-7">
            <div class="glass-card p-4">
                <h6 class="text-dark mb-3 fw-semibold">Đơn ứng tuyển của tôi</h6>
                @forelse($applications as $app)
                <div class="py-2 border-bottom border-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-0 text-dark small fw-semibold">{{ $app->job->title ?? 'Job' }}</p>
                            <span class="text-muted" style="font-size:0.75rem">{{ $app->job->company->company_name ?? '' }} · {{ $app->created_at->format('d/m/Y') }}</span>
                        </div>
                        @if($app->status=='pending')<span class="badge bg-warning text-dark">Chờ duyệt</span>
                        @elseif($app->status=='accepted')<span class="badge bg-success">Chấp nhận <i class="fa-solid fa-check ms-1"></i></span>
                        @else<span class="badge bg-danger">Từ chối</span>@endif
                    </div>
                    @if($app->hr_feedback)
                    <div class="mt-2 p-2 bg-light rounded small text-muted border border-info" style="font-size: 0.8rem; white-space: pre-line;">
                        <strong><i class="fa-solid fa-comment-dots text-info me-1"></i>HR Nhận xét:</strong> {{ $app->hr_feedback }}
                    </div>
                    @endif
                </div>
                @empty
                <p class="text-muted text-center py-4 small">Bạn chưa nộp đơn nào. <a href="{{ route('jobs.index') }}">Tìm việc ngay</a></p>
                @endforelse
            </div>
        </div>

        {{-- Suggested Jobs --}}
        <div class="col-md-5">
            <div class="glass-card p-4">
                <h6 class="text-dark mb-3 fw-semibold"><i class="fa-solid fa-star text-warning me-1"></i>Job phù hợp với bạn</h6>
                @forelse($suggestedJobs as $job)
                <div class="py-2 border-bottom border-light">
                    <p class="mb-0 text-dark small fw-semibold">{{ $job->title }}</p>
                    <span class="text-muted" style="font-size:0.75rem">{{ $job->company->company_name ?? '' }}</span>
                    <div class="mt-1"><a href="{{ route('jobs.show', $job->id) }}" class="btn btn-xs btn-outline-primary btn-sm" style="font-size:0.7rem;padding:2px 8px;">Xem</a></div>
                </div>
                @empty
                <p class="text-muted text-center py-3 small">Hãy thêm thông tin ngành học để nhận gợi ý.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection