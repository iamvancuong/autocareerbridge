# Sổ tay bảo vệ — Auto Career Bridge

Tổng hợp từ toàn bộ tài liệu trong `docs/`. Đây là bản cầm tay cho buổi bảo vệ:
số liệu cần thuộc, câu trả lời cho các câu hỏi dễ bị vặn, kịch bản demo và bảng
tra nguồn.

Cập nhật 18/08/2026 · Tài liệu chi tiết: [Báo cáo kỹ thuật](BAO-CAO-KY-THUAT.md) ·
[Phân quyền & vai trò](PHAN-QUYEN-VAI-TRO.md) · [Đối chiếu báo cáo ↔ code](DOI-CHIEU-BAO-CAO-CODE.md)

---

## 1. Giới thiệu 60 giây

> Auto Career Bridge là nền tảng web kết nối ba chủ thể **Nhà trường – Sinh viên –
> Doanh nghiệp** trong tuyển dụng và thực tập. Hệ thống số hoá trọn quy trình từ
> đăng tin, kiểm duyệt tin, nộp hồ sơ tới phản hồi kết quả. Điểm khác biệt là khâu
> **chấm điểm độ phù hợp giữa CV và yêu cầu công việc bằng AI**: nội dung CV được
> trích xuất tự động, gửi sang mô hình ngôn ngữ lớn để chấm từ 0 đến 100 kèm nhận
> xét, và danh sách ứng viên được xếp hạng theo điểm đó — nhưng quyền quyết định
> cuối cùng vẫn thuộc về con người.

Công nghệ: Laravel 12 · MySQL 8 · Blade + Bootstrap 5 · Docker · PHPUnit.

---

## 2. Số liệu cần thuộc

| Hạng mục | Con số |
|---|---|
| Vai trò người dùng | **6** (4 chính + 2 vai trò cấp dưới) |
| Bảng cơ sở dữ liệu | **22** (không tính bảng `migrations` nội bộ của Laravel) |
| Kiểm thử tự động | **45 bài / 108 điểm kiểm chứng**, toàn bộ đạt |
| Kịch bản kiểm thử thủ công | **30** |
| Test riêng cho phân quyền | **9** |
| Hạn mức hồ sơ chờ của mỗi sinh viên | **5** |
| Thời gian chờ gửi lại yêu cầu hợp tác sau khi bị từ chối | **30 ngày** |

Lệnh tự kiểm ngay trước khi vào phòng:

```bash
php artisan test
```

---

## 3. Sáu vai trò

| Vai trò | Ai tạo | Cần duyệt | Đặc quyền riêng |
|---|---|:---:|---|
| `admin` | Seeder | — | Duyệt tin, duyệt/từ chối tài khoản tổ chức, quản lý danh mục ngành |
| `company` | Tự đăng ký | ✅ | **Quản lý tài khoản Nhân sự HR** |
| `hiring` | Do `company` tạo | ❌ | *(như company, trừ quản lý HR)* |
| `university` | Tự đăng ký | ✅ | **Quản lý tài khoản Giáo vụ** |
| `academic_affairs` | Do `university` tạo | ❌ | *(như university, trừ quản lý giáo vụ)* |
| `student` | Tự đăng ký | ❌ | Tải CV, ứng tuyển |

**Câu chốt:** quản trị viên gác cổng ở tầng **tổ chức**; tài khoản nhân viên do
tổ chức tự quản lý. Giữa vai trò chính và vai trò phụ chỉ khác **đúng một điều** —
quyền tạo tài khoản; nếu nhân viên tự tạo được nhân viên khác thì họ có thể tự
nhân bản quyền truy cập mà chủ tổ chức không hay biết.

---

## 4. Ba luồng nghiệp vụ lõi

**Đăng tin → kiểm duyệt → công khai.** Doanh nghiệp soạn tin bằng CKEditor; nội
dung HTML được lọc lại ở tầng máy chủ; tin ở trạng thái chờ tới khi quản trị viên
duyệt. Sửa tin thì phải duyệt lại.

**Ứng tuyển → chấm điểm → quyết định.** Sinh viên nộp CV mặc định → hệ thống trích
văn bản từ PDF → gửi prompt sang AI → nhận điểm 0–100 kèm nhận xét → nhà tuyển dụng
xem danh sách đã xếp hạng, mở từng hồ sơ rồi chấp nhận hoặc từ chối kèm nhận xét →
sinh viên nhận cả thông báo trong hệ thống lẫn email.

**Hợp tác Trường ↔ Doanh nghiệp.** Hai bên đều gửi được lời mời; bên nhận duyệt
hoặc từ chối; bị từ chối thì chờ 30 ngày mới gửi lại được, và bản ghi cũ được tái
sử dụng để lịch sử quan hệ nằm gọn trong một dòng dữ liệu.

---

## 5. Sáu quyết định thiết kế đáng mang ra bảo vệ

### 5.1 Không bịa điểm khi AI không phản hồi

Khi thiếu khoá API, mất mạng hoặc AI trả sai định dạng, hệ thống ghi
`ai_score = NULL` kèm lý do cụ thể, **không sinh điểm giả**.

> Nếu bịa điểm, nhà tuyển dụng sẽ loại ứng viên dựa trên một con số vô căn cứ mà
> không hề biết. Với hệ thống tác động tới cơ hội việc làm, thừa nhận "chưa đánh
> giá được" luôn an toàn hơn phỏng đoán.

### 5.2 Hồ sơ chưa chấm được xếp cuối, không xếp đầu

`ORDER BY ai_score IS NULL, ai_score DESC` — mệnh đề đầu đẩy hồ sơ chưa chấm xuống
cuối để không bị hiểu nhầm là ứng viên yếu.

### 5.3 Mỗi vị trí chỉ nộp một lần + hạn mức 5 hồ sơ chờ

Ràng buộc `UNIQUE (job_id, student_id)` đặt ở **tầng cơ sở dữ liệu**, không chỉ
kiểm tra ở tầng PHP — vì hai request song song đều có thể vượt qua bước kiểm tra
ứng dụng. Hạn mức 5 hồ sơ chờ ngăn việc nộp dàn trải và giới hạn chi phí gọi AI;
hạn mức được hoàn lại ngay khi nhà tuyển dụng phản hồi.

### 5.4 Nhật ký quyết định tuyển dụng

Bảng `applications` chỉ giữ trạng thái mới nhất. Khi chủ doanh nghiệp chấp nhận
rồi nhân viên tuyển dụng từ chối lại, không còn dấu vết ai đã đổi. Bảng
`application_decisions` ghi thêm từng lượt (không sửa, không xoá), lưu cả vai trò
và tên **tại thời điểm quyết định** vì hai thông tin này có thể thay đổi về sau.

### 5.5 Lọc HTML ở tầng máy chủ

Nội dung CKEditor được in ra bằng `{!! !!}` nên **không thể tin trình soạn thảo
phía client**: người dùng hoàn toàn có thể bỏ qua giao diện và POST thẳng mã độc.
Hệ thống lọc lại bằng HTMLPurifier với danh sách thẻ giới hạn đúng bằng thanh công
cụ của editor.

### 5.6 Lỗi gửi email không được làm hỏng nghiệp vụ

Hàm gửi thư bọc `try/catch` và chỉ ghi log khi thất bại. Máy chủ thư có thể ngưng,
nhưng thao tác duyệt hồ sơ đã ghi vào CSDL thì không được phép văng lỗi giữa chừng.

---

## 6. Mười câu hội đồng hay hỏi — trả lời sẵn

**1. Phân quyền cài ở đâu?**
Ba tầng: middleware `auth` → middleware tự viết `EnsureUserHasRole` khai báo ngay
trên route (`role:company,hiring`) → kiểm tra quyền sở hữu dữ liệu trong controller.
Có 9 bài test riêng cho phân quyền.

**2. Vai trò phụ dùng chung dữ liệu tổ chức bằng cách nào?**
Hai thuộc tính truy xuất `active_company` và `active_university` trong lớp `User`.
Nhờ đó mọi controller chỉ làm việc với một khái niệm "tổ chức hiện hành", không
phân nhánh theo vai trò.

**3. Admin duyệt những vai trò nào?**
Chỉ `company` và `university` — hai vai trò tự đăng ký, cần xác minh là pháp nhân
thật. Sinh viên dùng được ngay; nhân viên do tổ chức tự tạo.

**4. Sinh viên nộp trùng được không?**
Không. Ràng buộc `UNIQUE (job_id, student_id)` ở tầng CSDL, cộng kiểm tra ở tầng
ứng dụng. Có test chứng minh.

