<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Hạn mức hồ sơ đang chờ phản hồi
    |--------------------------------------------------------------------------
    |
    | Số hồ sơ tối đa mà một sinh viên được giữ ở trạng thái `pending` tại cùng
    | một thời điểm. Khi nhà tuyển dụng đã chấp nhận hoặc từ chối một hồ sơ, hạn
    | mức tương ứng được giải phóng ngay.
    |
    | Mục đích: buộc sinh viên cân nhắc và chuẩn bị hồ sơ nghiêm túc thay vì nộp
    | dàn trải, đồng thời giới hạn số lượt gọi dịch vụ chấm điểm tự động — vốn là
    | thành phần bên ngoài tính phí theo lượng dữ liệu xử lý.
    |
    | Đặt ở đây thay vì viết cứng trong mã nguồn để đổi được mà không phải sửa
    | nghiệp vụ, và để con số này có một chỗ duy nhất làm nguồn tham chiếu.
    |
    */

    'max_pending_applications' => (int) env('MAX_PENDING_APPLICATIONS', 5),

];
