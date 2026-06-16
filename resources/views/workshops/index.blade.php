@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-dark mb-1">Sự kiện & Workshop</h2>
            <p class="text-muted mb-0">Các sự kiện từ các trường đại học hợp tác</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="glass-card p-3 mb-4">
        <form action="{{ route('workshops.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control form-control-glass"
                    placeholder="Tìm kiếm Workshop..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-gradient w-100">Tìm</button>
            </div>
        </form>
    </div>

    {{-- Workshop List --}}
    <div class="row g-4">
        @forelse($workshops as $ws)
            <div class="col-md-6 col-lg-4">
                <div class="glass-card p-4 h-100 d-flex flex-column">
                    {{-- Date badge --}}
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-2">
                            <i class="fa-solid fa-calendar me-1"></i>
                            {{ $ws->date ? \Carbon\Carbon::parse($ws->date)->format('d/m/Y') : 'Sắp có' }}
                        </span>
                        @if($ws->date && \Carbon\Carbon::parse($ws->date)->isFuture())
                            <span class="badge bg-success">Sắp diễn ra</span>
                        @elseif($ws->date)
                            <span class="badge bg-secondary">Đã kết thúc</span>
                        @endif
                    </div>

                    <h5 class="text-dark mb-2">{{ $ws->title }}</h5>
                    <p class="text-muted small flex-grow-1">{{ Str::limit($ws->description, 120) }}</p>

                    <div class="border-top border-light pt-3 mt-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-school text-primary"></i>
                            <span class="text-muted small">{{ $ws->university->university_name ?? 'Trường học' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5 text-muted">
                    <i class="fa-solid fa-calendar-xmark fa-3x mb-3 d-block opacity-50"></i>
                    <h5>Chưa có Workshop nào được tổ chức.</h5>
                    <p class="small">Hãy quay lại sau khi các trường cập nhật sự kiện mới!</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-4">{{ $workshops->links() }}</div>
</div>
@endsection
