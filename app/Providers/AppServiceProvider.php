<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Mặc định Laravel dựng thanh phân trang bằng lớp CSS của Tailwind, trong
        // khi toàn bộ giao diện dự án chạy Bootstrap 5. Không khai báo dòng này
        // thì các lớp như `w-5 h-5`, `relative inline-flex` không có CSS nào định
        // nghĩa, khiến thanh phân trang xổ dọc và icon SVG phóng to bất thường.
        Paginator::useBootstrapFive();
    }
}
