@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý Hồ sơ & CV</h2>
        <button class="btn btn-gradient" data-bs-toggle="modal" data-bs-target="#uploadCvModal">
            <i class="fa-solid fa-cloud-arrow-up me-2"></i>Tải CV lên
        </button>
    </div>

    <!-- CV List Grid -->
    <div class="row g-4">
        @forelse($resumes as $resume)
            <div class="col-md-4">
                <div class="glass-card p-4 h-100 position-relative {{ $resume->is_default ? 'border-primary shadow-lg' : 'opacity-75' }}">
                    @if($resume->is_default)
                        <span class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-primary px-3 text-white">
                            <i class="fa-solid fa-star me-1 text-warning"></i> CV Mặc định
                        </span>
                    @endif
                    <div class="text-center mb-3 mt-2">
                        <i class="fa-solid fa-file-pdf fa-4x text-danger"></i>
                    </div>
                    <h5 class="text-dark text-center mb-1 text-truncate" title="{{ $resume->original_name }}">{{ $resume->original_name ?? 'CV_Document.pdf' }}</h5>
                    <p class="text-muted text-center small mb-3">Tải lên: {{ $resume->created_at->format('d/m/Y') }}</p>
                    
                    <div class="d-flex justify-content-center gap-2 mt-auto">
                        <a href="{{ Storage::url($resume->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye"></i> Xem</a>
                        @if(!$resume->is_default)
                            <form action="{{ route('student.resumes.setDefault', $resume->id) }}" method="POST" class="d-inline">
                                @csrf @method('PUT')
                                <button type="submit" class="btn btn-sm btn-outline-success" title="Đặt làm mặc định"><i class="fa-solid fa-check"></i></button>
                            </form>
                        @endif
                        <form action="{{ route('student.resumes.destroy', $resume->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xác nhận xóa CV này?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert bg-light border-0 text-center py-5 shadow-sm text-dark">
                    <i class="fa-solid fa-folder-open fa-3x mb-3 text-muted"></i>
                    <h5>Bạn chưa tải lên CV nào!</h5>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadCvModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-white text-dark border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title"><i class="fa-solid fa-cloud-arrow-up text-primary me-2"></i> Tải CV mới lên</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('student.resumes.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3 text-center p-4 border border-secondary border-2 border-dashed rounded" style="border-style: dashed !important; border-color: #cbd5e1 !important;">
                        <i class="fa-solid fa-file-arrow-up fa-3x text-muted mb-3"></i>
                        <p class="mb-2">Kéo thả file CV của bạn vào đây hoặc</p>
                        <input name="cv_file" class="form-control form-control-sm bg-light text-dark mx-auto w-75" type="file" id="cv_file" accept=".pdf,.doc,.docx" required>
                        <p class="text-muted small mt-2">Hỗ trợ: PDF, DOCX (Tối đa 5MB)</p>
                    </div>
                    <div class="form-check mb-3">
                        <input name="is_default" class="form-check-input" type="checkbox" id="is_default" value="1" checked>
                        <label class="form-check-label" for="is_default">Đặt làm CV mặc định</label>
                    </div>
                    <button type="submit" class="btn btn-gradient w-100">Tải lên</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection