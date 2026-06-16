@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-dark">Quản lý Nhân sự (HR)</h2>
        <button class="btn btn-gradient" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fa-solid fa-plus me-2"></i>Thêm Nhân sự
        </button>
    </div>

    <div class="glass-card p-0 overflow-hidden">
        <table class="table table-hover table-borderless align-middle mb-0 bg-transparent">
            <thead>
                <tr>
                    <th class="bg-transparent border-bottom border-secondary text-muted fw-bold py-3">Họ tên</th>
                    <th class="bg-transparent border-bottom border-secondary text-muted fw-bold py-3">Email</th>
                    <th class="bg-transparent border-bottom border-secondary text-muted fw-bold py-3">Vị trí</th>
                    <th class="bg-transparent border-bottom border-secondary text-muted fw-bold py-3 text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hirings as $hr)
                <tr>
                    <td class="bg-transparent border-bottom border-secondary text-dark py-3 align-middle">{{ $hr->user->name ?? 'N/A' }}</td>
                    <td class="bg-transparent border-bottom border-secondary py-3 align-middle">{{ $hr->user->email ?? 'N/A' }}</td>
                    <td class="bg-transparent border-bottom border-secondary py-3 align-middle">{{ $hr->position }}</td>
                    <td class="bg-transparent border-bottom border-secondary py-3 align-middle text-end">
                        <form action="{{ route('company.hirings.destroy', $hr->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa nhân sự này?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center py-4 text-muted">Chưa có nhân sự nào được thêm.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content glass-card">
            <form action="{{ route('company.hirings.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom border-secondary">
                    <h5 class="modal-title text-dark">Thêm Nhân sự HR</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-dark">Họ và tên</label>
                        <input type="text" name="name" class="form-control form-control-glass" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark">Email đăng nhập</label>
                        <input type="email" name="email" class="form-control form-control-glass" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark">Mật khẩu</label>
                        <input type="password" name="password" class="form-control form-control-glass" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark">Vị trí (Ví dụ: Chuyên viên Tuyển dụng)</label>
                        <input type="text" name="position" class="form-control form-control-glass" required>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm mới</button>
                </div>
            </form>
        </div>
    </div>

    
    <div class="d-flex justify-content-center mt-4">
        {{ $hirings->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection