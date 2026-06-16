@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="glass-card p-4 text-center">
                @if(auth()->user()->avatar)
                    <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="Avatar" class="rounded-circle mb-3 object-fit-cover shadow-sm border border-2 border-primary" style="width: 120px; height: 120px;">
                @else
                    <i class="fa-solid fa-circle-user fa-5x text-primary mb-3"></i>
                @endif
                <h4 class="text-dark">{{ auth()->user()->name }}</h4>
                <span class="badge bg-primary">{{ ucfirst(auth()->user()->role) }}</span>
                <p class="text-muted mt-2 small">{{ auth()->user()->email }}</p>
            </div>
        </div>
        <div class="col-md-8">
            <div class="glass-card p-4">
                <h5 class="text-dark border-bottom border-secondary pb-2 mb-4">Cập nhật thông tin</h5>
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label text-muted">Tên</label>
                        <input type="text" name="name" class="form-control form-control-glass" value="{{ auth()->user()->name }}" required>
                    </div>
                    @if(auth()->user()->role != 'student')
                    <div class="mb-3">
                        <label class="form-label text-muted">Tên Tổ chức / Trường</label>
                        <input type="text" name="org_name" class="form-control form-control-glass"
                            value="{{ $profile->company_name ?? $profile->university_name ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Địa chỉ</label>
                        <input type="text" name="address" class="form-control form-control-glass" value="{{ $profile->address ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Mô tả</label>
                        <textarea name="description" rows="4" class="form-control form-control-glass">{{ $profile->description ?? '' }}</textarea>
                    </div>
                    @else
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Trường Đại học</label>
                            <select name="university_id" class="form-select form-control-glass">
                                <option value="">-- Chọn trường học --</option>
                                @foreach($universities as $uni)
                                    <option value="{{ $uni->id }}" {{ ($profile->university_id ?? '') == $uni->id ? 'selected' : '' }}>{{ $uni->university_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Chuyên ngành</label>
                            <select name="major_id" class="form-select form-control-glass">
                                <option value="">-- Chọn chuyên ngành --</option>
                                @foreach($majors as $major)
                                    <option value="{{ $major->id }}" {{ ($profile->major_id ?? '') == $major->id ? 'selected' : '' }}>{{ $major->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Mã sinh viên</label>
                            <input type="text" name="student_code" class="form-control form-control-glass" value="{{ $profile->student_code ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">GPA</label>
                            <input type="number" step="0.01" max="4.0" min="0" name="gpa" class="form-control form-control-glass" value="{{ $profile->gpa ?? '' }}">
                        </div>
                    </div>
                    @endif
                    <div class="mb-4">
                        <label class="form-label text-muted">Ảnh đại diện (Avatar)</label>
                        <input type="file" name="avatar" class="form-control form-control-glass" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-gradient">Lưu thay đổi</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection