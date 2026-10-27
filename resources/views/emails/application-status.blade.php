<x-mail::message>
# Xin chào {{ $studentName }},

@if($isAccepted)
Chúc mừng bạn! Hồ sơ ứng tuyển vị trí **{{ $jobTitle }}** tại **{{ $companyName }}** đã được **chấp nhận**.

Nhà tuyển dụng sẽ liên hệ với bạn trong thời gian sớm nhất để trao đổi các bước tiếp theo.
@else
Cảm ơn bạn đã quan tâm và ứng tuyển vào vị trí **{{ $jobTitle }}** tại **{{ $companyName }}**.

Rất tiếc, ở lần này hồ sơ của bạn chưa phù hợp với yêu cầu của vị trí. Mong bạn tiếp tục theo dõi các cơ hội khác trên hệ thống.
@endif

@if($feedback)
<x-mail::panel>
**Nhận xét từ nhà tuyển dụng:**

{{ $feedback }}
</x-mail::panel>
@endif

<x-mail::button :url="route('dashboard')">
Xem hồ sơ ứng tuyển của tôi
</x-mail::button>

Trân trọng,<br>
{{ config('app.name') }}
</x-mail::message>
