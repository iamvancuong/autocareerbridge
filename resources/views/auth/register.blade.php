@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="glass-card p-5">
                <h3 class="text-center mb-4 text-dark"><i class="fa-solid fa-user-plus text-primary me-2"></i>Đăng ký tài khoản</h3>
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-muted">Loại tài khoản</label>
                        <select name="role" class="form-select form-control-glass" required>
                            <option value="company">Doanh nghiệp</option>
                            <option value="university">Trường Đại học</option>
                            <option value="student">Sinh viên</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Tên tổ chức / Họ tên</label>
                        <input type="text" name="name" class="form-control form-control-glass" required value="{{ old('name') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Email</label>
                        <input type="email" name="email" class="form-control form-control-glass" required value="{{ old('email') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Mật khẩu</label>
                        <input type="password" name="password" class="form-control form-control-glass" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted">Xác nhận mật khẩu</label>
                        <input type="password" name="password_confirmation" class="form-control form-control-glass" required>
                    </div>
                    <div class="alert bg-light border-start border-warning border-4 small text-muted">
                        <i class="fa-solid fa-circle-info me-2 text-warning"></i>Tài khoản Doanh nghiệp và Trường học cần chờ Admin phê duyệt trước khi sử dụng.
                    </div>
                    <button type="submit" class="btn btn-gradient w-100 mb-3">Đăng ký</button>
                    <p class="text-center text-muted small">Đã có tài khoản? <a href="{{ route('login') }}" class="text-primary">Đăng nhập</a></p>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection