# Đối chiếu báo cáo ↔ mã nguồn

Mỗi khẳng định trong báo cáo Word ứng với vị trí nào trong mã nguồn và bài kiểm
thử nào chứng minh. Dùng để tự kiểm trước khi nộp, và để mở đúng chỗ khi hội đồng
hỏi *"chỗ này em làm ở đâu?"*.

Cập nhật: 18/08/2026 · `php artisan test` → **45 test / 108 assertion, toàn bộ đạt**

---

## 1. Hạn mức hồ sơ đang chờ *(đã cập nhật vào báo cáo — nhóm D)*

| Báo cáo khẳng định | Mã nguồn | Kiểm thử chứng minh |
|---|---|---|
| "mỗi sinh viên chỉ được giữ tối đa **5** hồ sơ đang chờ" | `config/recruitment.php` → `max_pending_applications` | `test_limit_is_read_from_configuration` |
| "hồ sơ vượt hạn mức bị từ chối, thông báo nêu rõ số hồ sơ đang chờ" | `ApplicationService::apply()` | `test_student_cannot_exceed_the_pending_application_limit` |
| "không tạo bản ghi mới" (TC_12c) | cùng trên, kiểm tra nằm trong `DB::transaction` | `test_no_extra_record_is_created_when_the_limit_is_reached` |
| "ngay khi nhà tuyển dụng xử lý xong một hồ sơ, hạn mức được giải phóng" | đếm theo `status = 'pending'`, không lưu bộ đếm riêng | `test_quota_is_released_once_the_recruiter_has_decided` |
| "mỗi vị trí chỉ được nộp một lần duy nhất" | `UNIQUE (job_id, student_id)` — migration `2026_08_12_000001` | `test_applying_twice_to_the_same_job_is_still_blocked` |
| "chặn được cả trường hợp hai yêu cầu chạy song song" | `Student::whereKey(...)->lockForUpdate()` + bắt mã lỗi `23000` | *(ràng buộc CSDL; không mô phỏng được bằng test đơn tiến trình)* |

**Kiểm chứng thủ công đã thực hiện:** đăng nhập `an.nv01@demo.local` (3 hồ sơ chờ)
→ trang tin hiện *"Lượt nộp còn lại 2/5"*; hạ ngưỡng xuống 3 → nút nộp biến mất,
thay bằng cảnh báo đã đạt hạn mức; gửi POST thẳng vào `/jobs/5/apply` vượt mặt
giao diện → **không tạo được bản ghi** (số đơn giữ nguyên 7).

> Giao diện: `resources/views/jobs/show.blade.php` (lượt còn lại + chặn khi hết hạn
> mức), `resources/views/dashboards/student.blade.php` (ô "Đang chờ xét duyệt" dạng N/5).

---

## 2. Nhật ký quyết định tuyển dụng *(chưa đưa vào báo cáo — xem nhóm B)*

| Nội dung | Mã nguồn | Kiểm thử |
|---|---|---|
| Ghi lại mọi lượt chấp nhận / từ chối | `CompanyApplicationController::update()` trong `DB::transaction` | `test_each_decision_is_appended_to_the_log` |
| Truy được ai đảo quyết định của ai | bảng `application_decisions`, cột `decided_by_role` | `test_log_shows_who_overturned_an_earlier_decision` |
| Mỗi quyết định đều sinh thông báo | `NotificationService::send()` + `mail()` | `test_student_is_notified_on_every_decision` |
| Hiển thị trên trang chi tiết hồ sơ | `resources/views/company/applications/show.blade.php` | `test_decision_log_is_visible_on_the_detail_page` |

---

## 3. Phê duyệt & từ chối tài khoản *(chưa đưa vào báo cáo — xem nhóm B)*

| Nội dung | Mã nguồn | Kiểm thử |
|---|---|---|
| Tài khoản bị từ chối rời hàng chờ | `users.rejected_at` + `whereNull('rejected_at')` | `test_rejected_account_leaves_the_pending_queue` |
| Bản ghi vẫn được giữ để truy vết | không xoá, chỉ đánh dấu | `test_pending_list_no_longer_shows_a_rejected_account` |
| Duyệt lại đảo được quyết định | `approve()` xoá `rejected_at` | `test_admin_can_reverse_a_rejection_by_approving` |

---

## 4. Các khẳng định sẵn có trong báo cáo — đã đối chiếu, vẫn đúng

