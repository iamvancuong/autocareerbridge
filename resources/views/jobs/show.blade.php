@extends('layouts.app')

@section('content')
<div class="container py-4">
    <a href="{{ route('jobs.index') }}" class="text-decoration-none text-muted mb-3 d-inline-block"><i class="fa-solid fa-arrow-left me-2"></i>Quay lại</a>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="glass-card p-4 p-md-5 mb-4">
                <h2 class="fw-bold">{{ $job->title }}</h2>
                <div class="d-flex align-items-center gap-2 mb-4">
                    <h5 class="text-primary mb-0">{{ $job->company->company_name ?? 'Company' }}</h5>
                    @if(isset($isPartner) && $isPartner)
                        <span class="badge bg-info text-dark rounded-pill border border-info shadow-sm">
                            <i class="fa-solid fa-handshake me-1"></i> Đối tác của Trường
                        </span>
                    @endif
                </div>
                
                <h5 class="mt-4 border-bottom border-secondary pb-2">Mô tả công việc</h5>
                <p class="text-muted" style="white-space: pre-line;">{{ $job->description }}</p>
                
                <h5 class="mt-4 border-bottom border-secondary pb-2">Yêu cầu</h5>
                <p class="text-muted" style="white-space: pre-line;">{{ $job->requirements }}</p>
            </div>
            
            <!-- Result after applying -->
            @if(isset($application))
                <div class="glass-card p-4 border-primary shadow-lg">
                    <h4 class="text-primary"><i class="fa-solid fa-robot me-2"></i>Kết quả phân tích CV từ AI</h4>
                    <div class="d-flex align-items-center mt-4 mb-3">
                        <div class="display-4 fw-bold me-3 {{ $application->ai_score > 70 ? 'text-success' : 'text-warning' }}">{{ $application->ai_score }}</div>
                        <div class="text-muted h5">/ 100 Điểm</div>
                    </div>
                    <div class="alert alert-dark bg-opacity-10 border-0 text-dark">
                        <i class="fa-solid fa-comment-dots text-primary me-2"></i> <strong>Nhận xét:</strong> {{ $application->ai_review }}
                    </div>
                </div>
                
                @if($application->hr_feedback)
                <div class="glass-card p-4 border-info shadow-lg mt-4">
                    <h5 class="text-info mb-3"><i class="fa-solid fa-user-tie me-2"></i>Nhận xét từ Nhà tuyển dụng (HR)</h5>
                    <div class="alert alert-info bg-opacity-10 border-0 text-dark" style="white-space: pre-line;">
                        {{ $application->hr_feedback }}
                    </div>
                </div>
                @endif
            @endif
        </div>
        
        <div class="col-lg-4">
            <div class="glass-card p-4 sticky-top" style="top: 100px;">
                <h4 class="mb-3">Ứng tuyển ngay</h4>
                @if(auth()->check() && auth()->user()->role == 'student')
                    @if(isset($application))
                        <div class="alert alert-success border-0 bg-success bg-opacity-25 text-dark text-center">
                            <i class="fa-solid fa-circle-check me-2"></i> Bạn đã ứng tuyển vị trí này vào ngày {{ $application->created_at->format('d/m/Y') }}
                        </div>
                    @else
                        <form action="{{ route('jobs.apply', $job->id) }}" method="POST">
                            @csrf
                            <div class="mb-3"><p class="text-muted small">Hệ thống sẽ tự động gửi <strong>CV Mặc định</strong> của bạn cho Nhà tuyển dụng và dùng nó để phân tích AI.</p></div>
                            <button type="submit" class="btn btn-gradient w-100 rounded-pill">
                                Nộp CV & Phân tích AI <i class="fa-solid fa-paper-plane ms-2"></i>
                            </button>
                        </form>
                    @endif
                @else
                    <div class="alert alert-warning border-0 bg-warning bg-opacity-25 text-dark">
                        Vui lòng đăng nhập tài khoản Sinh viên để ứng tuyển.
                    </div>
                    <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 rounded-pill">Đăng nhập Sinh viên</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection