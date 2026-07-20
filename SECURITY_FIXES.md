# Security Fixes — Auto Career Bridge

Commit: `819c38e`

---

## Issue 1: Admin Area Not Protected by Role

**Problem**: Routes in `/admin/*` group chỉ có middleware `auth`, không kiểm tra role. Bất kỳ user đã đăng nhập (kể cả student) đều có thể truy cập `/admin`, duyệt job, duyệt tài khoản, xoá field/major.

**Fix**:
1. Tạo middleware `app/Http/Middleware/EnsureUserHasRole.php`:
   ```php
   $middleware->alias([
       'role' => \App\Http\Middleware\EnsureUserHasRole::class,
   ]);
   ```

2. Áp dụng trên routes/web.php:
   - `Route::prefix('admin')->middleware('role:admin')` — chỉ admin
   - `Route::prefix('company')->middleware('role:company,hiring')` — company + HR (hired staff)
   - `Route::prefix('university')->middleware('role:university,academic_affairs')` — trường + giáo vụ
   - `Route::prefix('student')->middleware('role:student')` — sinh viên

**Kết quả**: Một user không thuộc role yêu cầu sẽ nhận 403 Forbidden, không được vào khu vực đó.

---

## Issue 2: Mock Login Route Publicly Accessible

**Problem**: `GET /mock-login/{role}` là route public, đăng nhập thẳng vào user đầu tiên có role đó mà không cần mật khẩu. Vào `/mock-login/admin` là chiếm quyền admin.

**Fix**:
1. Điều kiện route chỉ đăng ký trên local:
   ```php
   if (app()->environment('local')) {
       Route::get('/mock-login/{role}', [AuthController::class, 'mockLogin'])->name('mock.login');
       Route::get('/mock-logout', [AuthController::class, 'mockLogout'])->name('mock.logout');
   }
   ```

2. Secondary check trong controller:
   ```php
   public function mockLogin($role)
   {
       abort_unless(app()->environment('local'), 404);
       // ...
   }
   ```

3. Bọc tất cả link tới `mock-login` trong views dùng `@env('local')`:
   - `resources/views/auth/login.blade.php`
   - `resources/views/layouts/app.blade.php`
   - `resources/views/jobs/show.blade.php` (đổi thành `/login` thay vì mock-login)

**Kết quả**: 
- **Local (dev)**: Nút mock-login hiển thị, cho phép login nhanh = dev experience
- **Staging/Production (APP_ENV=staging|production)**: Route không tồn tại (404), không có nút, không thể bypass auth

---

## Issue 3: ApplicationService Uses `env()` Instead of `config()`

**Problem**: 
1. Code dùng `env('OPENAI_API_KEY')` trực tiếp → sau `php artisan config:cache`, `env()` trả về null vì cache không gọi .env lần thứ 2.
2. Khi API key thiếu, fallback tới điểm **ngẫu nhiên** `rand(65,95)` → HR không biết điểm này "giả lập", có thể tin vào số ngẫu nhiên để quyết định tuyển dụng.

**Fix**:
1. Thêm config vào `config/services.php`:
   ```php
   'openai' => [
       'key' => env('OPENAI_API_KEY'),
       'base' => env('OPENAI_API_BASE', 'https://api.openai.com/v1'),
       'model' => env('OPENAI_MODEL', 'gpt-3.5-turbo'),
   ],
   ```

2. Đổi `env()` thành `config()` trong ApplicationService:
   ```php
   $apiKey = config('services.openai.key');
   $apiBase = config('services.openai.base');
   $model = config('services.openai.model');
   ```

3. Bỏ random fallback, thay bằng `markEvaluationUnavailable()`:
   - `ai_score` → null (rõ ràng "chưa có đánh giá")
   - `ai_review` → "Chưa có đánh giá tự động. Lý do: [cách thức lỗi]. Vui lòng xem xét hủ công."

**Kết quả**:
- Cấu hình sẽ hoạt động ngay cả sau `config:cache`
- HR nhìn thấy ai_score=null rõ ràng là "chưa chấm", không phải một con số trông hợp lệ
- Logs ghi chi tiết lý do tại sao chấm điểm thất bại

---

## Bonus: Missing Columns Fixed

Migration `2026_07_20_000000_add_missing_columns_to_collaborations_workshops_notifications.php` thêm:
- `collaborations.initiated_by` (string, nullable) — để biết ai khởi tạo hợp tác (company hay university)
- `workshops.date` (date, nullable) — ngày tổ chức workshop
- `notifications.type` (string, nullable) — loại thông báo
- `notifications.url` (string, nullable) — URL liên kết (nếu có)

Code đã dùng các cột này nhưng chúng không tồn tại → trang chủ `/` sẽ crash. Bây giờ all columns tồn tại.

---

## Test Coverage

### `tests/Feature/RoleAccessControlTest.php` — 9 tests
- ✅ Guest redirect từ /admin
- ✅ Student không vào /admin
- ✅ Student không approve user
- ✅ Company không vào /admin
- ✅ Admin vào được /admin
- ✅ Student không vào /company/jobs
- ✅ Company không vào /university/students
- ✅ Hiring role vào được /company/jobs (secondary role)
- ✅ Mock-login route không tồn tại (non-local)

### `tests/Feature/ApplicationAIEvaluationTest.php` — 2 tests
- ✅ ai_score=null khi key thiếu (không phải số giả)
- ✅ ai_review giải thích rõ lý do (không có từ "ngẫu nhiên")

### `tests/Feature/CompanyJobTest.php` — Updated
- ✅ Kỳ vọng 403 Forbidden thay vì 302 redirect

**Tất cả 21 test pass** ✅

---

## How to Deploy

1. **Cập nhật code**: Pull commit này
2. **Migrate**: `php artisan migrate --force`
3. **Clear cache**: `php artisan config:clear cache:clear route:clear view:clear`
4. **Test**: `php artisan test`
5. **Production tip**: Set `.env` với `APP_ENV=production` để mock-login bị vô hiệu

---

## Breaking Changes

- Nếu bạn viết test mà mong chờ `assertStatus(302)` khi user truy cập khu vực sai role, cần đổi thành `assertForbidden()` hay `assertStatus(403)`
- Nếu code gọi `route('mock.login')` ngoài `@env('local')` sẽ crash — xem lại templates
