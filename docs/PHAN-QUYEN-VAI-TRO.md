# Phân quyền & vai trò người dùng — Auto Career Bridge

Tài liệu tra cứu nhanh cho buổi bảo vệ. Mỗi khẳng định đều kèm **vị trí chính xác
trong mã nguồn** (`đường/dẫn/tệp.php:dòng`) để mở đúng chỗ khi hội đồng hỏi.

> Xem thêm: [Báo cáo kỹ thuật](BAO-CAO-KY-THUAT.md) — các quyết định thiết kế khác.

---

## 1. Sáu vai trò trong hệ thống

Danh sách vai trò được khai báo cứng ở tầng cơ sở dữ liệu:

```php
$table->enum('role', ['admin', 'company', 'university', 'student', 'hiring', 'academic_affairs']);
```
📍 `database/migrations/0001_01_01_000000_create_users_table.php:14`

| Vai trò | Tên tiếng Việt | Ai tạo ra | Cần Admin duyệt? | Bảng hồ sơ đi kèm |
|---|---|---|---|---|
| `admin` | Quản trị viên | Seeder | — | (không) |
| `company` | Chủ doanh nghiệp | Tự đăng ký | ✅ **Có** | `companies` |
| `hiring` | Nhân viên tuyển dụng | Do `company` tạo | ❌ Không | `hirings` |
| `university` | Chủ trường đại học | Tự đăng ký | ✅ **Có** | `universities` |
| `academic_affairs` | Giáo vụ | Do `university` tạo | ❌ Không | `academic_affairs` |
| `student` | Sinh viên | Tự đăng ký | ❌ Không | `students` |

### Vì sao chỉ `company` và `university` cần duyệt?

```php
$isActive = $request->role === 'student';
```
📍 `app/Http/Controllers/AuthController.php:60`

Chỉ ba vai trò được phép tự đăng ký (`company`, `university`, `student` —
📍 `AuthController.php:57`). Trong đó sinh viên dùng được ngay, còn hai vai trò
tổ chức phải chờ xác minh là pháp nhân có thật.

Hai vai trò phụ (`hiring`, `academic_affairs`) **không đi qua form đăng ký** mà do
chính tổ chức tạo, nên được `is_active = true` ngay từ đầu — tổ chức tự chịu
trách nhiệm về nhân sự của mình.

**Câu trả lời gọn cho hội đồng:** *Quản trị viên gác cổng ở tầng **tổ chức**;
tài khoản nhân viên do tổ chức tự quản lý.*

Hàng chờ duyệt lọc đúng hai vai trò đó:
```php
User::where('is_active', false)->whereNull('rejected_at')
    ->whereIn('role', ['company', 'university'])
```
📍 `app/Http/Controllers/Admin/AdminUserController.php:19`

---

## 2. Mô hình "vai trò chính – vai trò phụ"

Đây là điểm thiết kế đáng nói nhất về phân quyền. Ba nhóm người dùng, mỗi nhóm
có một **vai trò chính** (sở hữu hồ sơ tổ chức) và có thể có **vai trò phụ**
(dùng chung hồ sơ đó).

```
Doanh nghiệp                          Trường đại học
─────────────                         ──────────────
company  ──sở hữu──> companies        university ──sở hữu──> universities
   │                     ▲                │                       ▲
   │ tạo tài khoản       │ dùng chung     │ tạo tài khoản         │ dùng chung
   ▼                     │                ▼                       │
hiring ──────────────────┘         academic_affairs ──────────────┘
```

### Cơ chế kỹ thuật: hai accessor

Vai trò phụ không có hồ sơ tổ chức riêng, mà truy ngược qua bảng trung gian:

```php
public function getActiveCompanyAttribute() {
    return $this->role === 'company' ? $this->company
         : ($this->role === 'hiring' ? $this->hiring->company : null);
}
```
📍 `app/Models/User.php:37` (và `getActiveUniversityAttribute()` tại dòng 41)

Nhờ vậy mọi controller chỉ cần gọi `auth()->user()->active_company` là lấy đúng
doanh nghiệp, **không cần biết** người đang đăng nhập là chủ hay nhân viên.

Hai hàm hỗ trợ kiểm tra nhóm:
```php
public function hasCompanyRole()    { return in_array($this->role, ['company', 'hiring']); }
public function hasUniversityRole() { return in_array($this->role, ['university', 'academic_affairs']); }
```
📍 `app/Models/User.php:45` và `:49`

---

## 3. Vai trò chính khác vai trò phụ ở điểm nào?

