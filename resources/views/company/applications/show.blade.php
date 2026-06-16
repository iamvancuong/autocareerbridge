@extends('layouts.app')

@section('content')
<div class="container py-4">
    <a href="{{ route('company.applications.index') }}" class="text-decoration-none text-muted mb-3 d-inline-block"><i class="fa-solid fa-arrow-left me-2"></i>Quay lại danh sách</a>

    <div class="row g-4">
        <!-- Left: Student Info & AI Review -->
        <div class="col-lg-4">
            <div class="glass-card p-4 text-center mb-4">
                <i class="fa-solid fa-circle-user fa-5x text-primary mb-3"></i>
                <h4 class="text-dark">{{ $application->student->user->name }}</h4>
                <p class="text-muted mb-1"><i class="fa-solid fa-school me-2"></i>{{ $application->student->university->university_name ?? '' }}</p>
                <p class="text-muted mb-3"><i class="fa-solid fa-graduation-cap me-2"></i>{{ $application->student->major->name ?? '' }}</p>
                
                @if($application->status == 'pending')
                    <span class="badge bg-warning text-dark px-3 py-2">Đang chờ xét duyệt</span>
                @elseif($application->status == 'accepted')
                    <span class="badge bg-success text-white px-3 py-2">Đã chấp nhận</span>
                @else
                    <span class="badge bg-danger text-white px-3 py-2">Đã từ chối</span>
                @endif
            </div>

            <div class="glass-card p-4 border-primary shadow">
                <h5 class="text-primary border-bottom border-secondary pb-2 mb-3"><i class="fa-solid fa-robot me-2"></i>AI Đánh giá CV</h5>
                <div class="text-center mb-3">
                    <h1 class="display-3 fw-bold text-success">{{ $application->ai_score ?? '--' }}</h1>
                    <span class="text-muted">/ 100 Điểm</span>
                </div>
                <div class="alert bg-light text-dark border border-secondary small shadow-sm">
                    {!! nl2br(e($application->ai_review ?? 'Chưa có dữ liệu phân tích từ AI.')) !!}
                </div>
            </div>
            
            <div class="glass-card p-4 mt-4">
                <h5 class="mb-3 text-dark">Quyết định của HR</h5>
                <div class="d-grid gap-2">
                    <form action="{{ route('company.applications.update', $application->id) }}" method="POST" class="d-grid gap-2">
                        @csrf @method('PUT')
                        <div class="mb-3 text-start">
                            <label class="form-label text-muted small">Nhận xét thủ công (HR Feedback):</label>
                            <textarea name="hr_feedback" class="form-control bg-dark text-light border-secondary" rows="3" placeholder="Nhập nhận xét của bạn để sinh viên biết lý do...">{{ $application->hr_feedback }}</textarea>
                        </div>
                        <button type="submit" name="status" value="accepted" class="btn btn-success" {{ $application->status == 'accepted' ? 'disabled' : '' }}><i class="fa-solid fa-check me-2"></i>Chấp nhận (Accept)</button>
                        <button type="submit" name="status" value="rejected" class="btn btn-outline-danger" {{ $application->status == 'rejected' ? 'disabled' : '' }}><i class="fa-solid fa-xmark me-2"></i>Từ chối (Reject)</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right: PDF Viewer Mock -->
        <div class="col-lg-8">
            <div class="glass-card p-4 h-100 d-flex flex-column">
                <h5 class="border-bottom border-secondary pb-2 mb-4 text-dark"><i class="fa-regular fa-file-pdf text-danger me-2"></i>Hồ sơ ứng viên ({{ $resume->original_name ?? 'CV_Document.pdf' }})</h5>
                
                <div class="bg-light flex-grow-1 rounded d-flex flex-column align-items-center justify-content-center text-muted" style="min-height: 500px; border: 1px dashed #475569;">
                    @if($resume && file_exists(storage_path('app/public/' . $resume->file_path)))
                        <iframe src="{{ Storage::url($resume->file_path) }}" width="100%" height="600px" style="border: none;"></iframe>
                    @else
                        <i class="fa-solid fa-file-pdf fa-4x mb-3 opacity-50"></i>
                        <p>Không tìm thấy file CV đính kèm.</p>
                    @endif
                    @if($resume)
                        <a href="{{ Storage::url($resume->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-3"><i class="fa-solid fa-download me-2"></i>Tải CV Về máy</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection