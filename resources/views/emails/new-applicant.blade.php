<x-mail::message>
# Có ứng viên mới

Ứng viên **{{ $studentName }}**@if($universityName) ({{ $universityName }})@endif vừa nộp hồ sơ vào vị trí **{{ $jobTitle }}**.

@if(!is_null($aiScore))
Điểm phù hợp do hệ thống AI chấm sơ bộ: **{{ $aiScore }}/100**.
@else
Hệ thống chưa chấm được điểm phù hợp cho hồ sơ này, vui lòng xem xét thủ công.
@endif

<x-mail::button :url="$reviewUrl">
Xem danh sách ứng viên
</x-mail::button>

Trân trọng,<br>
{{ config('app.name') }}
</x-mail::message>