Câu hỏi hội đồng rất hay hỏi. Trả lời: **đúng một khác biệt duy nhất** cho mỗi cặp.

| | `company` (chủ) | `hiring` (nhân viên) |
|---|---|---|
| Đăng / sửa / xóa tin tuyển dụng | ✅ | ✅ |
| Duyệt ứng viên, xem CV, ra quyết định | ✅ | ✅ |
| Gửi & duyệt yêu cầu hợp tác với trường | ✅ | ✅ |
| Xem báo cáo thống kê | ✅ | ✅ |
| **Quản lý tài khoản Nhân sự HR** | ✅ | ❌ **Bị chặn** |

📍 `app/Http/Controllers/Company/HiringController.php:14, 23, 52`
```php
if (auth()->check() && auth()->user()->role !== 'company')
    abort(403, 'Chỉ tài khoản chủ Doanh nghiệp mới có quyền quản lý Nhân sự.');
```

| | `university` (chủ) | `academic_affairs` (giáo vụ) |
|---|---|---|
| Quản lý danh sách sinh viên | ✅ | ✅ |
| Tạo & quản lý Workshop | ✅ | ✅ |
| Chọn ngành đào tạo của trường | ✅ | ✅ |
| Gửi & duyệt yêu cầu hợp tác với doanh nghiệp | ✅ | ✅ |
| Xem báo cáo thống kê | ✅ | ✅ |
| **Quản lý tài khoản Giáo vụ** | ✅ | ❌ **Bị chặn** |

📍 `app/Http/Controllers/University/AcademicAffairController.php:14, 23, 52`

**Lý do thiết kế:** nếu vai trò phụ tự tạo được vai trò phụ khác, một nhân viên
có thể tự nhân bản quyền truy cập vô hạn mà chủ tổ chức không hay biết. Quyền tạo
tài khoản phải dừng lại ở người sở hữu tổ chức.

---

## 4. Ma trận quyền truy cập theo khu vực

| Khu vực (URL) | admin | company | hiring | university | academic_affairs | student | khách |
|---|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| `/` `/jobs` `/companies` `/universities` `/workshops` | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| `/dashboard` `/profile` `/notifications` | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ |
| `/jobs/{id}/apply` (nộp hồ sơ) | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ |
| `/student/*` (quản lý CV) | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ |
| `/company/*` | ❌ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| `/company/hirings` (Nhân sự HR) | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ |
| `/university/*` | ❌ | ❌ | ❌ | ✅ | ✅ | ❌ | ❌ |
| `/university/academic_affairs` (Giáo vụ) | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ |
| `/collaborations` | ❌ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ |
| `/admin/*` | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |

Khai báo nhóm route tương ứng — 📍 `routes/web.php`:

| Dòng | Khai báo |
|---|---|
| `:125` | `Route::prefix('student')->middleware('role:student')` |
| `:131` | `Route::prefix('company')->middleware('role:company,hiring')` |
| `:139` | `Route::prefix('university')->middleware('role:university,academic_affairs')` |
| `:149` | `Route::prefix('admin')->middleware('role:admin')` |

Lưu ý cách đọc: middleware cho **cả cặp** vai trò vào chung khu vực; hạn chế
riêng của vai trò phụ được siết thêm **bên trong controller** (mục 3).

---

## 5. Ba tầng bảo vệ

Khi hội đồng hỏi *"phân quyền của em nằm ở đâu?"*, trả lời theo ba tầng:

### Tầng 1 — Xác thực
Middleware `auth` bọc toàn bộ nhóm route cần đăng nhập.
📍 `routes/web.php:105`

### Tầng 2 — Vai trò
Middleware tự viết `EnsureUserHasRole`, dùng dạng `role:admin` hoặc `role:company,hiring`:

```php
if (! $user || ! in_array($user->role, $roles, true)) {
    abort(403, 'Bạn không có quyền truy cập khu vực này.');
}
```
📍 `app/Http/Middleware/EnsureUserHasRole.php:19`
Đăng ký bí danh `role` tại 📍 `bootstrap/app.php:15`

### Tầng 3 — Quyền sở hữu dữ liệu
Vào được khu vực chưa đủ — còn phải đúng dữ liệu của tổ chức mình:

```php
if ($job->company_id !== auth()->user()->active_company->id) abort(403);
```
📍 `app/Http/Controllers/Company/CompanyJobController.php` (và các controller khác)

