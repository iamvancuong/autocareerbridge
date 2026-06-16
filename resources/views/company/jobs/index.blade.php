@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý Tin Tuyển dụng</h2>
        <a href="{{ route('company.jobs.create') }}" class="btn btn-gradient">
            <i class="fa-solid fa-plus me-2"></i>Đăng tin mới
        </a>
    </div>

    <div class="glass-card p-0 overflow-hidden">
        <table class="table table-hover table-borderless align-middle mb-0 bg-transparent">
            <thead>
                <tr>
                    <th class="bg-transparent border-bottom border-secondary text-muted fw-bold py-3">Tiêu đề</th>
                    <th class="bg-transparent border-bottom border-secondary text-muted fw-bold py-3">Ngành</th>
                    <th class="bg-transparent border-bottom border-secondary text-muted fw-bold py-3">Trạng thái</th>
                    <th class="bg-transparent border-bottom border-secondary text-muted fw-bold py-3 text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobs as $job)
                    <tr>
                        <td class="bg-transparent border-bottom border-secondary text-dark py-3 align-middle">{{ $job->title }}</td>
                        <td class="bg-transparent border-bottom border-secondary text-dark py-3 align-middle">{{ $job->major->name }}</td>
                        <td class="bg-transparent border-bottom border-secondary py-3 align-middle">
                            @if($job->is_approved)
                                <span class="badge bg-success">Đã duyệt</span>
                            @else
                                <span class="badge bg-warning text-dark">Chờ duyệt</span>
                            @endif
                        </td>
                        <td class="bg-transparent border-bottom border-secondary py-3 align-middle text-end">
                            <a href="{{ route('company.jobs.edit', $job->id) }}" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen"></i></a>
                            <form action="{{ route('company.jobs.destroy', $job->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa tin này?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="bg-transparent border-0 text-center py-4 text-muted">Bạn chưa đăng tin tuyển dụng nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    
    <div class="d-flex justify-content-center mt-4">
        {{ $jobs->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection