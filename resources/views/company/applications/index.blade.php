@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Duyệt Ứng viên</h2>
    </div>

    <!-- UI Metrics -->
    <div class="row mb-4">
        <div class="col-md-3"><div class="glass-card p-3 border-start border-primary border-4 text-dark"><h5>{{ $stats['total'] }}</h5><span class="text-muted small">Tổng ứng viên</span></div></div>
        <div class="col-md-3"><div class="glass-card p-3 border-start border-warning border-4 text-dark"><h5>{{ $stats['pending'] }}</h5><span class="text-muted small">Đang chờ duyệt</span></div></div>
        <div class="col-md-3"><div class="glass-card p-3 border-start border-success border-4 text-dark"><h5>{{ $stats['accepted'] }}</h5><span class="text-muted small">Đã chấp nhận</span></div></div>
        <div class="col-md-3"><div class="glass-card p-3 border-start border-danger border-4 text-dark"><h5>{{ $stats['rejected'] }}</h5><span class="text-muted small">Từ chối</span></div></div>
    </div>

    <!-- Table -->
    <div class="glass-card p-0 overflow-hidden">
        <table class="table table-hover table-borderless align-middle mb-0 bg-transparent">
            <thead>
                <tr>
                    <th class="bg-transparent border-bottom border-secondary text-muted fw-bold py-3">Ứng viên</th>
                    <th class="bg-transparent border-bottom border-secondary text-muted fw-bold py-3">Vị trí ứng tuyển</th>
                    <th class="bg-transparent border-bottom border-secondary text-muted fw-bold py-3">AI Score</th>
                    <th class="bg-transparent border-bottom border-secondary text-muted fw-bold py-3">Trạng thái</th>
                    <th class="bg-transparent border-bottom border-secondary text-muted fw-bold py-3 text-end">Chi tiết</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                <tr>
                    <td class="bg-transparent border-bottom border-secondary text-dark py-3 align-middle">
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-circle-user fa-2x text-primary me-2"></i>
                            <div>
                                <h6 class="mb-0">{{ $app->student->user->name ?? 'Student' }}</h6>
                                <small class="text-muted">{{ $app->student->university->university_name ?? '' }}</small>
                            </div>
                        </div>
                    </td>
                    <td class="bg-transparent border-bottom border-secondary text-dark py-3 align-middle">{{ $app->job->title }}</td>
                    <td class="bg-transparent border-bottom border-secondary py-3 align-middle">
                        @if($app->ai_score)
                            <span class="badge {{ $app->ai_score > 70 ? 'bg-success text-success' : 'bg-warning text-warning' }} bg-opacity-25 border {{ $app->ai_score > 70 ? 'border-success' : 'border-warning' }} p-2">
                                <i class="fa-solid fa-robot"></i> {{ $app->ai_score }}/100
                            </span>
                        @else
                            <span class="badge bg-secondary bg-opacity-25 text-secondary border border-secondary p-2">Chưa phân tích</span>
                        @endif
                    </td>
                    <td class="bg-transparent border-bottom border-secondary py-3 align-middle">
                        @if($app->status == 'pending')
                            <span class="badge bg-warning text-dark">Chờ duyệt</span>
                        @elseif($app->status == 'accepted')
                            <span class="badge bg-success text-white">Chấp nhận</span>
                        @else
                            <span class="badge bg-danger text-white">Từ chối</span>
                        @endif
                    </td>
                    <td class="bg-transparent border-bottom border-secondary py-3 align-middle text-end">
                        <a href="{{ route('company.applications.show', $app->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Đánh giá CV</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="bg-transparent border-0 text-center py-4 text-muted">Chưa có ứng viên nào nộp đơn.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    
    <div class="d-flex justify-content-center mt-4">
        {{ $applications->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection