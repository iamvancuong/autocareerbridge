@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-dark">Thông báo của tôi</h2>
    </div>
    <div class="glass-card p-0 overflow-hidden">
        @forelse($notifications as $notif)
        <div class="d-flex align-items-start gap-3 p-3 border-bottom border-light {{ $notif->is_read ? '' : 'bg-primary bg-opacity-5' }}">
            <div class="mt-1 flex-shrink-0">
                @if($notif->type == 'success')<i class="fa-solid fa-circle-check fa-lg text-success"></i>
                @elseif($notif->type == 'warning')<i class="fa-solid fa-triangle-exclamation fa-lg text-warning"></i>
                @elseif($notif->type == 'danger')<i class="fa-solid fa-circle-xmark fa-lg text-danger"></i>
                @else<i class="fa-solid fa-circle-info fa-lg text-primary"></i>@endif
            </div>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start">
                    <p class="mb-1 fw-semibold text-dark small">{{ $notif->title }}</p>
                    <span class="text-muted" style="font-size:0.75rem">{{ $notif->created_at->diffForHumans() }}</span>
                </div>
                <p class="mb-0 text-muted small">{{ $notif->message }}</p>
                @if($notif->url)
                <a href="{{ $notif->url }}" class="text-primary small">Xem chi tiết →</a>
                @endif
            </div>
            @if(!$notif->is_read)<span class="badge bg-primary rounded-circle ms-2 flex-shrink-0" style="width:8px;height:8px;padding:0;"> </span>@endif
        </div>
        @empty
        <div class="text-center py-5 text-muted">
            <i class="fa-solid fa-bell-slash fa-3x mb-3 d-block opacity-50"></i>
            <p>Bạn chưa có thông báo nào.</p>
        </div>
        @endforelse
    </div>
    <div class="mt-3">{{ $notifications->links() }}</div>
</div>
@endsection