**5. Sao không cho sinh viên chấm thử CV rồi sửa dần?**
Vì nó phá giá trị sàng lọc của điểm số: biết trước cách chấm và thử lại không giới
hạn thì sinh viên sẽ chỉnh CV để chiều bộ chấm, khiến điểm đo "mức độ kiên trì thử
lại" thay vì "mức độ phù hợp". *(Đừng lấy chi phí token làm lý do chính — mỗi lượt
chấm chưa tới một xu, hội đồng sẽ bác ngay.)* Nếu làm, phải tách lượt chấm thử khỏi
lượt chính thức, giới hạn số lượt và chỉ trả dải điểm thay vì con số chính xác.

**6. AI chấm sai thì sao?**
Điểm chỉ để **xếp hạng và tham khảo**, quyền quyết định vẫn thuộc con người: nhà
tuyển dụng đọc CV, xem nhận xét rồi tự chọn. Hồ sơ không chấm được thì hiển thị
gạch ngang kèm lý do chứ không có điểm giả.

**7. Đọc được những định dạng CV nào?**
Chỉ PDF có lớp văn bản. Word và PDF ảnh scan chưa đọc được — hệ thống ghi rõ lý do
thay vì tạo dữ liệu giả. Đây là hạn chế đã nêu trong báo cáo.

**8. Có xử lý bất đồng bộ không?**
Chưa. Lời gọi AI hiện chạy đồng bộ trong request nên sinh viên phải chờ. Hạ tầng
hàng đợi đã sẵn sàng (`QUEUE_CONNECTION=database`), chuyển sang xử lý nền là bước
tiếp theo — đã ghi trong định hướng phát triển.

**9. Kiểm thử thế nào?**
30 kịch bản thủ công + 45 bài kiểm thử tự động PHPUnit với 108 điểm kiểm chứng,
chạy trên cơ sở dữ liệu trong bộ nhớ. Mỗi lỗi từng phát hiện đều có test khoá lại
để không tái diễn.

**10. Cơ sở dữ liệu có bao nhiêu bảng?**
22 bảng, chuẩn hoá tới dạng chuẩn thứ ba, ràng buộc khoá ngoại đầy đủ, quản lý
phiên bản bằng migration.

---

## 7. Kịch bản demo

Đăng nhập bằng các tài khoản demo, mật khẩu tất cả là `password`.

| Bước | Tài khoản | Trình diễn |
|---|---|---|
| 1 | `an.nv01@demo.local` | Sinh viên: xem tin đúng chuyên ngành được ưu tiên lên đầu; mở chi tiết → thấy cảnh báo **"chỉ nộp một lần"** và **"Lượt nộp còn lại 2/5"** |
| 2 | — | Nộp CV → điểm AI và nhận xét hiện ngay |
| 3 | `hr.fpt@demo.local` | Doanh nghiệp: danh sách ứng viên **xếp theo điểm AI giảm dần**, hồ sơ chưa chấm nằm cuối; mở chi tiết → xem CV, điểm, nhận xét → bấm Chấp nhận |
| 4 | `ts.fpt@demo.local` | Nhân viên tuyển dụng: **không thấy menu Nhân sự HR**; gõ thẳng `/company/hirings` → **403** |
| 5 | `ts.fpt@demo.local` | Từ chối lại hồ sơ vừa được chấp nhận → **bảng Lịch sử quyết định** hiện rõ ai đổi, đổi từ trạng thái gì |
| 6 | — | Mở http://localhost:8025 → thấy email đã gửi cho sinh viên ở cả hai lần |
| 7 | `admin@demo.local` | Duyệt tin tuyển dụng, duyệt/từ chối tài khoản tổ chức |

**Bước 4 và 5 là hai bước ăn điểm nhất** — một chứng minh phân quyền chặt, một
chứng minh khả năng truy vết trách nhiệm.

Truy vấn SQL trình diễn (nhớ cổng **3307**, và bắt buộc có `--default-character-set`
nếu không tiếng Việt ra dấu hỏi):

```bash
docker exec -it acb_mysql mysql --default-character-set=utf8mb4 -uroot -psecret autocareerbridge
```

---

## 8. Tra nguồn nhanh

