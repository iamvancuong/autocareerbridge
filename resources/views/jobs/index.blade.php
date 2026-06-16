@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Danh sách Việc làm</h2>
    </div>

    <!-- Search & Filter Form -->
    <div class="glass-card p-3 mb-4">
        <form action="{{ route('jobs.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control form-control-glass" placeholder="Tìm kiếm công việc..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="major_id" class="form-select form-control-glass">
                    <option value="">-- Tất cả ngành học --</option>
                    @foreach($majors as $major)
                        <option value="{{ $major->id }}" {{ request('major_id') == $major->id ? 'selected' : '' }}>
                            {{ $major->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-gradient w-100">Tìm kiếm <i class="fa-solid fa-search ms-1"></i></button>
            </div>
        </form>
    </div>

    <!-- Jobs List -->
    <div class="row g-4">
        @forelse($jobs as $job)
            <div class="col-md-6 col-lg-4">
                <div class="glass-card p-4 h-100 d-flex flex-column position-relative">
                    @if(isset($studentMajorIdForView) && $job->major_id == $studentMajorIdForView)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success" style="z-index: 10;">
                            <i class="fa-solid fa-star text-warning"></i> Phù hợp
                        </span>
                    @endif
                    @if(isset($partnerCompanyIds) && in_array($job->company_id, $partnerCompanyIds))
                        <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-info text-dark shadow-sm border border-info" style="z-index: 10;">
                            <i class="fa-solid fa-handshake"></i> Đối tác Trường
                        </span>
                    @endif
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h4 class="h5 mb-0 text-dark">{{ $job->title }}</h4>
                        <span class="badge {{ isset($studentMajorIdForView) && $job->major_id == $studentMajorIdForView ? 'bg-success' : 'bg-primary' }} text-uppercase">{{ $job->major->name ?? 'IT' }}</span>
                    </div>
                    <p class="text-dark mb-2"><i class="fa-solid fa-building me-2"></i>{{ $job->company->company_name ?? 'Company' }}</p>
                    <p class="text-dark flex-grow-1" style="white-space: pre-line;">{{ Str::limit($job->description, 100) }}</p>
                    <div class="mt-3">
                        <a href="{{ route('jobs.show', $job->id) }}" class="btn btn-outline-primary w-100 rounded-pill">Xem chi tiết</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-dark bg-opacity-10 border-0 text-dark text-center py-5">
                    <i class="fa-solid fa-folder-open fa-3x mb-3 text-muted"></i>
                    <h5>Không tìm thấy công việc nào phù hợp!</h5>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection