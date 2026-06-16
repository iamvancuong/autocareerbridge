@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="glass-card p-5">
                <h3 class="text-center mb-4 text-dark"><i class="fa-solid fa-right-to-bracket text-primary me-2"></i>Đăng nhập</h3>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-muted">Email</label>
                        <input type="email" name="email" class="form-control form-control-glass" required autofocus value="{{ old('email') }}">
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted">Mật khẩu</label>
                        <input type="password" name="password" class="form-control form-control-glass" required>
                    </div>
                    <button type="submit" class="btn btn-gradient w-100 mb-3">Đăng nhập</button>
                    <p class="text-center text-muted small">Chưa có tài khoản? <a href="{{ route('register') }}" class="text-primary">Đăng ký ngay</a></p>
                </form>
                <hr class="my-3">
                <p class="text-muted text-center small mb-2">Demo nhanh (Dev Mode)</p>
                <div class="d-flex gap-2 flex-wrap justify-content-center">
                    <a href="{{ route('mock.login', ['role' => 'admin']) }}" class="btn btn-sm btn-outline-warning">Admin</a>
                    <a href="{{ route('mock.login', ['role' => 'company']) }}" class="btn btn-sm btn-outline-primary">Company</a>
                    <a href="{{ route('mock.login', ['role' => 'university']) }}" class="btn btn-sm btn-outline-info">University</a>
                    <a href="{{ route('mock.login', ['role' => 'student']) }}" class="btn btn-sm btn-outline-success">Student</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection