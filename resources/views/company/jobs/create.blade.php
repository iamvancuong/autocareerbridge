@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="glass-card p-4">
                <h3 class="mb-4">Đăng Tin Tuyển Dụng Mới</h3>
                <form action="{{ route('company.jobs.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-muted">Tiêu đề công việc</label>
                        <input type="text" name="title" class="form-control form-control-glass" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Ngành học liên quan</label>
                        <select name="major_id" class="form-select form-control-glass" required>
                            @foreach($majors as $major)
                                <option value="{{ $major->id }}">{{ $major->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Mô tả công việc</label>
                        <textarea name="description" rows="5" class="form-control form-control-glass" required></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted">Yêu cầu ứng viên</label>
                        <textarea name="requirements" rows="4" class="form-control form-control-glass" required></textarea>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('company.jobs.index') }}" class="btn btn-outline-primary">Hủy</a>
                        <button type="submit" class="btn btn-gradient">Đăng tin (Chờ duyệt)</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection