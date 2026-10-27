# Checklist cập nhật báo cáo Word

Đối chiếu giữa `TTDN_TRUONGVANCUONG_2121050428.docx` và mã nguồn hiện tại.
Câu chữ trong các ô **"Đề xuất bổ sung"** đã viết sẵn theo văn phong của báo cáo,
dán thẳng vào Word được.

> Làm **một lần trước khi nộp**, không cần cập nhật liên tục trong lúc còn sửa code.

---

## Nhóm A — Thiếu sót có sẵn (không do đợt sửa gần đây)

Ràng buộc "mỗi vị trí chỉ nộp một lần" đã tồn tại trong mã nguồn từ trước
(`database/migrations/2026_08_12_000001_add_unique_index_to_applications_table.php`)
nhưng **chưa được mô tả ở bất kỳ đâu trong báo cáo**.

### A1. Bảng 2.15 APPLICATIONS — thiếu dòng ràng buộc

Bảng hiện chỉ ghi `job_id → FK` và `student_id → FK`. Bổ sung một dòng vào cuối bảng:

| Tên cột | Kiểu dữ liệu | Ràng buộc | Mô tả |
|---|---|---|---|
| (ràng buộc bảng) | — | `UNIQUE (job_id, student_id)` | Mỗi sinh viên chỉ nộp được một hồ sơ cho mỗi vị trí |

### A2. Yêu cầu chức năng — Sinh viên

Sau gạch đầu dòng *"Nộp hồ sơ ứng tuyển vào vị trí công việc."*, sửa thành:

> **Đề xuất bổ sung:** Nộp hồ sơ ứng tuyển vào vị trí công việc; mỗi vị trí chỉ
> nộp được một lần duy nhất và không thể nộp lại.

### A3. Luồng ứng tuyển và đánh giá hồ sơ

Chèn thêm vào cuối đoạn *"Luồng ứng tuyển và đánh giá hồ sơ"*:

> **Đề xuất bổ sung:** Mỗi sinh viên chỉ được nộp một hồ sơ cho mỗi vị trí. Ràng
> buộc này được đặt ở tầng cơ sở dữ liệu bằng chỉ mục duy nhất trên cặp
> `(job_id, student_id)`, thay vì chỉ kiểm tra ở tầng ứng dụng — nếu sinh viên
> bấm nút ứng tuyển hai lần liên tiếp thật nhanh, hai yêu cầu chạy song song đều
> có thể vượt qua bước kiểm tra ở tầng PHP, và chỉ ràng buộc ở tầng cơ sở dữ liệu
> mới chặn được tình huống đó. Quy tắc một lần duy nhất còn mang ý nghĩa nghiệp
> vụ: nó buộc sinh viên chuẩn bị hồ sơ nghiêm túc trước khi nộp, đồng thời giữ cho
> điểm đánh giá của AI phản ánh đúng năng lực thay vì là kết quả của việc thử đi
> thử lại nhiều lần. Giao diện nêu rõ điều này trước khi sinh viên xác nhận nộp.

### A4. Kịch bản kiểm thử thủ công — thiếu ca nộp trùng

Danh sách hiện có TC_11 (ứng tuyển có CV) và TC_12 (ứng tuyển không có CV) nhưng
chưa có ca kiểm thử nộp trùng. Bổ sung:

| ID | Tên Test Case | Đầu vào | Kết quả mong đợi | Kết quả Test |
|---|---|---|---|---|
| TC_12b | Ứng tuyển lại vị trí đã nộp | Bấm ứng tuyển lần hai vào cùng một tin | Hệ thống báo "Bạn đã nộp đơn vào vị trí này rồi!", không tạo bản ghi mới | Pass |

---

## Nhóm B — Lệch do các thay đổi gần đây

### B1. Phụ lục 3 — số liệu kiểm thử ⚠️ *bắt buộc sửa*

| Hiện ghi | Sửa thành |
|---|---|
| 33 bài kiểm thử, 72 điểm kiểm chứng | **40 bài kiểm thử, 97 điểm kiểm chứng** |

Chụp lại ảnh màn hình kết quả `php artisan test` cho khớp.

### B2. Phụ lục 4 — sơ đồ thực thể liên kết ⚠️ *bắt buộc vẽ lại*

| Hiện ghi | Sửa thành |
|---|---|
| toàn bộ 21 bảng dữ liệu | **toàn bộ 22 bảng dữ liệu** |

Sơ đồ cần bổ sung:
- Bảng mới **`application_decisions`** (nhật ký quyết định tuyển dụng), quan hệ
  `applications 1–n application_decisions` và `users 1–n application_decisions`
- Cột mới **`users.rejected_at`**

### B3. Thiết kế CSDL — bảng mới cần mô tả

Thêm một bảng mô tả sau Bảng 2.15 APPLICATIONS:

**Bảng 2.x APPLICATION_DECISIONS (Nhật ký quyết định tuyển dụng)**

| Tên cột | Kiểu dữ liệu | Ràng buộc | Mô tả |
|---|---|---|---|
| `id` | bigint | PK | Khóa chính tự tăng |
| `application_id` | bigint | FK | Liên kết với applications |
| `user_id` | bigint | FK, NULL | Người ra quyết định; để trống nếu tài khoản đã bị gỡ |
| `decided_by_role` | string | NULL | Vai trò tại thời điểm quyết định |
| `decided_by_name` | string | NULL | Tên tại thời điểm quyết định |
| `previous_status` | string | NULL | Trạng thái trước khi thay đổi |
| `status` | string | | Trạng thái sau khi thay đổi |
| `hr_feedback` | text | NULL | Nhận xét kèm theo quyết định |
| `created_at` / `updated_at` | timestamp | | Thời gian |

Và bổ sung cột `rejected_at` (timestamp, NULL — "Thời điểm tài khoản bị quản trị
viên từ chối") vào Bảng USERS.

### B4. Yêu cầu chức năng — Doanh nghiệp

> **Đề xuất bổ sung:** Tra cứu nhật ký quyết định tuyển dụng của từng hồ sơ, ghi
> nhận ai đã chấp nhận hoặc từ chối, vào thời điểm nào và thay đổi từ trạng thái gì.

### B5. Yêu cầu chức năng — Quản trị hệ thống

> **Đề xuất bổ sung:** Từ chối tài khoản Doanh nghiệp hoặc Trường học không hợp lệ;
> tài khoản bị từ chối được ghi nhận thời điểm xử lý và loại khỏi hàng chờ duyệt,
> nhưng vẫn giữ lại bản ghi để truy vết.

### B6. Thiết kế giao diện — Màn hình Chi tiết Công việc & Ứng tuyển

Mô tả hiện tại: *"Hiển thị JD (Job Description) và form đính kèm CV nộp đơn ứng tuyển."*

> **Đề xuất thay thế:** Hiển thị mô tả công việc (JD) và khu vực nộp hồ sơ ứng
> tuyển. Trước khi nộp, hệ thống cảnh báo rõ rằng mỗi vị trí chỉ nộp được một lần
> và không thể chấm điểm lại, kèm lối tắt sang màn hình kiểm tra CV mặc định và
> một hộp thoại xác nhận. Sau khi nộp, màn hình hiển thị điểm phù hợp cùng nhận
> xét của AI và phản hồi của nhà tuyển dụng khi có kết quả.

### B7. Thiết kế giao diện — Màn hình Quản lý Ứng viên

Mô tả hiện tại vẫn đúng về phần xếp hạng. Bổ sung:

> **Đề xuất bổ sung:** Danh sách có thêm cột Ngày nộp hiển thị kèm khoảng thời gian
> tương đối, giúp nhà tuyển dụng nhận biết nhanh hồ sơ mới.

### B8. Thiết kế giao diện — Màn hình Chi tiết Hồ sơ ứng viên *(màn hình chưa được mô tả)*

Danh sách màn hình của phân hệ Doanh nghiệp hiện chưa nhắc tới màn hình này.

> **Đề xuất bổ sung:** Màn hình Chi tiết Hồ sơ ứng viên: hiển thị thông tin sinh
> viên, khung xem trực tiếp tệp CV, điểm và nhận xét của AI, khu vực ra quyết định
> kèm ô nhận xét, và bảng Lịch sử quyết định tuyển dụng ghi lại toàn bộ các lần
> chấp nhận hoặc từ chối cùng người thực hiện.

---

## Nhóm C — Nội dung nên bổ sung để bảo vệ tốt hơn *(không bắt buộc)*

### C1. Mục "Định hướng phát triển" — bổ sung lập luận về việc chấm thử

Hội đồng nhiều khả năng hỏi *"sao không cho sinh viên chấm thử CV rồi sửa dần?"*.
Nên có sẵn một đoạn trả lời:

> **Đề xuất bổ sung:** Một hướng phát triển từng được cân nhắc là cho phép sinh
> viên chấm thử hồ sơ nhiều lần trước khi nộp chính thức. Hướng này bị loại bỏ ở
> phạm vi hiện tại vì nó làm mất giá trị sàng lọc của điểm số: khi biết trước cách
> chấm và được thử lại không giới hạn, sinh viên sẽ chỉnh hồ sơ để chiều bộ chấm
> thay vì phản ánh đúng năng lực, khiến điểm số đo mức độ kiên trì thử lại thay vì
> mức độ phù hợp. Nếu triển khai trong tương lai, cần tách riêng lượt chấm thử khỏi
> lượt chấm chính thức, giới hạn số lượt mỗi ngày và chỉ trả về dải điểm kèm gợi ý
> định tính thay vì con số chính xác.

Lập luận đầy đủ nằm ở mục 10 của [Báo cáo kỹ thuật](BAO-CAO-KY-THUAT.md).

### C2. Mục "Hạn chế của dự án" — bổ sung hai hạn chế đã biết

> **Đề xuất bổ sung:** Việc kiểm tra quyền sở hữu dữ liệu hiện được viết lặp lại ở
> từng lớp điều khiển thay vì gom về lớp Policy của Laravel, nên tồn tại rủi ro bỏ
> sót ở một số điểm truy cập. Ngoài ra, hồ sơ ứng tuyển chưa lưu lại chính xác tệp
> CV đã dùng tại thời điểm nộp mà truy ngược theo CV mặc định hiện hành, dẫn tới
> sai lệch nếu sinh viên thay đổi CV mặc định sau khi đã ứng tuyển.

---

## Nhóm D — Hạn mức hồ sơ đang chờ *(chỉ dán sau khi đã code xong)*

> ⚠️ Bốn đoạn dưới đây mô tả chức năng **chưa tồn tại**. Chỉ đưa vào báo cáo khi
> đã triển khai xong cơ chế giới hạn hồ sơ đang chờ trong mã nguồn.

Con số hạn mức dùng thống nhất là **5**. Nếu đổi sang số khác, phải sửa ở **cả
bốn** vị trí bên dưới cho khớp.

### D1. Yêu cầu chức năng → Sinh viên

**Tìm dòng:** *"Nộp hồ sơ ứng tuyển vào vị trí công việc."*

**Thay bằng:**

> Nộp hồ sơ ứng tuyển vào vị trí công việc; mỗi vị trí chỉ nộp được một lần duy
> nhất và mỗi sinh viên được giữ tối đa 5 hồ sơ đang chờ nhà tuyển dụng phản hồi
> tại cùng một thời điểm.

### D2. Phân tích luồng hoạt động chính → Luồng ứng tuyển và đánh giá hồ sơ

**Chèn thêm vào cuối đoạn "Luồng ứng tuyển và đánh giá hồ sơ":**

> Hệ thống áp dụng hai quy tắc nhằm bảo đảm chất lượng hồ sơ đầu vào. Thứ nhất,
> mỗi sinh viên chỉ nộp được một hồ sơ cho mỗi vị trí; ràng buộc này được đặt ở
> tầng cơ sở dữ liệu bằng chỉ mục duy nhất trên cặp (job_id, student_id) nên chặn
> được cả trường hợp hai yêu cầu chạy song song cùng vượt qua bước kiểm tra ở tầng
> ứng dụng. Thứ hai, mỗi sinh viên chỉ được giữ tối đa năm hồ sơ ở trạng thái chờ
> phản hồi tại cùng một thời điểm; ngay khi nhà tuyển dụng xử lý xong một hồ sơ,
> hạn mức tương ứng được giải phóng và sinh viên có thể tiếp tục ứng tuyển.
>
> Hai quy tắc này hướng tới việc buộc sinh viên cân nhắc và chuẩn bị hồ sơ nghiêm
> túc thay vì nộp dàn trải, đồng thời giới hạn số lượt gọi dịch vụ chấm điểm tự
> động — vốn là thành phần bên ngoài tính phí theo lượng dữ liệu xử lý. So với
> phương án giới hạn bằng thời gian chờ cố định giữa hai lần ứng tuyển, cơ chế
> theo hạn mức không gây cản trở cho sinh viên nghiêm túc: chỉ cần hồ sơ đã có kết
> quả xét duyệt là hạn mức được hoàn lại ngay, thay vì phải chờ hết một khoảng
> thời gian ấn định.

### D3. Yêu cầu phi chức năng

**Thêm một gạch đầu dòng mới** (đặt cạnh mục *"Khả năng mở rộng"*):

> Kiểm soát chi phí vận hành: Số lượt gọi dịch vụ chấm điểm tự động được giới hạn
> gián tiếp thông qua hạn mức hồ sơ đang chờ của mỗi sinh viên, giúp chi phí dịch
> vụ bên ngoài không tăng mất kiểm soát khi quy mô người dùng mở rộng.

### D4. Bảng kịch bản Test chi tiết → Phân hệ Sinh viên

**Thêm một dòng ngay sau TC_12:**

| ID | Tên Test Case | Đầu vào | Kết quả mong đợi | Kết quả Test (Lần 1) | Khắc phục & Test lại |
|---|---|---|---|---|---|
| TC_12c | Ứng tuyển khi đã đạt hạn mức hồ sơ chờ | Sinh viên đang có 5 hồ sơ ở trạng thái chờ, bấm ứng tuyển vị trí thứ sáu | Hệ thống từ chối và hiển thị thông báo nêu rõ số hồ sơ đang chờ; không tạo bản ghi mới | Pass | - |

---

## Tóm tắt mức ưu tiên

| Mức | Hạng mục |
|---|---|
| ⚠️ **Bắt buộc** | B1 (số liệu test), B2 (ERD + số bảng), B3 (mô tả bảng mới) |
| **Nên làm** | A1, A2, A3 (ràng buộc nộp một lần), B4–B8 (chức năng & giao diện mới) |
| Tùy chọn | A4 (test case), C1, C2 |
| **Chỉ khi đã code xong** | D1–D4 (hạn mức hồ sơ đang chờ) |
