@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Dashboard - <span class="text-primary">{{ auth()->user()->role }}</span></h2>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="glass-card p-4">
                <h4>Xin chào, {{ auth()->user()->name }}!</h4>
                <p class="text-muted mb-0">Chào mừng bạn đến với hệ thống quản lý Career Bridge.</p>
                <hr class="border-secondary my-4">
                @if(auth()->user()->role == 'student')
                    <a href="{{ route('jobs.index') }}" class="btn btn-gradient">Xem việc làm phù hợp</a>
                @elseif(auth()->user()->role == 'company' || auth()->user()->role == 'university')
                    <a href="{{ route('collaborations.index') }}" class="btn btn-gradient">Quản lý Hợp tác</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection