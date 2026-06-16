@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h2 class="text-dark mb-4">Khám phá Doanh nghiệp</h2>
    <div class="glass-card p-3 mb-4">
        <form action="{{ route('companies.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-10"><input type="text" name="search" class="form-control form-control-glass" placeholder="Tìm kiếm doanh nghiệp..." value="{{ request('search') }}"></div>
            <div class="col-md-2"><button type="submit" class="btn btn-gradient w-100">Tìm</button></div>
        </form>
    </div>
    <div class="row g-4">
        @forelse($companies as $company)
        <div class="col-md-4">
            <div class="glass-card p-4 h-100 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h5 class="text-dark mb-0">{{ $company->company_name }}</h5>
                    <i class="fa-solid fa-building fa-2x text-primary opacity-50"></i>
                </div>
                <p class="text-muted flex-grow-1 small">{{ $company->description ?? 'Chưa có mô tả' }}</p>
                @if(auth()->check() && auth()->user()->role == 'university')
                <div class="mt-3 border-top border-secondary pt-3">
                    @if(isset($collabStatuses[$company->id]))
                        @if($collabStatuses[$company->id]=='pending')<button class="btn btn-secondary w-100" disabled>Đang chờ duyệt</button>
                        @elseif($collabStatuses[$company->id]=='approved')<button class="btn btn-success w-100" disabled>Đã hợp tác</button>
                        @else<button class="btn btn-danger w-100" disabled>Bị từ chối</button>@endif
                    @else
                    <form action="{{ route('collaborations.store') }}" method="POST">@csrf
                        <input type="hidden" name="company_id" value="{{ $company->id }}">
                        <button type="submit" class="btn btn-outline-primary w-100 rounded-pill"><i class="fa-solid fa-handshake me-2"></i>Gửi Yêu cầu Hợp tác</button>
                    </form>
                    @endif
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5 text-muted"><i class="fa-solid fa-building fa-3x mb-3 d-block"></i>Không tìm thấy doanh nghiệp nào.</div>
        @endforelse
    </div>
</div>
@endsection