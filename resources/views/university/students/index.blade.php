@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-dark mb-1">Danh sách Sinh viên</h2>
            <p class="text-muted mb-0 small">Quản lý sinh viên thuộc trường đại học của bạn</p>
        </div>
    </div>

    <div class="glass-card p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Mã Sinh Viên</th>
                        <th>Chuyên Ngành</th>
                        <th>GPA</th>
                        <th class="text-center">Số CV</th>
                        <th class="text-center">Số Đơn Ứng Tuyển</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 40px; height: 40px;">
                                        <i class="fa-solid fa-user text-primary"></i>
                                    </div>
                                    <strong>{{ $student->full_name ?? $student->user->name }}</strong>
                                </div>
                            </td>
                            <td class="text-muted">{{ $student->user->email }}</td>
                            <td><span class="badge bg-secondary">{{ $student->student_code ?? 'N/A' }}</span></td>
                            <td>{{ $student->major->name ?? 'Chưa cập nhật' }}</td>
                            <td><span class="fw-bold {{ $student->gpa >= 3.2 ? 'text-success' : 'text-warning' }}">{{ $student->gpa ?? 'N/A' }}</span></td>
                            <td class="text-center">{{ $student->resumes_count }}</td>
                            <td class="text-center">
                                <span class="badge bg-info text-dark">{{ $student->applications_count }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fa-solid fa-users-slash fa-2x mb-3"></i>
                                <p>Trường của bạn hiện chưa có sinh viên nào đăng ký.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $students->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
