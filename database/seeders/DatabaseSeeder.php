<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Bộ dữ liệu trình diễn: trường đại học và doanh nghiệp khu vực Hà Nội,
        // sinh viên, tin tuyển dụng, hồ sơ ứng tuyển kèm điểm đánh giá AI.
        // Toàn bộ là dữ liệu hư cấu, xem chú thích đầu tệp DemoDataSeeder.
        $this->call(DemoDataSeeder::class);
    }
}
