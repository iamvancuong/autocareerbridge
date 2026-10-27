<x-mail::message>
# Xin chào {{ $name }},

Tài khoản **{{ $roleName }}** của bạn trên hệ thống {{ config('app.name') }} đã được quản trị viên phê duyệt.

Bạn có thể đăng nhập ngay để bắt đầu sử dụng đầy đủ các tính năng của hệ thống.

<x-mail::button :url="$loginUrl">
Đăng nhập
</x-mail::button>

Trân trọng,<br>
{{ config('app.name') }}
</x-mail::message>
