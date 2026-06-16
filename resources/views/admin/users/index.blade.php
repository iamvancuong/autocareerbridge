@extends('layouts.app')
@section('content')
<div class="container py-4">
    @if($pendingUsers->count())
    <div class="mb-5">
        <h4 class="text-dark mb-3"><i class="fa-solid fa-clock text-warning me-2"></i>Tài khoản Chờ duyệt ({{ $pendingUsers->count() }})</h4>
        <div class="row g-3">
            @foreach($pendingUsers as $user)
            <div class="col-md-4">
                <div class="glass-card p-4 border-start border-warning border-4">
                    <h6 class="text-dark mb-1">{{ $user->name }}</h6>
                    <p class="text-muted small mb-1">{{ $user->email }}</p>
                    <span class="badge bg-primary mb-3">{{ ucfirst($user->role) }}</span>
                    <div class="d-flex gap-2">
                        <form action="{{ route('admin.users.approve', $user->id) }}" method="POST" class="d-inline">@csrf
                            <button class="btn btn-sm btn-success"><i class="fa-solid fa-check"></i> Duyệt</button>
                        </form>
                        <form action="{{ route('admin.users.reject', $user->id) }}" method="POST" class="d-inline">@csrf
                            <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-xmark"></i> Từ chối</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <h4 class="text-dark mb-3">Tất cả Tài khoản</h4>
    <div class="glass-card p-0 overflow-hidden">
        <table class="table table-light table-hover mb-0">
            <thead>
                <tr><th class="text-muted">Tên</th><th class="text-muted">Email</th><th class="text-muted">Role</th><th class="text-muted">Trạng thái</th></tr>
            </thead>
            <tbody>
                @foreach($allUsers as $user)
                <tr>
                    <td class="text-dark align-middle">{{ $user->name }}</td>
                    <td class="text-muted align-middle small">{{ $user->email }}</td>
                    <td class="align-middle"><span class="badge bg-secondary">{{ ucfirst($user->role) }}</span></td>
                    <td class="align-middle">
                        @if($user->is_active)<span class="badge bg-success">Hoạt động</span>
                        @else<span class="badge bg-warning text-dark">Chờ duyệt</span>@endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-3">{{ $allUsers->links() }}</div>
    </div>
</div>
@endsection