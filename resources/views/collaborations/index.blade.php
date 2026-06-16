@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-dark">Quản lý Hợp tác</h2>
        @if(auth()->user()->role == 'company')
            <a href="{{ route('universities.index') }}" class="btn btn-gradient">
                <i class="fa-solid fa-magnifying-glass me-2"></i>Tìm Trường học mới
            </a>
        @elseif(auth()->user()->role == 'university')
            <a href="{{ route('companies.index') }}" class="btn btn-gradient">
                <i class="fa-solid fa-magnifying-glass me-2"></i>Tìm Doanh nghiệp mới
            </a>
        @endif
    </div>

    {{-- Legend --}}
    <div class="d-flex gap-3 mb-3 text-muted small">
        <span><i class="fa-solid fa-arrow-right-from-bracket text-primary me-1"></i> Yêu cầu bạn đã gửi</span>
        <span><i class="fa-solid fa-arrow-right-to-bracket text-success me-1"></i> Yêu cầu bạn nhận được</span>
    </div>

    <div class="glass-card p-0 overflow-hidden">
        <table class="table table-hover table-borderless align-middle mb-0 bg-transparent">
            <thead>
                <tr>
                    <th class="bg-transparent border-bottom border-secondary text-muted fw-bold py-3">Hướng</th>
                    <th class="bg-transparent border-bottom border-secondary text-muted fw-bold py-3">Đối tác</th>
                    <th class="bg-transparent border-bottom border-secondary text-muted fw-bold py-3">Trạng thái</th>
                    <th class="bg-transparent border-bottom border-secondary text-muted fw-bold py-3">Ngày gửi</th>
                    <th class="bg-transparent border-bottom border-secondary text-muted fw-bold py-3">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($collaborations as $collab)
                    @php
                        $currentRole = auth()->user()->role;
                        // Did I initiate this request?
                        $iSentThis = $collab->initiated_by === $currentRole
                                     || ($collab->initiated_by === null && $currentRole === 'company');
                        // The partner name
                        $partnerName = $currentRole === 'university'
                            ? ($collab->company->company_name ?? 'Company')
                            : ($collab->university->university_name ?? 'University');
                        // Can I approve/reject? Only if I'm the RECEIVER and status is pending
                        $canDecide = !$iSentThis && $collab->status === 'pending';
                    @endphp
                    <tr>
                        <td class="bg-transparent border-bottom border-secondary py-3 align-middle">
                            @if($iSentThis)
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary">
                                    <i class="fa-solid fa-arrow-right-from-bracket me-1"></i>Đã gửi
                                </span>
                            @else
                                <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                    <i class="fa-solid fa-arrow-right-to-bracket me-1"></i>Nhận được
                                </span>
                            @endif
                        </td>
                        <td class="bg-transparent border-bottom border-secondary text-dark py-3 align-middle fw-semibold">
                            {{ $partnerName }}
                        </td>
                        <td class="bg-transparent border-bottom border-secondary py-3 align-middle">
                            @if($collab->status == 'pending')
                                <span class="badge bg-warning text-dark">Chờ duyệt</span>
                            @elseif($collab->status == 'approved')
                                <span class="badge bg-success">Đã hợp tác</span>
                            @else
                                <span class="badge bg-danger">Từ chối</span>
                            @endif
                        </td>
                        <td class="bg-transparent border-bottom border-secondary text-muted py-3 align-middle">
                            {{ $collab->created_at->format('d/m/Y') }}
                        </td>
                        <td class="bg-transparent border-bottom border-secondary py-3 align-middle">
                            @if($canDecide)
                                <form action="{{ route('collaborations.update', $collab->id) }}" method="POST" class="d-inline">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="status" value="approved">
                                    <button class="btn btn-sm btn-success me-1">
                                        <i class="fa-solid fa-check me-1"></i>Phê duyệt
                                    </button>
                                </form>
                                <form action="{{ route('collaborations.update', $collab->id) }}" method="POST" class="d-inline">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="status" value="rejected">
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="fa-solid fa-xmark me-1"></i>Từ chối
                                    </button>
                                </form>
                            @elseif($iSentThis && $collab->status === 'pending')
                                <span class="text-muted small"><i class="fa-solid fa-clock me-1"></i>Đang chờ phản hồi</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="bg-transparent border-0 text-center py-5 text-muted">
                            <i class="fa-solid fa-handshake fa-2x mb-2 d-block opacity-50"></i>
                            Chưa có yêu cầu hợp tác nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    
    <div class="d-flex justify-content-center mt-4">
        {{ $collaborations->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection