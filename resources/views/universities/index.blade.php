@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Khám phá Trường Đại học</h2>
    </div>

    <!-- Search Form -->
    <div class="glass-card p-3 mb-4">
        <form action="{{ route('universities.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control form-control-glass" placeholder="Tìm kiếm tên trường đại học..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-gradient w-100">Tìm kiếm <i class="fa-solid fa-search ms-1"></i></button>
            </div>
        </form>
    </div>

    <!-- Universities List -->
    <div class="row g-4">
        @forelse($universities as $uni)
            <div class="col-md-6 col-lg-4">
                <div class="glass-card p-4 h-100 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h4 class="h5 mb-0 text-dark">{{ $uni->university_name }}</h4>
                        <i class="fa-solid fa-school text-primary fa-2x"></i>
                    </div>
                    <p class="text-dark flex-grow-1"><i class="fa-solid fa-location-dot me-2 text-danger"></i>{{ $uni->address ?? 'Đang cập nhật địa chỉ' }}</p>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Chuyên ngành đào tạo:</small>
                        @if($uni->majors->count() > 0)
                            @foreach($uni->majors->take(5) as $major)
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary">{{ $major->name }}</span>
                            @endforeach
                            @if($uni->majors->count() > 5)
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary">+{{ $uni->majors->count() - 5 }}</span>
                            @endif
                        @else
                            <span class="text-muted small fst-italic">Chưa cập nhật ngành đào tạo</span>
                        @endif
                    </div>
                    
                    @if(auth()->check() && auth()->user()->hasCompanyRole())
                        <div class="mt-3 border-top border-secondary pt-3">
                            @if(isset($collabStatuses[$uni->id]))
                                @if($collabStatuses[$uni->id] == 'pending')
                                    <button class="btn btn-secondary w-100 rounded-pill" disabled><i class="fa-solid fa-clock me-2"></i>Đang chờ duyệt</button>
                                @elseif($collabStatuses[$uni->id] == 'approved')
                                    <button class="btn btn-success w-100 rounded-pill" disabled><i class="fa-solid fa-check me-2"></i>Đã hợp tác</button>
                                @else
                                    <button class="btn btn-danger w-100 rounded-pill" disabled><i class="fa-solid fa-xmark me-2"></i>Bị từ chối</button>
                                @endif
                            @else
                                <form action="{{ route('collaborations.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="university_id" value="{{ $uni->id }}">
                                    <button type="submit" class="btn btn-outline-primary w-100 rounded-pill">
                                        <i class="fa-solid fa-handshake me-2"></i>Gửi Yêu cầu Hợp tác
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-dark bg-opacity-10 border-0 text-dark text-center py-5">
                    <i class="fa-solid fa-building-columns fa-3x mb-3 text-muted"></i>
                    <h5>Không tìm thấy trường đại học nào!</h5>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection