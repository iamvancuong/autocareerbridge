@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="glass-card p-5">
                <h4 class="text-dark mb-4">Tạo Workshop mới</h4>
                <form action="{{ route('university.workshops.store') }}" method="POST">@csrf
                    <div class="mb-3"><label class="form-label text-muted">Tiêu đề Workshop</label>
                        <input type="text" name="title" class="form-control form-control-glass" required></div>
                    <div class="mb-3"><label class="form-label text-muted">Mô tả</label>
                        <textarea name="description" rows="4" class="form-control form-control-glass" required></textarea></div>
                    <div class="mb-4"><label class="form-label text-muted">Ngày tổ chức</label>
                        <input type="date" name="date" class="form-control form-control-glass" required></div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('university.workshops.index') }}" class="btn btn-outline-secondary">Hủy</a>
                        <button type="submit" class="btn btn-gradient">Tạo Workshop</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection