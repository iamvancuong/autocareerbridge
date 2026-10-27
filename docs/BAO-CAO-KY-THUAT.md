# Báo cáo kỹ thuật — Auto Career Bridge

Tài liệu ghi lại các quyết định kỹ thuật quan trọng, lý do đằng sau chúng và
cách kiểm chứng. Mục đích: phục vụ buổi bảo vệ trước hội đồng, để mỗi lựa chọn
trong mã nguồn đều giải thích được.

Cập nhật: 17/08/2026 · Số lượng kiểm thử tự động: **40 test / 97 assertion**

---

## 1. Nhật ký quyết định tuyển dụng

### Vấn đề

Bảng `applications` chỉ lưu **trạng thái mới nhất** (`pending` / `accepted` /
`rejected`). Khi thử nghiệm, chúng tôi phát hiện kịch bản sau không để lại dấu vết:

1. Tài khoản **chủ doanh nghiệp** vào chấp nhận một hồ sơ.
2. Tài khoản **nhân viên tuyển dụng** (role `hiring`, dùng chung hồ sơ công ty)
   vào từ chối chính hồ sơ đó.
3. Kết quả: hồ sơ mang trạng thái `rejected`, và **không có thông tin nào** cho
   biết trước đó nó đã từng được chấp nhận, ai đổi, đổi lúc nào.

Với một hệ thống tuyển dụng, đây là lỗ hổng về trách nhiệm giải trình: doanh
nghiệp không truy được ai đã ra quyết định nào.

### Cách xử lý

Bổ sung bảng **`application_decisions`** — nhật ký chỉ ghi thêm (append-only),
không sửa và không xóa:

| Cột | Ý nghĩa |
|---|---|
| `application_id` | Hồ sơ được quyết định |
| `user_id` | Người ra quyết định (`nullOnDelete` để giữ nhật ký kể cả khi tài khoản bị gỡ) |
| `decided_by_role` | **Chụp lại** vai trò tại thời điểm quyết định |
| `decided_by_name` | **Chụp lại** tên tại thời điểm quyết định |
| `previous_status` | Trạng thái trước khi đổi |
| `status` | Trạng thái mới |
| `hr_feedback` | Nhận xét kèm theo lượt quyết định đó |

**Vì sao chụp lại `role` và `name` thay vì luôn join sang bảng `users`?**
Vai trò và tên của một tài khoản có thể thay đổi về sau. Nhật ký phải phản ánh
đúng sự thật **tại thời điểm xảy ra**, nên hai trường này được lưu tách rời.

Việc cập nhật trạng thái và ghi nhật ký được bọc trong một **transaction**
(`DB::transaction`): nếu chỉ một trong hai thành công, hồ sơ sẽ mang trạng thái
không giải thích được.

### Giao diện

Trang chi tiết hồ sơ (`/company/applications/{id}`) có thêm bảng **"Lịch sử
quyết định tuyển dụng"** ở cuối trang, hiển thị: thời điểm, người quyết định,
vai trò (badge phân biệt Chủ doanh nghiệp / Nhân viên tuyển dụng), luồng thay
đổi trạng thái, và nhận xét kèm theo. Khi có từ 2 quyết định trở lên, hệ thống
cảnh báo rõ *"Hồ sơ này đã bị đổi quyết định N lần"*.

### Kiểm chứng

Tái hiện đúng kịch bản trên hồ sơ số 60 bằng request HTTP thật:

```
| decided_by_name          | decided_by_role | previous_status | status   |
|--------------------------|-----------------|-----------------|----------|
| Nguyễn Thu Trang         | hiring          | accepted        | rejected |
| Công ty TNHH Phần mềm FPT| company         | accepted        | accepted |
```

Khóa lại bằng 4 test tự động trong `tests/Feature/ApplicationDecisionLogTest.php`.

### Điểm còn để mở

Hệ thống hiện **cho phép** đổi quyết định (chỉ ghi nhận, không chặn). Đây là lựa
chọn có chủ đích: thực tế doanh nghiệp vẫn cần đảo quyết định khi ứng viên rút
lui hoặc vị trí thay đổi. Nếu hội đồng yêu cầu siết chặt, có thể bổ sung quy tắc
"chỉ chủ doanh nghiệp mới được đảo quyết định của người khác" — nhật ký hiện tại
đã đủ dữ liệu để cài quy tắc này.

---

## 2. Cơ chế thông báo — gọi hàm nào?

Toàn bộ thông báo đi qua lớp **`App\Services\NotificationService`** với **hai
hàm** tách bạch:

### `send()` — thông báo trong hệ thống (hiện ở chuông trên navbar)

```php
$notifService->send(
    $userId,                              // người nhận
    'Hồ sơ đã được chấp nhận',            // tiêu đề
    "Hồ sơ ứng tuyển của bạn cho vị trí {$jobTitle} tại {$companyName} đã được chấp nhận!",
    'success',                            // loại: info | success | warning | danger
    route('company.applications.index')   // (tùy chọn) bấm vào thì đi đâu
);
```

Ghi một bản ghi vào bảng `notifications`.

### `mail()` — gửi email

```php
$notifService->mail(
    $application->student->user->email,
    new \App\Mail\ApplicationStatusMail($application, $companyName)
);
```

**Điểm quan trọng cần nêu với hội đồng:** hàm `mail()` bọc toàn bộ thao tác gửi
thư trong `try/catch` và **chỉ ghi log khi thất bại**. Lý do: máy chủ thư có thể
tạm ngưng hoặc cấu hình sai, nhưng điều đó **không được phép làm hỏng nghiệp vụ
đang chạy**. Nếu để lỗi SMTP văng ra, thao tác duyệt hồ sơ sẽ thất bại giữa
chừng dù quyết định đã ghi vào cơ sở dữ liệu.

### Ba mẫu thư hiện có

| Mailable | Gửi khi nào | Người nhận |
|---|---|---|
| `NewApplicantMail` | Sinh viên nộp hồ sơ | Doanh nghiệp (kèm sẵn điểm AI để sàng lọc nhanh) |
| `ApplicationStatusMail` | Doanh nghiệp ra quyết định | Sinh viên |
| `AccountApprovedMail` | Quản trị viên duyệt tài khoản | Tổ chức |

### Quy tắc áp dụng

**Mỗi lượt quyết định tuyển dụng đều sinh cả hai**: một thông báo trong hệ thống
và một email. Đã kiểm chứng: sau kịch bản "chấp nhận rồi từ chối", sinh viên nhận
đúng 2 thông báo (`success` và `danger`) và 2 email tương ứng trong hộp thư
Mailpit.

Xem thư hệ thống gửi ra khi demo: **http://localhost:8025**

---

## 3. Chấm điểm CV bằng AI

### Nguyên tắc thiết kế: không bịa số

Khi không gọi được dịch vụ AI (thiếu API key, lỗi mạng, phản hồi sai định dạng),
hệ thống ghi `ai_score = NULL` kèm lý do cụ thể trong `ai_review`, **tuyệt đối
không sinh ra một điểm số giả**. Xem `ApplicationService::markEvaluationUnavailable()`.

> Nếu hệ thống bịa điểm, nhà tuyển dụng sẽ loại ứng viên dựa trên một con số vô
> nghĩa mà không hề hay biết. Thà hiển thị "chưa có đánh giá" còn hơn.

Tương tự ở khâu đọc CV (`StudentResumeController::extractText()`): hệ thống chỉ
trích xuất được văn bản từ tệp `.pdf` có lớp text. Với `.doc/.docx` hoặc PDF bản
scan, nội dung trả về là một ghi chú nêu rõ lý do, không phải dữ liệu bịa.

### Xếp hạng ứng viên

Danh sách ứng viên mặc định sắp theo điểm AI giảm dần:

```sql
ORDER BY ai_score IS NULL, ai_score DESC
```

Mệnh đề `ai_score IS NULL` đứng trước đẩy các hồ sơ **chưa chấm được** xuống
cuối, tránh bị hiểu nhầm là ứng viên điểm thấp.

### Sự cố đã xử lý: lỗi chứng chỉ SSL

Trên Windows, PHP không đi kèm bộ chứng chỉ CA. Máy phát triển có cả
`curl.cainfo` lẫn `openssl.cafile` bỏ trống, khiến **mọi** kết nối HTTPS phát đi
từ PHP đều thất bại:

```
cURL error 60: SSL certificate problem: unable to get local issuer certificate
```

**Cách xử lý đúng:** tải bộ chứng chỉ `cacert.pem` từ dự án curl và khai báo hai
tham số trên trong `php.ini`.

**Cách xử lý sai (cần tránh):** thêm `'verify' => false` vào HTTP client. Cách
này tắt hoàn toàn việc xác thực chứng chỉ, biến kết nối thành mục tiêu cho tấn
công xen giữa (man-in-the-middle). Đây là kết quả thường gặp khi tra cứu lỗi
trên mạng nhưng không được dùng.

---

## 4. Phân trang

### Sai framework CSS

Laravel mặc định dựng thanh phân trang bằng lớp CSS của **Tailwind**, trong khi
toàn bộ giao diện dự án chạy **Bootstrap 5**. Hệ quả: các trang gọi `links()`
không kèm tham số sẽ xuất ra `class="w-5 h-5"`, `relative inline-flex` — không có
CSS nào định nghĩa, thanh phân trang xổ dọc và icon phóng to bất thường.

Khắc phục tại `AppServiceProvider::boot()`:

```php
Paginator::useBootstrapFive();
```

### Thứ tự không ổn định giữa các trang

Bộ dữ liệu demo tạo hàng loạt tài khoản trong cùng một giây — có tới **41 bản ghi
trùng nhau `created_at`**. Khi chỉ sắp xếp bằng `latest()`, thứ tự giữa các bản
ghi trùng là **không xác định**: cơ sở dữ liệu trả về khác nhau giữa các lần truy
vấn, khiến cùng một bản ghi lúc ở trang 3 lúc nhảy sang trang 4, có bản ghi bị
lặp và có bản ghi biến mất khi lật trang.

Khắc phục: thêm tiêu chí phụ `orderByDesc('id')` để chốt thứ tự.

> Đây là lỗi kín và rất khó phát hiện bằng mắt — chỉ lộ ra khi lật trang nhiều
> lần. Hệ thống áp dụng cùng cách xử lý ở danh sách ứng viên
> (`CompanyApplicationController`) và danh sách tài khoản (`AdminUserController`).

### Đếm số lượng

Trên paginator, `count()` chỉ đếm **số bản ghi của trang hiện tại**, còn `total()`
mới đếm toàn bộ. Nhầm hai hàm này khiến tiêu đề "Tài khoản Chờ duyệt (N)" dừng ở
10 dù còn nhiều hơn. Tương tự, các số liệu thống kê ở màn hình ứng viên được tính
bằng truy vấn `GROUP BY` riêng trên toàn bộ dữ liệu, không tính trên paginator.

---

## 5. Phê duyệt tài khoản

### Admin duyệt những vai trò nào?

| Vai trò | Cần duyệt? | Lý do |
|---|---|---|
| `company`, `university` | **Có** | Tự đăng ký, cần xác minh là tổ chức thật |
| `student` | Không | Đăng ký xong dùng được ngay |
| `hiring`, `academic_affairs` | Không | Do chủ doanh nghiệp / trường tự tạo, tổ chức tự chịu trách nhiệm |
| `admin` | — | Chỉ sinh từ seeder |

Nói cách khác: quản trị viên gác cổng ở tầng **tổ chức**; tài khoản nhân viên do
tổ chức tự quản lý.

### Lỗi đã sửa: nút "Từ chối" không có tác dụng

Trước đây thao tác từ chối chỉ gán lại `is_active = false`, trong khi tài khoản
đang chờ duyệt **vốn đã** mang giá trị đó. Quản trị viên bấm xong vẫn thấy tài
khoản nằm nguyên trong hàng chờ, duyệt mãi không hết.

Nguyên nhân gốc: **không thể suy ra "đã xét và bị từ chối" từ mỗi cờ `is_active`** —
cờ này không phân biệt được "chưa xét" với "đã xét và bị loại".

Khắc phục: thêm cột `users.rejected_at`. Tài khoản bị từ chối rời khỏi hàng chờ,
nhưng bản ghi được giữ lại để truy vết và hiển thị badge "Bị từ chối" trong danh
sách tổng. Duyệt lại sẽ xóa dấu từ chối, cho phép quản trị viên đảo quyết định.

Khóa bằng 3 test trong `tests/Feature/AdminAccountRejectionTest.php`.

---

## 6. Giao diện: tên tổ chức dài làm vỡ thanh điều hướng

Tài khoản tổ chức đặt tên theo tên pháp nhân nên rất dài — dài nhất trong dữ liệu
demo là **52 ký tự**: *"Công ty Cổ phần Tư vấn Trắc địa và Bản đồ Thăng Long"*,
chiếm 428px trên thanh điều hướng.

Đo thực tế ở khung nhìn 1100px:

| | Chiều rộng tên | Navbar tràn | Trang cuộn ngang |
|---|---|---|---|
| Trước khi sửa | 428px | Có | Có |
| Sau khi sửa | 110px | Không | Không |

Khắc phục bằng lớp `.navbar-username`: cắt bớt bằng dấu ba chấm, giữ tên đầy đủ ở
thuộc tính `title` khi rê chuột. Ba mức theo bề ngang màn hình: 180px (desktop),
110px (992–1200px, dải chật nhất), 60vw (khi thanh điều hướng gập lại).

**Lưu ý kỹ thuật:** ở chế độ gập không dùng được `max-width: 100%` vì phần tử nằm
trong một flex container — trong ngữ cảnh đó `100%` không ràng buộc được gì và
tên dài vẫn tràn ra ngoài. Phải neo theo bề ngang khung nhìn (`vw`) và đặt
`min-width: 0` cho thẻ bao ngoài.

---

## 7. An toàn thông tin

### Đã xử lý

| Biện pháp | Vị trí |
|---|---|
| Lọc HTML từ trình soạn thảo trước khi lưu (chống XSS) | `CompanyJobController::purifyRichTextInput()` — bắt buộc vì nội dung này in ra bằng `{!! !!}`, người dùng có thể bỏ qua giao diện và POST thẳng mã độc |
| Ràng buộc `UNIQUE (job_id, student_id)` ở tầng CSDL | Chặn nộp trùng khi hai request chạy song song cùng vượt qua bước kiểm tra ở tầng PHP |
| Route đăng nhập giả và chạy test chỉ tồn tại ở môi trường `local` | `routes/web.php` — có test khẳng định chúng trả về 404 ngoài local |
| Kiểm tra quyền sở hữu trước mọi thao tác trên dữ liệu tổ chức | `abort(403)` trong các controller |

### Còn tồn — cần nêu trung thực nếu hội đồng hỏi

1. **`CollaborationUIController::update()` thiếu kiểm tra quyền sở hữu.** Bất kỳ
   tài khoản doanh nghiệp/trường nào cũng có thể duyệt hoặc từ chối yêu cầu hợp
   tác của tổ chức khác bằng cách đổi ID trên URL (lỗi IDOR).
2. **`JobUIController::show()` không lọc `is_approved`.** Tin tuyển dụng chưa được
   duyệt vẫn xem được nếu truy cập thẳng URL, và vẫn nộp hồ sơ được.
3. **Phân quyền rải rác.** Câu lệnh kiểm tra `abort(403)` lặp lại khoảng 15 lần
   trong các controller thay vì gom vào Policy/Gate của Laravel. Chỉ cần quên một
   dòng là thủng — chính là nguyên nhân của lỗi số 1.
4. **Gọi dịch vụ AI đồng bộ trong request.** Sinh viên bấm "Ứng tuyển" phải chờ
   trọn vòng gọi API. Hạ tầng hàng đợi (`QUEUE_CONNECTION=database`) đã sẵn sàng,
   chỉ chưa chuyển sang xử lý nền.
5. **`Application` không lưu `resume_id`.** Lúc nộp lấy CV mặc định, nhưng lúc nhà
   tuyển dụng xem lại thì hệ thống **đoán lại** CV mặc định hiện tại. Nếu sinh
   viên đổi CV mặc định sau khi nộp, nhà tuyển dụng sẽ xem nhầm CV so với bản mà
   AI đã chấm.

---

## 8. Kiểm thử

```bash
php artisan test
```

Hiện có **40 test / 97 assertion**, tất cả đạt.

| Tệp | Phạm vi |
|---|---|
| `ApplicationDecisionLogTest` | Nhật ký quyết định, thông báo mỗi lượt quyết định |
| `AdminAccountRejectionTest` | Từ chối tài khoản rời hàng chờ, đảo được quyết định |
| `ApplicationRankingAndMailTest` | Xếp hạng theo điểm AI, email kết quả |
| `ApplicationAIEvaluationTest` | Không bịa điểm khi thiếu API key |
| `RoleAccessControlTest` | Phân quyền theo vai trò |
| `HardenedRoutesTest` | Route nhạy cảm không tồn tại ngoài local, chặn nộp trùng |
| `JobRichTextTest` | Lọc mã độc trong nội dung tin tuyển dụng |
| `WorkshopManagementTest` | Tạo workshop không cần doanh nghiệp đồng hành |
| `AuthenticationTest`, `CompanyJobTest`, `StudentApplyTest` | Luồng cơ bản |

Ngoài ra hệ thống có trang **`/testing-report`** cho phép chạy toàn bộ bộ test
ngay trên trình duyệt — tiện để trình diễn trực tiếp trước hội đồng. Trang này
chỉ tồn tại ở môi trường `local`.

---

## 9. Truy vấn SQL để trình diễn

Kết nối cơ sở dữ liệu (chú ý **cổng 3307**, không phải 3306):

```
Host: 127.0.0.1    Port: 3307
User: root         Password: secret
Database: autocareerbridge
```

Truy vấn từ dòng lệnh — bắt buộc có `--default-character-set=utf8mb4`, nếu thiếu
thì tiếng Việt hiển thị thành dấu hỏi:

```bash
docker exec -it acb_mysql mysql --default-character-set=utf8mb4 -uroot -psecret autocareerbridge
```

### Xếp hạng ứng viên theo điểm AI

Đây chính là câu lệnh mà `CompanyApplicationController` sinh ra:

```sql
SELECT u.name AS sinh_vien, j.title AS vi_tri, c.company_name AS cong_ty,
       a.ai_score, a.status
FROM applications a
JOIN students s  ON s.id = a.student_id
JOIN users u     ON u.id = s.user_id
JOIN jobs j      ON j.id = a.job_id
JOIN companies c ON c.id = j.company_id
ORDER BY a.ai_score IS NULL, a.ai_score DESC
LIMIT 10;
```

### Nhật ký quyết định của một hồ sơ

```sql
SELECT decided_by_name, decided_by_role, previous_status, status, created_at
FROM application_decisions
WHERE application_id = 60
ORDER BY created_at DESC;
```

### Chứng minh ràng buộc chống nộp trùng

```sql
SHOW CREATE TABLE applications;
```

Kết quả có dòng `UNIQUE KEY applications_job_student_unique (job_id, student_id)`.

---

## 10. Vì sao không cho sinh viên chấm thử CV nhiều lần?

**Đây là câu hỏi hội đồng nhiều khả năng sẽ vặn.** Nội dung dưới đây là lập luận
bảo vệ, kèm phần thừa nhận hạn chế một cách trung thực.

### Luồng hiện tại

Sinh viên nộp CV → AI chấm ngay → điểm hiển thị cho nhà tuyển dụng để xếp hạng.
Sinh viên **cũng xem được điểm của mình**, nhưng chỉ **sau khi đã nộp**
(`resources/views/jobs/show.blade.php:33`), tức là không sửa lại được nữa. Ràng
buộc `UNIQUE (job_id, student_id)` bảo đảm mỗi vị trí chỉ nộp được một lần.

### Đề xuất thường gặp

*"Sao không cho sinh viên chấm thử trước, thấy điểm thấp thì sửa CV rồi chấm lại
đến khi ưng thì mới nộp?"*

Nghe hợp lý, và **có giá trị thật** về mặt hướng nghiệp: sinh viên biết mình
thiếu gì mà bổ sung. Nhưng nó phá hỏng chính tính năng lõi của hệ thống.

### Lý do từ chối — theo thứ tự sức nặng