| Báo cáo | Mã nguồn | Trạng thái |
|---|---|---|
| Phụ lục 1 — mã nguồn `EnsureUserHasRole` | `app/Http/Middleware/EnsureUserHasRole.php` | ✅ khớp từng dòng |
| Phụ lục 2 — mã nguồn `markEvaluationUnavailable` | `app/Services/ApplicationService.php` | ✅ khớp từng dòng |
| "phân quyền được bảo vệ bằng **chín** bài kiểm thử" | `tests/Feature/RoleAccessControlTest.php` | ✅ đúng 9 test |
| "sáu vai trò, hai vai trò cấp dưới mượn hồ sơ qua `active_company` / `active_university`" | `app/Models/User.php:37,41` | ✅ |
| "xếp hạng theo `ai_score` giảm dần, hồ sơ chưa chấm xếp cuối" | `CompanyApplicationController::index()` | ✅ mặc định không đổi |
| "ghi `ai_score = null` kèm lý do thay vì bịa điểm" | `markEvaluationUnavailable()` | ✅ + 2 test |
| "lọc HTML ở tầng máy chủ bằng HTMLPurifier" | `CompanyJobController::purifyRichTextInput()` | ✅ + `JobRichTextTest` |
| "cooldown 30 ngày sau khi yêu cầu hợp tác bị từ chối" | `CollaborationService::sendRequest()` | ✅ |
| "30 kịch bản kiểm thử thủ công" | — | ✅ không đổi |

---

## 5. Trạng thái cập nhật báo cáo Word

Toàn bộ phần **chữ** đã được cập nhật vào `TTDN_TRUONGVANCUONG_2121050428.docx`
(bản sao lưu trước khi sửa: `...BACKUP-2026-08-18.docx`).

| Mục | Nội dung | Trạng thái |
|---|---|---|
| D1–D4 | Hạn mức hồ sơ chờ: yêu cầu chức năng, luồng ứng tuyển, yêu cầu phi chức năng, TC_12c | ✅ đã sửa |
| A1 | Bảng APPLICATIONS — thêm ràng buộc `UNIQUE (job_id, student_id)` | ✅ đã sửa |
| A4 | Thêm ca kiểm thử TC_12b (nộp trùng cùng một vị trí) | ✅ đã sửa |
| B1 | Số liệu kiểm thử — đồng bộ **10 chỗ** về 45 test / 108 assertion | ✅ đã sửa |
| B2 | Số bảng — đồng bộ **8 chỗ** từ 21 sang 22 | ✅ đã sửa |
| B3 | Bảng USERS thêm cột `rejected_at`; thêm hẳn bảng mô tả `APPLICATION_DECISIONS` (10 dòng) kèm đoạn giới thiệu | ✅ đã sửa |
| B4 | Yêu cầu chức năng Doanh nghiệp — tra cứu nhật ký quyết định | ✅ đã sửa |
| B5 | Yêu cầu chức năng Quản trị — từ chối tài khoản tổ chức | ✅ đã sửa |
| B6 | Màn hình Chi tiết Công việc — cảnh báo nộp một lần, lượt còn lại, hộp thoại xác nhận | ✅ đã sửa |
| B7 | Màn hình Quản lý Ứng viên — thêm cột Ngày nộp | ✅ đã sửa |
| B8 | Bổ sung Màn hình Chi tiết Hồ sơ ứng viên (trước đây thiếu hẳn) | ✅ đã sửa |
| C1 | Định hướng phát triển — lập luận vì sao không cho chấm thử nhiều lần | ✅ đã sửa |
| C2 | Hạn chế — phân quyền chưa gom vào Policy, hồ sơ không lưu `resume_id` | ✅ đã sửa |

### Còn lại — cần làm thủ công

| Việc | Ghi chú |
|---|---|
| **Vẽ lại ERD (Phụ lục 4)** | Thêm bảng `application_decisions` (quan hệ `applications 1–n`, `users 1–n`) và cột `users.rejected_at`. Phần chữ đã ghi 22 bảng, chỉ còn thiếu hình. |
| **Chụp lại ảnh kết quả `php artisan test`** | Ảnh hiện tại là kết quả cũ; chạy lại và chụp cho khớp 45/108. |
| **Cập nhật mục lục bảng biểu** | Bảng `APPLICATION_DECISIONS` mới chèn dùng trường `SEQ` nên số bảng tự đánh lại: mở Word, chọn toàn bộ (Ctrl+A) rồi bấm F9 để cập nhật trường và mục lục. |

Câu chữ dự phòng cho các mục trên: xem [CAP-NHAT-BAO-CAO.md](CAP-NHAT-BAO-CAO.md).

---

## 6. Lệnh tự kiểm nhanh trước khi nộp

Chạy bộ kiểm thử và đối chiếu con số với báo cáo:

```bash
php artisan test
```

Đếm số bảng trong cơ sở dữ liệu (trừ bảng `migrations` — cách đếm dùng trong báo cáo):

```bash
docker exec acb_mysql mysql -uroot -psecret autocareerbridge -N -e "SELECT COUNT(*)-1 FROM information_schema.tables WHERE table_schema='autocareerbridge';"
```

Xác nhận hạn mức đang áp dụng:

```bash
php artisan tinker --execute="echo config('recruitment.max_pending_applications');"
```

> Nếu đổi hạn mức khỏi giá trị 5, phải sửa đồng bộ **bốn** vị trí trong báo cáo
> (yêu cầu chức năng Sinh viên, luồng ứng tuyển, yêu cầu phi chức năng, TC_12c).
