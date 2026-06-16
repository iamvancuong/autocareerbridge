@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Duyệt Tin Tuyển Dụng</h2>
    </div>

    <div class="glass-card p-0 overflow-hidden">
        <table class="table table-hover table-borderless align-middle mb-0 bg-transparent">
            <thead>
                <tr>
                    <th class="bg-transparent border-bottom border-secondary text-muted fw-bold py-3">Doanh nghiệp</th>
                    <th class="bg-transparent border-bottom border-secondary text-muted fw-bold py-3">Tiêu đề</th>
                    <th class="bg-transparent border-bottom border-secondary text-muted fw-bold py-3">Ngày đăng</th>
                    <th class="bg-transparent border-bottom border-secondary text-muted fw-bold py-3 text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobs as $job)
                    <tr>
                        <td class="bg-transparent border-bottom border-secondary text-dark py-3 align-middle">{{ $job->company->company_name }}</td>
                        <td class="bg-transparent border-bottom border-secondary text-dark py-3 align-middle">{{ $job->title }}</td>
                        <td class="bg-transparent border-bottom border-secondary text-dark py-3 align-middle">{{ $job->created_at->format('d/m/Y') }}</td>
                        <td class="bg-transparent border-bottom border-secondary py-3 align-middle text-end">
                            <form action="{{ route('admin.jobs.approve', $job->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-success"><i class="fa-solid fa-check me-1"></i> Phê duyệt</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="bg-transparent border-0 text-center py-4 text-muted">Không có tin tuyển dụng nào cần duyệt.</td>
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