**1. Phá hỏng tính toàn vẹn của phép đo (lý do chính).**

Nếu sinh viên chấm lại được không giới hạn, họ sẽ sửa CV để **chiều bộ chấm**
chứ không phải để phản ánh đúng năng lực: nhồi từ khóa lấy từ mô tả công việc,
thử đi thử lại đến khi điểm lên. Khi đó điểm số không còn đo *"mức độ phù hợp"*
mà đo *"ai kiên nhẫn dò tìm nhiều lần nhất"*.

Mà điểm AI chính là thứ nhà tuyển dụng dùng để xếp hạng ứng viên
(`ORDER BY ai_score IS NULL, ai_score DESC`). Làm hỏng độ tin cậy của nó là làm
hỏng toàn bộ giá trị của hệ thống.

**2. Tạo bất công giữa các ứng viên.**

Sinh viên hiểu cơ chế và chịu khó dò sẽ luôn có điểm cao hơn sinh viên khai báo
trung thực, dù năng lực thực tế có thể ngược lại.

**3. Khuyến khích khai không đúng sự thật.**

Hệ thống vô tình dạy sinh viên rằng cách để được chọn là viết CV chiều máy — trái
với mục tiêu hướng nghiệp của một nền tảng do nhà trường vận hành.

**4. Chi phí gọi dịch vụ AI (lý do phụ, không phải lý do chính).**

Mỗi lần chấm gửi đi toàn bộ nội dung CV (giới hạn 15.000 ký tự —
`StudentResumeController.php:76`) cộng mô tả công việc, ước tính khoảng 5.000–7.000
token đầu vào. Với mô hình `gpt-3.5-turbo`, chi phí mỗi lượt chấm ở mức **dưới một
xu Mỹ** — nghĩa là với quy mô một đồ án, tiền bạc **không phải** rào cản thật sự.

> **Lưu ý khi trả lời hội đồng:** đừng lấy chi phí làm lý do chính. Nếu chỉ nói
> "tốn token", hội đồng hoàn toàn có thể đáp *"vài xu thì có đáng gì"* và lập luận
> sụp đổ. Lý do đứng vững là **tính toàn vẹn của phép đo** (mục 1).

### Hạn chế phải thừa nhận

Thiết kế hiện tại có cái giá của nó: **sinh viên không học được gì từ điểm số**,
vì lúc nhìn thấy thì đã nộp xong. Đây là đánh đổi có ý thức giữa *giá trị sàng
lọc cho doanh nghiệp* và *giá trị hướng nghiệp cho sinh viên*, và hệ thống hiện
ưu tiên vế thứ nhất.

Bù lại, giao diện nói rõ điều đó **trước khi** sinh viên bấm nộp: cảnh báo "Mỗi vị
trí chỉ nộp được một lần", nút tắt sang trang kiểm tra CV mặc định, và một hộp
thoại xác nhận. Xem `resources/views/jobs/show.blade.php`.

### Hướng nâng cấp nếu hội đồng yêu cầu

Có thể dung hòa mà vẫn giữ được độ tin cậy của điểm số:

| Biện pháp | Tác dụng |
|---|---|
| Tách **"chấm thử"** khỏi **"chấm chính thức"** | Điểm gửi cho nhà tuyển dụng vẫn chỉ chấm một lần lúc nộp |
| Giới hạn số lượt chấm thử (ví dụ 3 lần/ngày) | Chặn việc dò tìm bằng cách thử hàng loạt |
| Chấm thử chỉ trả **dải điểm** (Cao / Khá / Thấp) kèm gợi ý định tính, **không trả số chính xác** | Sinh viên vẫn biết mình yếu chỗ nào, nhưng không có tín hiệu đủ mịn để dò từng bước |
| Lưu đệm theo `hash(nội dung CV + mã tin tuyển dụng)` | Xem lại kết quả cũ không tốn thêm lượt gọi; chỉ CV thay đổi mới chấm lại |

Ba biện pháp đầu giữ nguyên giá trị sàng lọc; biện pháp thứ tư giải quyết trọn vẹn
phần chi phí. Hiện dự án **chưa cài** cơ chế nào trong số này — đây là hướng phát
triển tiếp, không phải chức năng đã có.
