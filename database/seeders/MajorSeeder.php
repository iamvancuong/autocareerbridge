<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Field;
use App\Models\Major;

class MajorSeeder extends Seeder
{
    public function run()
    {
        $fields = [
            'Công nghệ Thông tin' => [
                'Khoa học Máy tính',
                'Kỹ thuật Phần mềm',
                'An toàn Thông tin',
                'Hệ thống Thông tin',
                'Trí tuệ Nhân tạo',
            ],
            'Kinh tế & Quản trị' => [
                'Quản trị Kinh doanh',
                'Kế toán',
                'Tài chính Ngân hàng',
                'Marketing',
                'Logistics và Quản lý chuỗi cung ứng',
            ],
            'Kỹ thuật & Công nghệ' => [
                'Kỹ thuật Cơ điện tử',
                'Kỹ thuật Điện - Điện tử',
                'Kỹ thuật Ô tô',
            ],
            'Ngoại ngữ' => [
                'Ngôn ngữ Anh',
                'Ngôn ngữ Nhật',
                'Ngôn ngữ Trung',
            ]
        ];

        foreach ($fields as $fieldName => $majors) {
            $field = Field::firstOrCreate(['name' => $fieldName]);

            foreach ($majors as $majorName) {
                Major::firstOrCreate([
                    'name' => $majorName,
                    'field_id' => $field->id
                ]);
            }
        }
    }
}
