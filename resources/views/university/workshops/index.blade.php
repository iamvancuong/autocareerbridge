@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-dark">Quản lý Workshop</h2>
        <a href="{{ route('university.workshops.create') }}" class="btn btn-gradient"><i class="fa-solid fa-plus me-2"></i>Tạo Workshop mới</a>
    </div>
    <div class="row g-4">
        @forelse($workshops as $ws)
        <div class="col-md-4">
            <div class="glass-card p-4 h-100 d-flex flex-column">
                <h5 class="text-dark mb-2">{{ $ws->title }}</h5>
                <p class="text-muted small flex-grow-1">{{ Str::limit($ws->description, 100) }}</p>
                <div class="d-flex justify-content-between align-items-center mt-3 border-top border-secondary pt-3">
                    <span class="text-muted small"><i class="fa-solid fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($ws->date)->format('d/m/Y') }}</span>
                    <form action="{{ route('university.workshops.destroy', $ws->id) }}" method="POST" onsubmit="return confirm('Xóa workshop này?')">@csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5 text-muted"><i class="fa-solid fa-calendar-xmark fa-3x mb-3 d-block"></i>Chưa có Workshop nào.</div>
        @endforelse
    </div>
</div>
@endsection