> **Điểm yếu cần thừa nhận nếu bị hỏi sâu:** kiểm tra ở tầng 3 hiện được viết tay
> và lặp lại khoảng 15 lần rải rác trong các controller, thay vì gom vào
> Policy/Gate của Laravel. Chỉ cần quên một chỗ là thủng — và thực tế
> `CollaborationUIController::update()` đang thiếu kiểm tra này (lỗi IDOR, xem
> mục 7 của [Báo cáo kỹ thuật](BAO-CAO-KY-THUAT.md)).

---

## 6. Kiểm chứng bằng test tự động

📍 `tests/Feature/RoleAccessControlTest.php` — 9 test phủ ma trận ở mục 4:

| Test | Khẳng định |
|---|---|
| `guest_is_redirected_away_from_admin_area` | Khách không vào được khu quản trị |
| `student_cannot_access_admin_dashboard` | Sinh viên không vào được `/admin` |
| `student_cannot_approve_users` | Sinh viên không gọi được hành động duyệt tài khoản |
| `company_cannot_access_admin_area` | Doanh nghiệp không vào được `/admin` |
| `admin_can_access_admin_dashboard` | Quản trị viên vào được |
| `student_cannot_access_company_area` | Sinh viên không vào được `/company` |
| `company_cannot_access_university_area` | Doanh nghiệp không vào được `/university` |
| `hiring_role_can_still_access_company_area` | **Vai trò phụ vẫn vào được khu vực của tổ chức** |
| `mock_login_route_does_not_exist_outside_local` | Route đăng nhập giả không tồn tại ngoài môi trường local |

Chạy riêng nhóm này khi demo:

```bash
php artisan test --filter=RoleAccessControlTest
```

---

## 7. Tài khoản demo để trình diễn từng vai trò

Mật khẩu của **mọi** tài khoản demo: `password`

| Vai trò | Email đăng nhập | Dùng để trình diễn |
|---|---|---|
| `admin` | `admin@demo.local` | Duyệt tin tuyển dụng, duyệt/từ chối tài khoản tổ chức |
| `company` | `hr.fpt@demo.local` | Đăng tin, duyệt ứng viên, **thấy menu "Nhân sự HR"** |
| `hiring` | `ts.fpt@demo.local` | Cùng công ty FPT, **không thấy menu "Nhân sự HR"** |
| `university` | `humg@demo.local` | Quản lý sinh viên, workshop, **thấy menu "Tài khoản Giáo vụ"** |
| `academic_affairs` | `vp.humg@demo.local` | Cùng trường, **không thấy menu "Tài khoản Giáo vụ"** |
| `student` | `an.nv01@demo.local` | Tải CV, ứng tuyển, xem điểm AI |

### Kịch bản demo phân quyền trong 60 giây

1. Đăng nhập `hr.fpt@demo.local` → thanh điều hướng **có** mục "Nhân sự HR".
2. Đăng xuất, đăng nhập `ts.fpt@demo.local` (cùng công ty) → mục đó **biến mất**.
3. Gõ thẳng URL `/company/hirings` → nhận **403** kèm thông báo tiếng Việt rõ ràng.
4. Vào `/company/jobs` → vẫn vào được bình thường, **đúng dữ liệu công ty FPT**.

Bước 3 và 4 chứng minh hai điều: chặn đúng chỗ cần chặn, và vai trò phụ vẫn dùng
chung được hồ sơ tổ chức qua accessor `active_company`.

---

## 8. Tra nguồn nhanh khi bị hỏi

| Hội đồng hỏi | Mở tệp | Dòng |
|---|---|---|
| "Hệ thống có mấy vai trò?" | `database/migrations/0001_01_01_000000_create_users_table.php` | 14 |
| "Ai cần được duyệt tài khoản?" | `app/Http/Controllers/AuthController.php` | 60 |
| "Danh sách chờ duyệt lọc thế nào?" | `app/Http/Controllers/Admin/AdminUserController.php` | 19 |
| "Nhân viên HR dùng chung dữ liệu công ty kiểu gì?" | `app/Models/User.php` | 37 |
| "Phân quyền cài ở đâu?" | `app/Http/Middleware/EnsureUserHasRole.php` | 19 |
| "Middleware đăng ký ở đâu?" | `bootstrap/app.php` | 15 |
| "Khu vực nào chặn vai trò nào?" | `routes/web.php` | 125, 131, 139, 149 |
| "Vai trò phụ bị hạn chế gì?" | `app/Http/Controllers/Company/HiringController.php` | 14 |
| | `app/Http/Controllers/University/AcademicAffairController.php` | 14 |
| "Có kiểm thử phân quyền không?" | `tests/Feature/RoleAccessControlTest.php` | toàn bộ |
