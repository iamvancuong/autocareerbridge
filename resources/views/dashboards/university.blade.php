@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-dark mb-1">Dashboard - {{ auth()->user()->university->university_name ?? auth()->user()->name }}</h2>
            <p class="text-muted mb-0 small">Tổng quan hoạt động của trường</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="glass-card p-4 border-start border-primary border-4">
            <h3 class="text-dark mb-0">{{ $stats['students'] }}</h3>
            <span class="text-muted small">Sinh viên</span>
        </div></div>
        <div class="col-md-3"><div class="glass-card p-4 border-start border-success border-4">
            <h3 class="text-dark mb-0">{{ $stats['collaborations'] }}</h3>
            <span class="text-muted small">Đang hợp tác</span>
        </div></div>
        <div class="col-md-3"><div class="glass-card p-4 border-start border-warning border-4">
            <h3 class="text-dark mb-0">{{ $stats['pending_collabs'] }}</h3>
            <span class="text-muted small">Yêu cầu chờ duyệt</span>
        </div></div>
        <div class="col-md-3"><div class="glass-card p-4 border-start border-info border-4">
            <h3 class="text-dark mb-0">{{ $stats['workshops'] }}</h3>
            <span class="text-muted small">Workshop đã tạo</span>
        </div></div>
    </div>

    <div class="row g-4">
        {{-- Pending Collabs --}}
        <div class="col-md-6">
            <div class="glass-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-dark mb-0 fw-semibold">Yêu cầu Hợp tác chờ duyệt</h6>
                    <a href="{{ route('collaborations.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">Xem tất cả</a>
                </div>
                @foreach($pendingCollabs as $collab)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-light">
                    <div>
                        <p class="mb-0 text-dark small fw-semibold">{{ $collab->company->company_name ?? 'Company' }}</p>
                        <span class="text-muted" style="font-size:0.75rem">{{ $collab->created_at->format('d/m/Y') }}</span>
                    </div>
                    <span class="badge bg-warning text-dark">Chờ duyệt</span>
                </div>
                @endforeach
                @if($pendingCollabs->isEmpty())<p class="text-muted text-center py-3 small">Không có yêu cầu nào đang chờ.</p>@endif
            </div>
        </div>

        {{-- Upcoming Workshops --}}
        <div class="col-md-6">
            <div class="glass-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-dark mb-0 fw-semibold">Workshop sắp tới</h6>
                    <a href="{{ route('university.workshops.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">Quản lý</a>
                </div>
                @foreach($upcomingWorkshops as $ws)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-light">
                    <div>
                        <p class="mb-0 text-dark small fw-semibold">{{ $ws->title }}</p>
                        <span class="text-muted" style="font-size:0.75rem">{{ $ws->date ? \Carbon\Carbon::parse($ws->date)->format('d/m/Y') : '' }}</span>
                    </div>
                    <span class="badge bg-success">Sắp tới</span>
                </div>
                @endforeach
                @if($upcomingWorkshops->isEmpty())<p class="text-muted text-center py-3 small">Chưa có Workshop nào sắp tới.</p>@endif
            </div>
        </div>
    </div>
</div>
@endsection