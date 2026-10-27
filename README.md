# Auto Career Bridge

Nền tảng kết nối Nhà trường – Sinh viên – Doanh nghiệp trong tuyển dụng và thực tập.
Xây dựng bằng Laravel 12 (Blade + Bootstrap 5), MySQL 8, có tính năng chấm điểm
độ phù hợp CV ↔ tin tuyển dụng bằng AI.

## Cài đặt môi trường phát triển

Cần **Docker Desktop** (chạy MySQL và hộp thư ảo Mailpit) và **PHP 8.2+**.

```bash
docker compose up -d          # MySQL cổng 3307, Mailpit cổng 1025/8025
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link      # để xem được CV và ảnh đại diện đã tải lên
php artisan serve
```

| Dịch vụ | Địa chỉ |
|---|---|
| Ứng dụng | http://localhost:8000 |
| Hộp thư Mailpit (xem email hệ thống gửi ra) | http://localhost:8025 |

## Tài khoản demo

Lệnh `--seed` nạp bộ dữ liệu trình diễn: 5 trường đại học và 8 doanh nghiệp khu vực
Hà Nội, 42 sinh viên, 23 tin tuyển dụng và hơn 150 hồ sơ ứng tuyển đã được chấm điểm.
**Mật khẩu của mọi tài khoản là `password`.**

| Vai trò | Email | Ghi chú |
|---|---|---|
| Quản trị viên | `admin@demo.local` | Duyệt tin tuyển dụng và tài khoản tổ chức |
| Doanh nghiệp | `hr.fpt@demo.local` | Còn có: `hr.viettel@`, `hr.misa@`, `hr.cmc@`, `hr.rikkei@`, `hr.nashtech@`, `hr.savvycom@`, `hr.thanglong@` |
| Nhân viên tuyển dụng | `ts.fpt@demo.local` | Vai trò phụ `hiring`, dùng chung hồ sơ công ty |
| Trường đại học | `humg@demo.local` | Còn có: `hust@`, `ptit@`, `uet@`, `tlu@` |
| Giáo vụ | `vp.humg@demo.local` | Vai trò phụ `academic_affairs` |
| Sinh viên | `an.nv01@demo.local` | Email sinh theo mẫu `<tên>.<viết tắt><số thứ tự>@demo.local` |

> ⚠️ Dữ liệu demo là **hư cấu hoàn toàn**. Tên trường và doanh nghiệp là tổ chức có
> thật nhằm giúp màn hình demo gần thực tế, nhưng mọi tài khoản, nhân sự, tin tuyển
> dụng và quan hệ hợp tác đều do seeder tự sinh, không phản ánh hoạt động thật của
> các tổ chức đó.

Muốn bật chấm điểm CV bằng AI thì điền `OPENAI_API_KEY` trong `.env`. Để trống
thì hệ thống ghi `ai_score = null` kèm lý do chứ **không bịa ra điểm số**.

## Kiểm thử

```bash
php artisan test
```

---

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