| Hỏi về | Tệp | Dòng |
|---|---|---|
| Danh sách vai trò | `database/migrations/0001_01_01_000000_create_users_table.php` | 14 |
| Ai cần duyệt tài khoản | `app/Http/Controllers/AuthController.php` | 60 |
| Vai trò phụ mượn hồ sơ tổ chức | `app/Models/User.php` | 37 |
| Middleware phân quyền | `app/Http/Middleware/EnsureUserHasRole.php` | 19 |
| Khu vực nào chặn vai trò nào | `routes/web.php` | 125, 131, 139, 149 |
| Vai trò phụ bị hạn chế gì | `app/Http/Controllers/Company/HiringController.php` | 14 |
| Không bịa điểm AI | `app/Services/ApplicationService.php` | `markEvaluationUnavailable()` |
| Xếp hạng ứng viên | `app/Http/Controllers/Company/CompanyApplicationController.php` | `index()` |
| Hạn mức hồ sơ chờ | `config/recruitment.php` + `ApplicationService::apply()` | — |
| Nhật ký quyết định | `app/Http/Controllers/Company/CompanyApplicationController.php` | `update()` |
| Lọc HTML chống XSS | `app/Http/Controllers/Company/CompanyJobController.php` | `purifyRichTextInput()` |
| Chặn nộp trùng | `database/migrations/2026_08_12_000001_add_unique_index_to_applications_table.php` | — |

---

## 9. Điểm yếu — nói thế nào cho đúng

Thừa nhận thẳng, kèm hướng khắc phục. Đây là phần ghi điểm chứ không phải mất điểm.

| Hạn chế | Cách trình bày |
|---|---|
| Kiểm tra quyền sở hữu viết lặp ~15 lần trong controller thay vì gom vào Policy | "Em nhận ra khi cùng một điều kiện lặp lại từ ba nơi trở lên thì nên đưa lên tầng trừu tượng cao hơn. Đây là hạng mục refactor em đưa vào định hướng phát triển." |
| Hồ sơ không lưu `resume_id`, truy ngược theo CV mặc định hiện hành | "Nếu sinh viên đổi CV mặc định sau khi nộp thì nhà tuyển dụng sẽ xem nhầm CV so với bản AI đã chấm. Khắc phục bằng cách lưu thẳng `resume_id` vào đơn." |
| Gọi AI đồng bộ trong request | "Hàng đợi đã cấu hình sẵn, chỉ chưa chuyển tác vụ sang xử lý nền." |
| Chỉ đọc được PDF có lớp văn bản | "Em chọn làm tốt định dạng phổ biến nhất và ghi rõ hạn chế, thay vì làm dở dang cả ba định dạng." |
| Chưa có bản di động | Đã nêu trong mục Hạn chế của báo cáo. |

> ⚠️ **Cần xử lý trước khi bảo vệ:** báo cáo khẳng định *"ba lỗ hổng bảo mật đều đã
> được khắc phục"* (nói ở 3 vị trí), nhưng còn **2 lỗ hổng chưa vá**: IDOR ở duyệt
> hợp tác (`CollaborationUIController::update()` không kiểm tra quyền sở hữu) và
> xem được tin chưa duyệt qua URL trực tiếp (`JobUIController::show()` không lọc
> `is_approved`). Hoặc vá code, hoặc sửa lại câu chữ trong báo cáo — đừng để nguyên.

---

## 10. Checklist trước buổi bảo vệ

**Đã xong**

- [x] Báo cáo Word: 16 chỗ chèn nội dung mới, 18 chỗ đồng bộ số liệu
- [x] Code khớp mô tả trong báo cáo (45 test/108 assertion đều đạt)
- [x] Bốn tài liệu kỹ thuật trong `docs/`

**Còn phải làm**

- [ ] **Vá 2 lỗ hổng** còn lại, hoặc sửa câu "đều đã được khắc phục" trong báo cáo
- [ ] **Vẽ lại ERD** — dán [schema-erd.sql](schema-erd.sql) vào https://drawsql.app/
- [ ] **Chụp lại ảnh** — kết quả `php artisan test` (chụp từ terminal, **không** dùng trang `/testing-report` vì trang này đang lỗi), màn hình Quản lý tài khoản, màn hình Duyệt ứng viên
- [ ] **Chụp bổ sung 3 màn hình chưa có ảnh**: Chi tiết Hồ sơ ứng viên (có bảng lịch sử quyết định), Chi tiết công việc phía sinh viên (có cảnh báo nộp một lần), Dashboard sinh viên
- [ ] **Mở Word, `Ctrl+A` → `F9`** để đánh lại số bảng và cập nhật mục lục
- [ ] Sửa thuật ngữ "học sinh" thành "sinh viên" ở Hình 2.24 và 2.25
- [ ] Khởi động Docker và `php artisan serve` trước giờ bảo vệ; mở sẵn Mailpit (http://localhost:8025)
