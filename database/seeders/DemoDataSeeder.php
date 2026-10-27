<?php

namespace Database\Seeders;

use App\Models\AcademicAffair;
use App\Models\Application;
use App\Models\Collaboration;
use App\Models\Company;
use App\Models\Hiring;
use App\Models\Job;
use App\Models\Major;
use App\Models\Notification;
use App\Models\Resume;
use App\Models\Skill;
use App\Models\Student;
use App\Models\University;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Dữ liệu trình diễn cho hệ thống Auto Career Bridge.
 *
 * ⚠️ TOÀN BỘ DỮ LIỆU DƯỚI ĐÂY LÀ HƯ CẤU, chỉ dùng để chạy demo trên máy cá nhân.
 * Tên trường đại học và doanh nghiệp là các tổ chức có thật tại Hà Nội nhằm giúp
 * màn hình demo trông gần thực tế, nhưng mọi tài khoản, nhân sự, tin tuyển dụng,
 * quan hệ hợp tác và hồ sơ ứng tuyển đều do seeder tự sinh — không phản ánh hoạt
 * động, chính sách hay lời mời tuyển dụng thật của các tổ chức đó. Địa chỉ email
 * đều thuộc miền demo.local nên không thể trùng với hộp thư thật.
 *
 * Chạy: php artisan migrate:fresh --seed
 */
class DemoDataSeeder extends Seeder
{
    /** Băm mật khẩu một lần rồi dùng lại — bcrypt cost 12 rất chậm nếu lặp 60 lần. */
    private string $pw;

    private array $majors = [];

    public function run(): void
    {
        $this->pw = Hash::make('password');

        $this->command->info('  → Danh mục ngành nghề & kỹ năng');
        $this->call(MajorSeeder::class);
        $this->majors = Major::pluck('id', 'name')->all();
        $this->seedSkills();

        $this->command->info('  → Tài khoản quản trị');
        $this->seedAdmin();

        $this->command->info('  → Trường đại học & giáo vụ');
        $universities = $this->seedUniversities();

        $this->command->info('  → Doanh nghiệp & nhân sự tuyển dụng');
        $companies = $this->seedCompanies();

        $this->command->info('  → Quan hệ hợp tác');
        $this->seedCollaborations($companies, $universities);

        $this->command->info('  → Workshop hướng nghiệp');
        $this->seedWorkshops($companies, $universities);

        $this->command->info('  → Sinh viên, CV và kỹ năng');
        $students = $this->seedStudents($universities);

        $this->command->info('  → Tin tuyển dụng');
        $jobs = $this->seedJobs($companies);

        $this->command->info('  → Hồ sơ ứng tuyển & đánh giá AI');
        $this->seedApplications($students, $jobs);

        $this->command->info('  → Thông báo');
        $this->seedNotifications();

        $this->summary();
    }

    // ------------------------------------------------------------------ dùng chung

    private function user(string $name, string $email, string $role, bool $active = true): User
    {
        return User::create([
            'name'      => $name,
            'email'     => $email,
            'password'  => $this->pw,
            'role'      => $role,
            'is_active' => $active,
        ]);
    }

    private function majorId(string $name): int
    {
        return $this->majors[$name] ?? array_values($this->majors)[0];
    }

    // ------------------------------------------------------------------ kỹ năng

    private function seedSkills(): void
    {
        $skills = [
            'PHP', 'Laravel', 'JavaScript', 'ReactJS', 'Vue.js', 'Node.js', 'Java',
            'Spring Boot', 'Python', 'Django', 'C#', '.NET', 'MySQL', 'PostgreSQL',
            'MongoDB', 'Redis', 'Docker', 'Git', 'Linux', 'AWS', 'REST API',
            'HTML/CSS', 'Bootstrap', 'TailwindCSS', 'Figma', 'Kiểm thử phần mềm',
            'Phân tích nghiệp vụ', 'Tiếng Anh giao tiếp', 'Tiếng Nhật N3',
            'AutoCAD', 'ArcGIS', 'MicroStation', 'Trắc địa công trình',
        ];

        foreach ($skills as $s) {
            Skill::firstOrCreate(['name' => $s]);
        }
    }

    // ------------------------------------------------------------------ quản trị

    private function seedAdmin(): void
    {
        $this->user('Quản trị hệ thống', 'admin@demo.local', 'admin');
    }

    // ------------------------------------------------------------------ trường

    private function seedUniversities(): array
    {
        $data = [
            [
                'key'   => 'humg',
                'name'  => 'Trường Đại học Mỏ - Địa chất',
                'email' => 'humg@demo.local',
                'web'   => 'https://humg.edu.vn',
                'addr'  => 'Số 18 Phố Viên, phường Đức Thắng, quận Bắc Từ Liêm, Hà Nội',
                'desc'  => 'Trường đại học kỹ thuật đa ngành, đào tạo mạnh về Công nghệ thông tin, '
                         . 'Mỏ, Địa chất, Trắc địa - Bản đồ và Dầu khí. Trung tâm Quan hệ doanh nghiệp '
                         . 'của trường thường xuyên tổ chức ngày hội việc làm cho sinh viên năm cuối.',
                'affair' => ['Nguyễn Thị Hồng Vân', 'vp.humg@demo.local', 'Phòng Công tác sinh viên'],
            ],
            [
                'key'   => 'hust',
                'name'  => 'Đại học Bách khoa Hà Nội',
                'email' => 'hust@demo.local',
                'web'   => 'https://hust.edu.vn',
                'addr'  => 'Số 1 Đại Cồ Việt, phường Bách Khoa, quận Hai Bà Trưng, Hà Nội',
                'desc'  => 'Cơ sở đào tạo kỹ thuật hàng đầu Việt Nam. Trường Công nghệ Thông tin và '
                         . 'Truyền thông cung cấp nguồn nhân lực chất lượng cao cho ngành phần mềm.',
                'affair' => ['Trần Quang Minh', 'vp.hust@demo.local', 'Trung tâm Quan hệ doanh nghiệp'],
            ],
            [
                'key'   => 'ptit',
                'name'  => 'Học viện Công nghệ Bưu chính Viễn thông',
                'email' => 'ptit@demo.local',
                'web'   => 'https://ptit.edu.vn',
                'addr'  => 'Km10 đường Nguyễn Trãi, quận Hà Đông, Hà Nội',
                'desc'  => 'Đào tạo chuyên sâu về Công nghệ thông tin, An toàn thông tin và '
                         . 'Kỹ thuật Điện tử Viễn thông, gắn bó chặt chẽ với các doanh nghiệp viễn thông.',
                'affair' => ['Lê Thị Thu Hà', 'vp.ptit@demo.local', 'Phòng Chính trị và Công tác sinh viên'],
            ],
            [
                'key'   => 'uet',
                'name'  => 'Trường Đại học Công nghệ - ĐHQG Hà Nội',
                'email' => 'uet@demo.local',
                'web'   => 'https://uet.vnu.edu.vn',
                'addr'  => 'Số 144 Xuân Thủy, phường Dịch Vọng Hậu, quận Cầu Giấy, Hà Nội',
                'desc'  => 'Trường thành viên của Đại học Quốc gia Hà Nội, thế mạnh về Khoa học máy tính, '
                         . 'Trí tuệ nhân tạo và Công nghệ thông tin.',
                'affair' => ['Phạm Văn Tuấn', 'vp.uet@demo.local', 'Phòng Công tác sinh viên'],
            ],
            [
                'key'   => 'tlu',
                'name'  => 'Trường Đại học Thủy lợi',
                'email' => 'tlu@demo.local',
                'web'   => 'https://tlu.edu.vn',
                'addr'  => 'Số 175 Tây Sơn, phường Trung Liệt, quận Đống Đa, Hà Nội',
                'desc'  => 'Đào tạo đa ngành với thế mạnh về Kỹ thuật xây dựng, Kỹ thuật môi trường '
                         . 'và Công nghệ thông tin.',
                'affair' => ['Đỗ Thị Mai Anh', 'vp.tlu@demo.local', 'Phòng Đào tạo đại học'],
            ],
        ];

        $out = [];
        foreach ($data as $row) {
            $u = $this->user($row['name'], $row['email'], 'university');
            $uni = University::create([
                'user_id'         => $u->id,
                'university_name' => $row['name'],
                'website'         => $row['web'],
                'description'     => $row['desc'],
                'address'         => $row['addr'],
            ]);

            // Tài khoản giáo vụ — minh hoạ vai trò phụ academic_affairs
            [$affName, $affEmail, $dept] = $row['affair'];
            $affUser = $this->user($affName, $affEmail, 'academic_affairs');
            AcademicAffair::create([
                'user_id'       => $affUser->id,
                'university_id' => $uni->id,
                'department'    => $dept,
            ]);

            $out[$row['key']] = $uni;
        }

        return $out;
    }

    // ------------------------------------------------------------------ doanh nghiệp

    private function seedCompanies(): array
    {
        $data = [
            [
                'key'   => 'fpt',
                'name'  => 'Công ty TNHH Phần mềm FPT (FPT Software)',
                'email' => 'hr.fpt@demo.local',
                'web'   => 'https://fptsoftware.com',
                'addr'  => 'Tòa nhà FPT, Khu Công nghệ cao Hòa Lạc, huyện Thạch Thất, Hà Nội',
                'desc'  => 'Công ty phần mềm lớn nhất Việt Nam, chuyên gia công và chuyển đổi số cho '
                         . 'khách hàng tại Nhật Bản, Mỹ và châu Âu. Chương trình FPT Fresher tuyển '
                         . 'sinh viên mới tốt nghiệp theo nhiều đợt trong năm.',
                'hr'    => ['Nguyễn Thu Trang', 'ts.fpt@demo.local', 'Chuyên viên Tuyển dụng'],
            ],
            [
                'key'   => 'viettel',
                'name'  => 'Tổng Công ty Giải pháp Doanh nghiệp Viettel',
                'email' => 'hr.viettel@demo.local',
                'web'   => 'https://viettelsolutions.vn',
                'addr'  => 'Số 1 Trần Hữu Dực, phường Mỹ Đình 2, quận Nam Từ Liêm, Hà Nội',
                'desc'  => 'Đơn vị thành viên của Tập đoàn Viettel, cung cấp giải pháp chuyển đổi số '
                         . 'cho khối chính phủ và doanh nghiệp: đô thị thông minh, y tế số, giáo dục số.',
                'hr'    => ['Hoàng Minh Đức', 'ts.viettel@demo.local', 'Phụ trách Tuyển dụng khối CNTT'],
            ],
            [
                'key'   => 'misa',
                'name'  => 'Công ty Cổ phần MISA',
                'email' => 'hr.misa@demo.local',
                'web'   => 'https://misa.vn',
                'addr'  => 'Tòa nhà Technosoft, số 8 Duy Tân, quận Cầu Giấy, Hà Nội',
                'desc'  => 'Doanh nghiệp phần mềm quản trị tài chính - kế toán hàng đầu Việt Nam với '
                         . 'các sản phẩm MISA AMIS, MISA SME phục vụ hàng trăm nghìn tổ chức.',
                'hr'    => ['Vũ Thị Ngọc Ánh', 'ts.misa@demo.local', 'Chuyên viên Nhân sự'],
            ],
            [
                'key'   => 'cmc',
                'name'  => 'Công ty TNHH CMC Global',
                'email' => 'hr.cmc@demo.local',
                'web'   => 'https://cmcglobal.com.vn',
                'addr'  => 'Tòa nhà CMC, số 11 Duy Tân, quận Cầu Giấy, Hà Nội',
                'desc'  => 'Thành viên Tập đoàn Công nghệ CMC, chuyên xuất khẩu phần mềm và dịch vụ '
                         . 'công nghệ cho thị trường Nhật Bản, Hàn Quốc và khu vực APAC.',
                'hr'    => ['Đặng Hải Nam', 'ts.cmc@demo.local', 'Talent Acquisition Executive'],
            ],
            [
                'key'   => 'rikkei',
                'name'  => 'Công ty Cổ phần Rikkeisoft',
                'email' => 'hr.rikkei@demo.local',
                'web'   => 'https://rikkeisoft.com',
                'addr'  => 'Tòa nhà Housing, số 299 Trung Kính, quận Cầu Giấy, Hà Nội',
                'desc'  => 'Công ty công nghệ chuyên thị trường Nhật Bản, mạnh về phát triển ứng dụng '
                         . 'web, mobile và dịch vụ Offshore Development Center.',
                'hr'    => ['Bùi Thị Phương Thảo', 'ts.rikkei@demo.local', 'Chuyên viên Tuyển dụng'],
            ],
            [
                'key'   => 'nashtech',
                'name'  => 'Công ty TNHH NashTech Việt Nam',
                'email' => 'hr.nashtech@demo.local',
                'web'   => 'https://nashtechglobal.com',
                'addr'  => 'Tòa nhà Handico, đường Phạm Hùng, quận Nam Từ Liêm, Hà Nội',
                'desc'  => 'Công ty công nghệ thuộc tập đoàn Nash Squared (Anh Quốc), cung cấp dịch vụ '
                         . 'phát triển phần mềm và tư vấn công nghệ cho khách hàng toàn cầu.',
                'hr'    => ['Trịnh Quốc Khánh', 'ts.nashtech@demo.local', 'HR Business Partner'],
            ],
            [
                'key'   => 'savvycom',
                'name'  => 'Công ty Cổ phần Phần mềm Savvycom',
                'email' => 'hr.savvycom@demo.local',
                'web'   => 'https://savvycomsoftware.com',
                'addr'  => 'Tòa nhà Sunrise, số 90 Trần Thái Tông, quận Cầu Giấy, Hà Nội',
                'desc'  => 'Công ty phát triển phần mềm tập trung vào lĩnh vực y tế số, fintech và '
                         . 'thương mại điện tử cho khách hàng Mỹ, Úc và châu Âu.',
                'hr'    => ['Ngô Thị Lan Hương', 'ts.savvycom@demo.local', 'Chuyên viên Tuyển dụng'],
            ],
            [
                'key'   => 'vietmap',
                'name'  => 'Công ty Cổ phần Tư vấn Trắc địa và Bản đồ Thăng Long',
                'email' => 'hr.thanglong@demo.local',
                'web'   => null,
                'addr'  => 'Số 25 Nguyễn Xiển, phường Hạ Đình, quận Thanh Xuân, Hà Nội',
                'desc'  => 'Doanh nghiệp tư vấn khảo sát địa hình, trắc địa công trình và xây dựng '
                         . 'cơ sở dữ liệu GIS cho các dự án hạ tầng khu vực phía Bắc.',
                'hr'    => ['Lý Văn Cường', 'ts.thanglong@demo.local', 'Trưởng phòng Hành chính - Nhân sự'],
            ],
        ];

        $out = [];
        foreach ($data as $row) {
            $u = $this->user($row['name'], $row['email'], 'company');
            $co = Company::create([
                'user_id'      => $u->id,
                'company_name' => $row['name'],
                'website'      => $row['web'],
                'description'  => $row['desc'],
                'address'      => $row['addr'],
            ]);

            // Tài khoản nhân viên tuyển dụng — minh hoạ vai trò phụ hiring
            [$hrName, $hrEmail, $position] = $row['hr'];
            $hrUser = $this->user($hrName, $hrEmail, 'hiring');
            Hiring::create([
                'user_id'    => $hrUser->id,
                'company_id' => $co->id,
                'position'   => $position,
            ]);

            $out[$row['key']] = $co;
        }

        // Hai hồ sơ đang chờ quản trị viên phê duyệt, để demo màn hình Duyệt tài khoản
        $p1 = $this->user('Công ty TNHH Công nghệ Sao Bắc Đẩu Hà Nội', 'hr.saobacdau@demo.local', 'company', false);
        Company::create([
            'user_id'      => $p1->id,
            'company_name' => 'Công ty TNHH Công nghệ Sao Bắc Đẩu Hà Nội',
            'address'      => 'Số 12 Nguyễn Chí Thanh, quận Ba Đình, Hà Nội',
            'description'  => 'Tích hợp hệ thống và hạ tầng công nghệ thông tin.',
        ]);

        $p2 = $this->user('Trường Đại học Điện lực', 'epu@demo.local', 'university', false);
        University::create([
            'user_id'         => $p2->id,
            'university_name' => 'Trường Đại học Điện lực',
            'address'         => 'Số 235 Hoàng Quốc Việt, quận Bắc Từ Liêm, Hà Nội',
            'description'     => 'Đào tạo kỹ sư ngành điện, tự động hóa và công nghệ thông tin.',
        ]);

        return $out;
    }

    // ------------------------------------------------------------------ hợp tác

    private function seedCollaborations(array $co, array $uni): void
    {
        // [công ty, trường, trạng thái, bên khởi tạo, số ngày trước]
        $rows = [
            ['fpt', 'humg', 'approved', 'company', 96],
            ['fpt', 'hust', 'approved', 'university', 140],
            ['fpt', 'ptit', 'approved', 'company', 75],
            ['viettel', 'humg', 'approved', 'university', 61],
            ['viettel', 'ptit', 'approved', 'company', 120],
            ['misa', 'humg', 'approved', 'company', 44],
            ['misa', 'tlu', 'approved', 'university', 88],
            ['cmc', 'hust', 'approved', 'company', 52],
            ['cmc', 'uet', 'approved', 'university', 37],
            ['rikkei', 'humg', 'approved', 'company', 29],
            ['rikkei', 'uet', 'approved', 'university', 66],
            ['nashtech', 'hust', 'approved', 'company', 105],
            ['savvycom', 'tlu', 'approved', 'university', 25],
            ['vietmap', 'humg', 'approved', 'company', 18],

            // Đang chờ trường/doanh nghiệp phản hồi
            ['nashtech', 'humg', 'pending', 'company', 6],
            ['savvycom', 'humg', 'pending', 'company', 3],
            ['cmc', 'ptit', 'pending', 'company', 9],
            ['viettel', 'uet', 'pending', 'university', 4],

            // Đã bị từ chối — minh hoạ quy tắc chờ 30 ngày mới gửi lại
            ['vietmap', 'hust', 'rejected', 'company', 12],
            ['savvycom', 'ptit', 'rejected', 'university', 41],
        ];

        foreach ($rows as [$c, $u, $status, $by, $days]) {
            $at = now()->subDays($days);
            Collaboration::create([
                'company_id'    => $co[$c]->id,
                'university_id' => $uni[$u]->id,
                'status'        => $status,
                'initiated_by'  => $by,
                'created_at'    => $at,
                'updated_at'    => $at->copy()->addDays(min(3, $days)),
            ]);
        }
    }

    // ------------------------------------------------------------------ workshop

    private function seedWorkshops(array $co, array $uni): void
    {
        $rows = [
            ['humg', 'fpt', 'Ngày hội Việc làm & Kết nối Doanh nghiệp HUMG 2026',
             'Sự kiện thường niên quy tụ hơn 40 doanh nghiệp tuyển dụng trực tiếp tại sân trường. '
             . 'Sinh viên năm 3, năm 4 mang theo CV để phỏng vấn nhanh tại gian hàng.', 14],
            ['humg', 'viettel', 'Hội thảo: Lộ trình nghề nghiệp ngành Công nghệ thông tin',
             'Chia sẻ từ kỹ sư đang làm việc tại doanh nghiệp về lộ trình từ thực tập sinh lên '
             . 'kỹ sư chính thức, các kỹ năng cần chuẩn bị và cách viết CV thuyết phục.', 27],
            ['humg', null, 'Workshop: Kỹ năng phỏng vấn và đàm phán lương cho sinh viên mới ra trường',
             'Buổi thực hành phỏng vấn thử với phản hồi trực tiếp từ chuyên viên nhân sự.', 41],
            ['hust', 'cmc', 'Tech Talk: Kiến trúc hệ thống trong dự án phần mềm quy mô lớn',
             'Buổi chia sẻ về cách tổ chức mã nguồn, kiểm thử tự động và quy trình triển khai '
             . 'trong các dự án nhiều lập trình viên cùng tham gia.', 9],
            ['ptit', 'viettel', 'Seminar: An toàn thông tin trong chuyển đổi số',
             'Giới thiệu các lỗ hổng thường gặp trong ứng dụng web và cách phòng tránh.', 20],
            ['uet', 'rikkei', 'Định hướng làm việc tại thị trường Nhật Bản',
             'Chia sẻ về văn hóa làm việc, yêu cầu tiếng Nhật và cơ hội onsite tại Nhật.', 33],
            ['tlu', 'savvycom', 'Ngày hội Thực tập sinh Công nghệ 2026',
             'Doanh nghiệp giới thiệu chương trình thực tập có lương dành cho sinh viên năm cuối.', 6],
            // Sự kiện đã diễn ra
            ['humg', 'misa', 'Hội thảo: Ứng dụng AI trong sàng lọc hồ sơ tuyển dụng',
             'Tổng kết chương trình đã diễn ra với hơn 200 sinh viên tham dự.', -18],
            ['hust', 'fpt', 'FPT Software Career Day 2026',
             'Chương trình tuyển dụng Fresher đợt 1 năm 2026.', -35],
        ];

        foreach ($rows as [$u, $c, $title, $desc, $inDays]) {
            $date = now()->addDays($inDays)->startOfDay();
            Workshop::create([
                'university_id' => $uni[$u]->id,
                'company_id'    => $c ? $co[$c]->id : null,
                'title'         => $title,
                'description'   => $desc,
                'date'          => $date,
                'scheduled_at'  => $date->copy()->setTime(8, 30),
                'created_at'    => now()->subDays(max(1, abs($inDays) + 10)),
            ]);
        }
    }

    // ------------------------------------------------------------------ sinh viên

    /** @return Student[] */
    private function seedStudents(array $uni): array
    {
        // [họ tên, trường, ngành, mã SV, GPA, kỹ năng]
        $rows = [
            ['Nguyễn Văn An',      'humg', 'Kỹ thuật Phần mềm',   '2021060101', 3.42, ['PHP', 'Laravel', 'MySQL', 'Git']],
            ['Trần Thị Bích Ngọc', 'humg', 'Kỹ thuật Phần mềm',   '2021060102', 3.71, ['JavaScript', 'ReactJS', 'HTML/CSS', 'Git']],
            ['Lê Minh Hoàng',      'humg', 'Khoa học Máy tính',   '2021060103', 3.15, ['Python', 'MySQL', 'Linux']],
            ['Phạm Thu Hà',        'humg', 'Hệ thống Thông tin',  '2021060104', 3.58, ['MySQL', 'Phân tích nghiệp vụ', 'Tiếng Anh giao tiếp']],
            ['Hoàng Đức Duy',      'humg', 'Kỹ thuật Phần mềm',   '2021060105', 2.88, ['Java', 'MySQL', 'Git']],
            ['Vũ Thị Thanh Mai',   'humg', 'An toàn Thông tin',   '2021060106', 3.64, ['Linux', 'Python', 'Docker']],
            ['Đỗ Quang Huy',       'humg', 'Kỹ thuật Phần mềm',   '2021060107', 3.05, ['PHP', 'JavaScript', 'Bootstrap']],
            ['Ngô Thị Phương Linh','humg', 'Kỹ thuật Trắc địa - Bản đồ', '2021060108', 3.33, ['AutoCAD', 'ArcGIS', 'Trắc địa công trình']],
            ['Bùi Văn Thắng',      'humg', 'Kỹ thuật Trắc địa - Bản đồ', '2021060109', 3.21, ['MicroStation', 'AutoCAD', 'ArcGIS']],
            ['Đặng Thị Kim Chi',   'humg', 'Kỹ thuật Địa chất',   '2021060110', 3.47, ['AutoCAD', 'ArcGIS']],
            ['Trịnh Bá Long',      'humg', 'Kỹ thuật Mỏ',         '2021060111', 2.95, ['AutoCAD', 'Tiếng Anh giao tiếp']],
            ['Lý Thị Hồng Nhung',  'humg', 'Kế toán',             '2021060112', 3.52, ['Phân tích nghiệp vụ', 'Tiếng Anh giao tiếp']],
            ['Nguyễn Đình Phúc',   'humg', 'Kỹ thuật Phần mềm',   '2022060113', 3.29, ['Laravel', 'PHP', 'Vue.js']],
            ['Trần Anh Tuấn',      'humg', 'Trí tuệ Nhân tạo',    '2022060114', 3.83, ['Python', 'Django', 'MySQL', 'Docker']],
            ['Phan Thị Diệu Linh', 'humg', 'Kỹ thuật Phần mềm',   '2022060115', 3.11, ['HTML/CSS', 'JavaScript', 'Figma']],

            ['Nguyễn Hữu Đạt',     'hust', 'Khoa học Máy tính',   '20215401', 3.76, ['C#', '.NET', 'MySQL', 'Docker']],
            ['Lê Thị Quỳnh Anh',   'hust', 'Kỹ thuật Phần mềm',   '20215402', 3.68, ['Java', 'Spring Boot', 'PostgreSQL']],
            ['Trương Công Minh',   'hust', 'Trí tuệ Nhân tạo',    '20215403', 3.91, ['Python', 'AWS', 'Docker', 'Linux']],
            ['Đinh Thị Hải Yến',   'hust', 'Hệ thống Thông tin',  '20215404', 3.24, ['MySQL', 'Phân tích nghiệp vụ']],
            ['Cao Xuân Trường',    'hust', 'Kỹ thuật Phần mềm',   '20215405', 3.02, ['Node.js', 'JavaScript', 'MongoDB']],
            ['Nguyễn Thị Vân Anh', 'hust', 'Khoa học Máy tính',   '20215406', 3.55, ['Python', 'ReactJS', 'REST API']],
            ['Hà Minh Quân',       'hust', 'An toàn Thông tin',   '20215407', 3.39, ['Linux', 'Python', 'Redis']],
            ['Vương Thị Thùy Dung','hust', 'Kỹ thuật Phần mềm',   '20225408', 3.47, ['Java', 'Spring Boot', 'Git']],

            ['Nguyễn Bá Tùng',     'ptit', 'An toàn Thông tin',   'B21DCAT101', 3.62, ['Linux', 'Python', 'Docker', 'AWS']],
            ['Trần Thị Ngọc Ánh',  'ptit', 'Kỹ thuật Phần mềm',   'B21DCCN102', 3.44, ['PHP', 'Laravel', 'MySQL']],
            ['Lưu Quang Hải',      'ptit', 'Khoa học Máy tính',   'B21DCCN103', 3.18, ['Java', 'MySQL', 'Git']],
            ['Mai Thị Hồng Hạnh',  'ptit', 'Hệ thống Thông tin',  'B21DCHT104', 3.71, ['MySQL', 'Phân tích nghiệp vụ', 'Tiếng Anh giao tiếp']],
            ['Nguyễn Thế Anh',     'ptit', 'Kỹ thuật Phần mềm',   'B21DCCN105', 2.91, ['JavaScript', 'Node.js']],
            ['Phùng Thị Lan',      'ptit', 'An toàn Thông tin',   'B22DCAT106', 3.36, ['Linux', 'Python']],
            ['Đoàn Minh Khôi',     'ptit', 'Kỹ thuật Phần mềm',   'B22DCCN107', 3.58, ['ReactJS', 'JavaScript', 'REST API']],

            ['Nguyễn Hoàng Nam',   'uet',  'Trí tuệ Nhân tạo',    '21020301', 3.88, ['Python', 'Django', 'Docker', 'AWS']],
            ['Trần Khánh Ly',      'uet',  'Khoa học Máy tính',   '21020302', 3.65, ['Python', 'MySQL', 'Git']],
            ['Lê Đức Anh',         'uet',  'Kỹ thuật Phần mềm',   '21020303', 3.31, ['Vue.js', 'JavaScript', 'Node.js']],
            ['Phạm Thị Thu Trang', 'uet',  'Kỹ thuật Phần mềm',   '21020304', 3.49, ['ReactJS', 'TailwindCSS', 'Figma']],
            ['Hồ Sỹ Nguyên',       'uet',  'Khoa học Máy tính',   '21020305', 3.07, ['Java', 'PostgreSQL']],
            ['Nguyễn Thị Mỹ Duyên','uet',  'Hệ thống Thông tin',  '22020306', 3.53, ['MySQL', 'Phân tích nghiệp vụ']],

            ['Trần Văn Kiên',      'tlu',  'Kỹ thuật Phần mềm',   '2154060201', 3.22, ['PHP', 'Laravel', 'Bootstrap']],
            ['Nguyễn Thị Hoài Thu','tlu',  'Kỹ thuật Môi trường', '2154060202', 3.44, ['AutoCAD', 'ArcGIS']],
            ['Lê Bá Hiếu',         'tlu',  'Kỹ thuật Phần mềm',   '2154060203', 2.85, ['JavaScript', 'HTML/CSS']],
            ['Đặng Thùy Dương',    'tlu',  'Kế toán',             '2154060204', 3.61, ['Phân tích nghiệp vụ', 'Tiếng Anh giao tiếp']],
            ['Nguyễn Trọng Nghĩa', 'tlu',  'Khoa học Máy tính',   '2254060205', 3.16, ['Python', 'MySQL']],
            ['Vũ Thị Ánh Tuyết',   'tlu',  'Kỹ thuật Phần mềm',   '2254060206', 3.38, ['ReactJS', 'JavaScript', 'Git']],
        ];

        $skillIds = Skill::pluck('id', 'name')->all();
        Storage::disk('public')->makeDirectory('resumes');

        $students = [];
        foreach ($rows as $i => [$name, $uniKey, $majorName, $code, $gpa, $skills]) {
            $email = $this->emailFromName($name, $i);
            $u = $this->user($name, $email, 'student');

            $st = Student::create([
                'user_id'       => $u->id,
                'university_id' => $uni[$uniKey]->id,
                'major_id'      => $this->majorId($majorName),
                'student_code'  => $code,
                'gpa'           => $gpa,
            ]);

            $ids = array_values(array_filter(array_map(fn($s) => $skillIds[$s] ?? null, $skills)));
            $st->skills()->sync($ids);

            $this->makeResume($st, $name, $majorName, $uni[$uniKey]->university_name, $gpa, $skills);

            $students[] = $st;
        }

        return $students;
    }

    /** Sinh địa chỉ email từ tên tiếng Việt: "Nguyễn Văn An" -> an.nv01@demo.local */
    private function emailFromName(string $name, int $i): string
    {
        $parts = explode(' ', Str::lower(Str::ascii($name)));
        $given = array_pop($parts);
        $initials = implode('', array_map(fn($p) => $p[0] ?? '', $parts));

        return sprintf('%s.%s%02d@demo.local', $given, $initials, $i + 1);
    }

    /**
     * Tạo hồ sơ CV: một tệp PDF thật để nút "Xem CV" mở được, kèm phần nội dung
     * văn bản (cột content) chính là dữ liệu mà dịch vụ AI dùng để chấm điểm.
     */
    private function makeResume(Student $st, string $name, string $major, string $uniName, float $gpa, array $skills): void
    {
        $file = 'resumes/cv_' . $st->student_code . '_' . Str::random(8) . '.pdf';
        Storage::disk('public')->put($file, $this->buildPdf($name, $major, $uniName, $gpa, $skills));

        $content = "HỒ SƠ ỨNG VIÊN\n"
            . "Họ và tên: {$name}\n"
            . "Trường: {$uniName}\n"
            . "Chuyên ngành: {$major} — Điểm trung bình tích lũy: {$gpa}/4.0\n\n"
            . "MỤC TIÊU NGHỀ NGHIỆP\n"
            . "Tìm kiếm vị trí thực tập sinh hoặc kỹ sư mới tốt nghiệp đúng chuyên ngành "
            . "{$major}, mong muốn được tham gia dự án thực tế để tích lũy kinh nghiệm và "
            . "phát triển lên vị trí kỹ sư chính thức trong 2 năm tới.\n\n"
            . "KỸ NĂNG CHUYÊN MÔN\n- " . implode("\n- ", $skills) . "\n\n"
            . "DỰ ÁN ĐÃ THỰC HIỆN\n"
            . "- Đồ án môn học: xây dựng ứng dụng web quản lý theo mô hình MVC, có phân quyền "
            . "người dùng và kết nối cơ sở dữ liệu quan hệ.\n"
            . "- Bài tập lớn nhóm 4 thành viên: phân tích yêu cầu, thiết kế cơ sở dữ liệu và "
            . "lập trình các chức năng chính; sử dụng Git để quản lý mã nguồn.\n\n"
            . "HOẠT ĐỘNG\nThành viên câu lạc bộ Tin học của trường, tham gia hỗ trợ tổ chức "
            . "ngày hội việc làm thường niên.\n";

        Resume::create([
            'student_id'    => $st->id,
            'file_path'     => $file,
            'original_name' => 'CV_' . str_replace(' ', '_', Str::ascii($name)) . '.pdf',
            'content'       => $content,
            'is_default'    => true,
        ]);
    }

    /** Dựng một tệp PDF một trang hợp lệ (không dùng thư viện ngoài). */
    private function buildPdf(string $name, string $major, string $uniName, float $gpa, array $skills): string
    {
        // Font chuẩn của PDF không có sẵn dấu tiếng Việt nên bỏ dấu cho phần hiển thị.
        $esc = fn($s) => str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], Str::ascii($s));

        $lines = [
            [24, 'CURRICULUM VITAE'],
            [14, $esc($name)],
            [11, 'Truong: ' . $esc($uniName)],
            [11, 'Chuyen nganh: ' . $esc($major) . '  |  GPA: ' . $gpa . '/4.0'],
            [11, ''],
            [12, 'KY NANG'],
        ];
        foreach ($skills as $s) {
            $lines[] = [11, '  - ' . $esc($s)];
        }
        $lines[] = [11, ''];
        $lines[] = [12, 'MUC TIEU NGHE NGHIEP'];
        $lines[] = [11, '  Tim vi tri thuc tap / ky su moi tot nghiep dung chuyen nganh.'];
        $lines[] = [11, ''];
        $lines[] = [9, '(Tep CV mau do he thong Auto Career Bridge sinh ra de trinh dien.)'];

        $stream = "BT\n";
        $y = 780;
        foreach ($lines as [$size, $text]) {
            $stream .= sprintf("/F1 %d Tf\n1 0 0 1 60 %d Tm\n(%s) Tj\n", $size, $y, $text);
            $y -= $size + 8;
        }
        $stream .= "ET";

        $objects = [
            "<< /Type /Catalog /Pages 2 0 R >>",
            "<< /Type /Pages /Kids [3 0 R] /Count 1 >>",
            "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] "
                . "/Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>",
            "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>",
            "<< /Length " . strlen($stream) . " >>\nstream\n{$stream}\nendstream",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $i => $body) {
            $offsets[] = strlen($pdf);
            $pdf .= ($i + 1) . " 0 obj\n{$body}\nendobj\n";
        }

        $xref = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";
        foreach ($offsets as $off) {
            $pdf .= sprintf("%010d 00000 n \n", $off);
        }
        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n"
              . "startxref\n{$xref}\n%%EOF";

        return $pdf;
    }

    // ------------------------------------------------------------------ tin tuyển dụng

    /** @return Job[] */
    private function seedJobs(array $co): array
    {
        // [công ty, tiêu đề, ngành, đã duyệt?, số ngày trước, mô tả, yêu cầu]
        $rows = [
            ['fpt', 'Thực tập sinh Lập trình Java (Fresher Java Developer)', 'Kỹ thuật Phần mềm', true, 26,
             ['Tham gia phát triển các module của hệ thống quản trị doanh nghiệp cho khách hàng Nhật Bản',
              'Viết mã theo tài liệu đặc tả và quy chuẩn của dự án',
              'Tham gia kiểm thử đơn vị (unit test) và sửa lỗi được ghi nhận',
              'Được đào tạo 2 tháng trước khi vào dự án chính thức'],
             ['Sinh viên năm cuối hoặc đã tốt nghiệp ngành Công nghệ thông tin',
              'Nắm vững lập trình hướng đối tượng với Java',
              'Hiểu cơ bản về cơ sở dữ liệu quan hệ và câu lệnh SQL',
              'Ưu tiên ứng viên có tiếng Nhật N3 hoặc tiếng Anh giao tiếp tốt']],

            ['fpt', 'Fresher .NET Developer', 'Khoa học Máy tính', true, 21,
             ['Phát triển ứng dụng web trên nền tảng ASP.NET Core',
              'Làm việc trong nhóm 5-8 người theo mô hình Agile',
              'Tham gia code review cùng kỹ sư cấp cao'],
             ['Tốt nghiệp ngành CNTT, điểm trung bình từ 2.8/4.0',
              'Biết C# và Entity Framework',
              'Có kiến thức SQL Server hoặc MySQL']],

            ['fpt', 'Thực tập sinh Kiểm thử phần mềm (Manual Tester)', 'Kỹ thuật Phần mềm', true, 12,
             ['Đọc tài liệu đặc tả và viết kịch bản kiểm thử',
              'Thực hiện kiểm thử chức năng, ghi nhận và theo dõi lỗi',
              'Phối hợp với lập trình viên để xác nhận lỗi đã được sửa'],
             ['Sinh viên năm 3, năm 4 ngành CNTT hoặc gần ngành',
              'Cẩn thận, tư duy logic tốt',
              'Có kiến thức cơ bản về quy trình phát triển phần mềm']],

            ['viettel', 'Kỹ sư Phát triển phần mềm (Backend - Java/Spring Boot)', 'Kỹ thuật Phần mềm', true, 24,
             ['Xây dựng các dịch vụ backend cho nền tảng chính phủ số',
              'Thiết kế và hiện thực hóa REST API',
              'Tối ưu hiệu năng truy vấn cơ sở dữ liệu'],
             ['Tốt nghiệp đại học ngành CNTT, Điện tử viễn thông',
              'Thành thạo Java, có kinh nghiệm với Spring Boot',
              'Hiểu về Docker và triển khai ứng dụng trên Linux']],

            ['viettel', 'Chuyên viên An toàn thông tin', 'An toàn Thông tin', true, 17,
             ['Rà soát lỗ hổng bảo mật trên các ứng dụng web nội bộ',
              'Xây dựng quy trình phản ứng sự cố an ninh mạng',
              'Đào tạo nhận thức an toàn thông tin cho nhân viên'],
             ['Tốt nghiệp ngành An toàn thông tin hoặc CNTT',
              'Hiểu các lỗ hổng phổ biến: SQL Injection, XSS, CSRF',
              'Sử dụng thành thạo hệ điều hành Linux']],

            ['viettel', 'Thực tập sinh Phân tích dữ liệu', 'Trí tuệ Nhân tạo', true, 8,
             ['Thu thập và làm sạch dữ liệu vận hành',
              'Xây dựng báo cáo trực quan phục vụ ban lãnh đạo'],
             ['Sinh viên năm cuối ngành CNTT, Toán tin hoặc Thống kê',
              'Biết Python và thư viện xử lý dữ liệu',
              'Sử dụng được SQL ở mức truy vấn tổng hợp']],

            ['misa', 'Lập trình viên PHP/Laravel', 'Kỹ thuật Phần mềm', true, 28,
             ['Phát triển và bảo trì các phân hệ của nền tảng quản trị doanh nghiệp',
              'Viết mã sạch, có kiểm thử tự động đi kèm',
              'Tham gia phân tích yêu cầu cùng chuyên viên nghiệp vụ'],
             ['Có kinh nghiệm với PHP và framework Laravel',
              'Thành thạo MySQL, hiểu về tối ưu truy vấn',
              'Sử dụng Git thành thạo trong làm việc nhóm']],

            ['misa', 'Chuyên viên Phân tích nghiệp vụ (Business Analyst)', 'Hệ thống Thông tin', true, 19,
             ['Khảo sát và ghi nhận yêu cầu từ khách hàng',
              'Viết tài liệu đặc tả yêu cầu phần mềm',
              'Làm cầu nối giữa khách hàng và đội phát triển'],
             ['Tốt nghiệp ngành Hệ thống thông tin, CNTT hoặc Kinh tế',
              'Kỹ năng giao tiếp và trình bày tốt',
              'Biết vẽ sơ đồ nghiệp vụ (BPMN, DFD) là một lợi thế']],

            ['misa', 'Thực tập sinh Kế toán', 'Kế toán', true, 10,
             ['Hỗ trợ nhập liệu và đối chiếu chứng từ',
              'Tham gia lập báo cáo tài chính định kỳ'],
             ['Sinh viên năm cuối ngành Kế toán, Tài chính',
              'Sử dụng tốt Excel',
              'Cẩn thận, trung thực']],

            ['cmc', 'Fresher Frontend Developer (ReactJS)', 'Kỹ thuật Phần mềm', true, 22,
             ['Xây dựng giao diện người dùng theo thiết kế từ đội UI/UX',
              'Tích hợp API với backend',
              'Bảo đảm giao diện hiển thị tốt trên nhiều kích thước màn hình'],
             ['Nắm vững HTML, CSS, JavaScript',
              'Đã làm quen với ReactJS qua đồ án hoặc dự án cá nhân',
              'Biết sử dụng Git']],

            ['cmc', 'Kỹ sư DevOps', 'Khoa học Máy tính', true, 15,
             ['Xây dựng và duy trì pipeline tích hợp - triển khai liên tục',
              'Quản trị hạ tầng container',
              'Giám sát hệ thống và xử lý sự cố'],
             ['Có kinh nghiệm với Docker, Linux',
              'Hiểu về hạ tầng đám mây (AWS hoặc Azure)',
              'Biết viết script tự động hóa']],

            ['cmc', 'Thực tập sinh Lập trình cầu nối tiếng Nhật (BrSE Intern)', 'Ngôn ngữ Nhật', true, 5,
             ['Hỗ trợ dịch tài liệu kỹ thuật Nhật - Việt',
              'Tham gia họp với khách hàng Nhật cùng kỹ sư cầu nối'],
             ['Tiếng Nhật từ N3 trở lên',
              'Có kiến thức nền về công nghệ thông tin',
              'Chủ động, ham học hỏi']],

            ['rikkei', 'Fresher PHP Developer', 'Kỹ thuật Phần mềm', true, 20,
             ['Phát triển ứng dụng web cho khách hàng Nhật Bản',
              'Tham gia đầy đủ vòng đời phát triển: phân tích, lập trình, kiểm thử'],
             ['Biết PHP, ưu tiên đã dùng Laravel',
              'Hiểu cơ bản HTML, CSS, JavaScript',
              'Tinh thần trách nhiệm, làm việc nhóm tốt']],

            ['rikkei', 'Thực tập sinh Mobile Developer (Flutter)', 'Kỹ thuật Phần mềm', true, 11,
             ['Phát triển ứng dụng di động đa nền tảng',
              'Tham gia kiểm thử trên thiết bị thật'],
             ['Sinh viên năm 3, năm 4 ngành CNTT',
              'Biết ít nhất một ngôn ngữ lập trình hướng đối tượng',
              'Có sản phẩm cá nhân là lợi thế']],

            ['nashtech', 'Junior Software Engineer (Node.js)', 'Kỹ thuật Phần mềm', true, 25,
             ['Phát triển dịch vụ backend bằng Node.js',
              'Viết kiểm thử tự động cho các API đã xây dựng',
              'Làm việc trực tiếp với khách hàng nói tiếng Anh'],
             ['Tiếng Anh đọc hiểu tài liệu kỹ thuật tốt',
              'Biết JavaScript và Node.js',
              'Hiểu về cơ sở dữ liệu quan hệ hoặc MongoDB']],

            ['nashtech', 'Fresher Quality Engineer (Automation Test)', 'Kỹ thuật Phần mềm', true, 13,
             ['Viết kịch bản kiểm thử tự động',
              'Duy trì bộ kiểm thử hồi quy'],
             ['Tốt nghiệp ngành CNTT',
              'Có tư duy lập trình cơ bản',
              'Tiếng Anh giao tiếp là lợi thế']],

            ['savvycom', 'Thực tập sinh Lập trình Web Full-stack', 'Kỹ thuật Phần mềm', true, 16,
             ['Tham gia phát triển sản phẩm trong lĩnh vực y tế số',
              'Được kèm cặp 1-1 bởi kỹ sư cấp cao'],
             ['Sinh viên năm cuối ngành CNTT',
              'Biết ít nhất một framework backend và một framework frontend',
              'Chủ động tìm hiểu công nghệ mới']],

            ['savvycom', 'UI/UX Designer', 'Kỹ thuật Phần mềm', true, 7,
             ['Thiết kế giao diện cho ứng dụng web và di động',
              'Xây dựng hệ thống thiết kế dùng chung cho sản phẩm'],
             ['Sử dụng thành thạo Figma',
              'Có portfolio các sản phẩm đã thiết kế',
              'Hiểu nguyên tắc thiết kế đáp ứng']],

            ['vietmap', 'Kỹ sư Trắc địa - Bản đồ', 'Kỹ thuật Trắc địa - Bản đồ', true, 23,
             ['Thực hiện đo đạc khảo sát địa hình phục vụ dự án hạ tầng',
              'Xử lý số liệu và biên tập bản đồ số',
              'Đi công tác tại các tỉnh phía Bắc theo dự án'],
             ['Tốt nghiệp ngành Trắc địa - Bản đồ hoặc Kỹ thuật địa chất',
              'Sử dụng thành thạo AutoCAD, MicroStation',
              'Có chứng chỉ hành nghề là lợi thế']],

            ['vietmap', 'Chuyên viên GIS', 'Kỹ thuật Địa chất', true, 14,
             ['Xây dựng và quản trị cơ sở dữ liệu không gian',
              'Phân tích dữ liệu địa lý phục vụ quy hoạch'],
             ['Thành thạo ArcGIS hoặc QGIS',
              'Hiểu về hệ tọa độ và phép chiếu bản đồ',
              'Biết SQL cơ bản']],

            // Đang chờ quản trị viên duyệt — để demo màn hình Kiểm duyệt tin
            ['fpt', 'Kỹ sư Cầu nối (BrSE) thị trường Nhật Bản', 'Ngôn ngữ Nhật', false, 2,
             ['Làm việc trực tiếp với khách hàng Nhật Bản',
              'Chuyển hóa yêu cầu nghiệp vụ thành tài liệu cho đội phát triển'],
             ['Tiếng Nhật N2 trở lên',
              'Có kinh nghiệm lập trình từ 1 năm',
              'Sẵn sàng công tác tại Nhật Bản']],

            ['misa', 'Thực tập sinh Marketing sản phẩm phần mềm', 'Marketing', false, 1,
             ['Hỗ trợ triển khai chiến dịch truyền thông sản phẩm',
              'Viết nội dung cho website và mạng xã hội'],
             ['Sinh viên năm 3, năm 4 ngành Marketing hoặc Truyền thông',
              'Kỹ năng viết tốt',
              'Biết dùng công cụ thiết kế cơ bản']],

            ['rikkei', 'Kỹ sư Kiểm thử tự động (Automation Engineer)', 'Kỹ thuật Phần mềm', false, 3,
             ['Xây dựng khung kiểm thử tự động cho sản phẩm web',
              'Tích hợp bộ kiểm thử vào pipeline triển khai'],
             ['Có kinh nghiệm kiểm thử tự động từ 1 năm',
              'Biết Selenium hoặc Playwright',
              'Hiểu về CI/CD']],
        ];

        $jobs = [];
        foreach ($rows as [$c, $title, $majorName, $approved, $days, $desc, $req]) {
            $at = now()->subDays($days);
            $jobs[] = Job::create([
                'company_id'   => $co[$c]->id,
                'major_id'     => $this->majorId($majorName),
                'title'        => $title,
                'description'  => $this->html('Mô tả công việc', $desc),
                'requirements' => $this->html('Yêu cầu ứng viên', $req),
                'is_approved'  => $approved,
                'created_at'   => $at,
                'updated_at'   => $at,
            ]);
        }

        return $jobs;
    }

    /** Nội dung dạng HTML giống hệt thứ CKEditor sinh ra và HTMLPurifier cho phép. */
    private function html(string $heading, array $items): string
    {
        $li = implode('', array_map(fn($i) => '<li>' . e($i) . '</li>', $items));

        return "<h3>{$heading}</h3><ul>{$li}</ul>";
    }

    // ------------------------------------------------------------------ ứng tuyển

    private function seedApplications(array $students, array $jobs): void
    {
        $openJobs = array_values(array_filter($jobs, fn($j) => $j->is_approved));
        mt_srand(20260812); // cố định để mỗi lần seed cho ra cùng một bộ dữ liệu

        $reviews = [
            'high' => [
                'Ứng viên có nền tảng vững về các công nghệ cốt lõi mà vị trí yêu cầu, đã thực hành qua đồ án môn học có sản phẩm cụ thể. Điểm trung bình khá tốt, kỹ năng liệt kê trong CV trùng khớp phần lớn với yêu cầu bắt buộc. Điểm cần lưu ý là chưa có kinh nghiệm làm việc trong dự án thực tế nhiều thành viên.',
                'Hồ sơ thể hiện rõ định hướng đúng chuyên ngành. Các kỹ năng chính đều xuất hiện trong yêu cầu công việc, có kinh nghiệm sử dụng công cụ quản lý mã nguồn. Đề xuất mời phỏng vấn để đánh giá thêm khả năng giải quyết vấn đề thực tế.',
            ],
            'mid' => [
                'Ứng viên đáp ứng được khoảng một nửa yêu cầu bắt buộc. Có nền tảng lập trình cơ bản nhưng còn thiếu kinh nghiệm với framework mà vị trí này sử dụng hằng ngày. Có thể cân nhắc nếu doanh nghiệp chấp nhận đào tạo thêm trong 1-2 tháng đầu.',
                'Hồ sơ ở mức trung bình khá. Kiến thức nền tương đối ổn, tuy nhiên phần kỹ năng chuyên sâu mà vị trí yêu cầu chưa được thể hiện rõ trong CV. Nên trao đổi thêm ở vòng phỏng vấn để làm rõ mức độ thành thạo thực tế.',
            ],
            'low' => [
                'Chuyên ngành và bộ kỹ năng trong CV lệch khá xa so với yêu cầu của vị trí. Ứng viên thiếu hầu hết các công nghệ cốt lõi được nêu trong phần yêu cầu bắt buộc. Chưa phù hợp với vị trí này ở thời điểm hiện tại.',
                'Hồ sơ chưa đáp ứng các yêu cầu bắt buộc. Kinh nghiệm và kỹ năng được liệt kê thuộc lĩnh vực khác với mô tả công việc. Khuyến nghị ứng viên tham khảo các vị trí đúng chuyên ngành hơn trên hệ thống.',
            ],
        ];

        $made = [];
        foreach ($students as $st) {
            // Mỗi sinh viên nộp 2-5 hồ sơ, ưu tiên tin đúng chuyên ngành.
            $sameMajor = array_values(array_filter($openJobs, fn($j) => $j->major_id === $st->major_id));
            $others    = array_values(array_filter($openJobs, fn($j) => $j->major_id !== $st->major_id));
            shuffle($sameMajor);
            shuffle($others);

            $picked = array_slice(array_merge($sameMajor, $others), 0, mt_rand(2, 5));

            foreach ($picked as $job) {
                $key = $job->id . '-' . $st->id;
                if (isset($made[$key])) {
                    continue;
                }
                $made[$key] = true;

                $isMatch = $job->major_id === $st->major_id;
                // Điểm phù hợp: đúng ngành + GPA cao thì điểm cao.
                $base = $isMatch ? 68 : 34;
                $score = min(97, max(18, (int) round($base + ($st->gpa - 3.0) * 22 + mt_rand(-9, 9))));

                $band = $score >= 72 ? 'high' : ($score >= 48 ? 'mid' : 'low');

                // Khoảng 8% hồ sơ chưa chấm được điểm — minh hoạ cách hệ thống
                // ứng xử khi dịch vụ AI không phản hồi (không bịa ra điểm số).
                $unscored = mt_rand(1, 100) <= 8;

                $createdAt = now()->subDays(mt_rand(0, 29))->subHours(mt_rand(0, 23));

                // Hồ sơ nộp lâu thì đã được xử lý; hồ sơ mới thì còn chờ.
                $ageDays = $createdAt->diffInDays(now());
                if ($ageDays > 12) {
                    $status = $score >= 72 ? 'accepted' : 'rejected';
                } elseif ($ageDays > 5) {
                    $status = mt_rand(1, 100) <= 55 ? 'reviewed' : ($score >= 72 ? 'accepted' : 'rejected');
                } else {
                    $status = 'pending';
                }
                if ($unscored && $status !== 'pending') {
                    $status = 'reviewed';
                }

                $feedback = null;
                if ($status === 'accepted') {
                    $feedback = 'Hồ sơ phù hợp với vị trí. Bộ phận nhân sự sẽ liên hệ qua email '
                              . 'để hẹn lịch phỏng vấn vòng 1 trong tuần tới.';
                } elseif ($status === 'rejected') {
                    $feedback = 'Cảm ơn bạn đã quan tâm tới vị trí. Rất tiếc hồ sơ lần này chưa '
                              . 'phù hợp với yêu cầu. Chúc bạn sớm tìm được công việc như ý.';
                }

                Application::create([
                    'job_id'      => $job->id,
                    'student_id'  => $st->id,
                    'status'      => $status,
                    'ai_score'    => $unscored ? null : $score,
                    'ai_review'   => $unscored
                        ? 'Chưa có đánh giá tự động. Lý do: Chưa cấu hình API key cho dịch vụ AI. '
                          . 'Vui lòng xem xét hồ sơ thủ công.'
                        : $reviews[$band][mt_rand(0, 1)],
                    'hr_feedback' => $feedback,
                    'created_at'  => $createdAt,
                    'updated_at'  => $status === 'pending' ? $createdAt : $createdAt->copy()->addDays(mt_rand(1, 4)),
                ]);
            }
        }
    }

    // ------------------------------------------------------------------ thông báo

    private function seedNotifications(): void
    {
        // Thông báo cho sinh viên về kết quả các hồ sơ đã có quyết định
        $decided = Application::whereIn('status', ['accepted', 'rejected'])
            ->with('student.user', 'job.company')
            ->latest()
            ->take(60)
            ->get();

        foreach ($decided as $app) {
            $accepted = $app->status === 'accepted';
            Notification::create([
                'user_id'    => $app->student->user_id,
                'title'      => $accepted ? 'Hồ sơ đã được chấp nhận' : 'Kết quả ứng tuyển',
                'message'    => $accepted
                    ? "Hồ sơ ứng tuyển của bạn cho vị trí {$app->job->title} tại "
                      . "{$app->job->company->company_name} đã được chấp nhận!"
                    : "Rất tiếc, hồ sơ ứng tuyển của bạn cho vị trí {$app->job->title} tại "
                      . "{$app->job->company->company_name} chưa phù hợp ở lần này.",
                'type'       => $accepted ? 'success' : 'danger',
                'url'        => '/dashboard',
                'is_read'    => mt_rand(1, 100) <= 45,
                'created_at' => $app->updated_at,
                'updated_at' => $app->updated_at,
            ]);
        }

        // Thông báo cho doanh nghiệp về các hồ sơ mới nhận
        $fresh = Application::where('status', 'pending')
            ->with('student.user', 'job.company.user')
            ->latest()
            ->take(35)
            ->get();

        foreach ($fresh as $app) {
            Notification::create([
                'user_id'    => $app->job->company->user_id,
                'title'      => 'Ứng viên mới',
                'message'    => "{$app->student->user->name} vừa ứng tuyển vào vị trí {$app->job->title}.",
                'type'       => 'info',
                'url'        => '/company/applications',
                'is_read'    => mt_rand(1, 100) <= 30,
                'created_at' => $app->created_at,
                'updated_at' => $app->created_at,
            ]);
        }

        // Thông báo cho quản trị viên
        $admin = User::where('role', 'admin')->first();
        $pendingJobs = Job::where('is_approved', false)->count();
        $pendingUsers = User::where('is_active', false)->count();

        Notification::create([
            'user_id' => $admin->id,
            'title'   => 'Có tin tuyển dụng chờ kiểm duyệt',
            'message' => "Hiện có {$pendingJobs} tin tuyển dụng đang chờ được phê duyệt.",
            'type'    => 'warning',
            'url'     => '/admin/jobs',
            'is_read' => false,
        ]);
        Notification::create([
            'user_id' => $admin->id,
            'title'   => 'Có tài khoản chờ phê duyệt',
            'message' => "Hiện có {$pendingUsers} tài khoản tổ chức đang chờ được xét duyệt.",
            'type'    => 'warning',
            'url'     => '/admin/users',
            'is_read' => false,
        ]);
    }

    // ------------------------------------------------------------------ tổng kết

    private function summary(): void
    {
        $rows = [
            ['Người dùng',        User::count()],
            ['Trường đại học',    University::count()],
            ['Doanh nghiệp',      Company::count()],
            ['Sinh viên',         Student::count()],
            ['Nhân viên tuyển dụng', Hiring::count()],
            ['Giáo vụ',           AcademicAffair::count()],
            ['Tin tuyển dụng',    Job::count() . ' (chờ duyệt: ' . Job::where('is_approved', false)->count() . ')'],
            ['Hồ sơ ứng tuyển',   Application::count()],
            ['  – chưa chấm điểm', Application::whereNull('ai_score')->count()],
            ['CV đã tải lên',     Resume::count()],
            ['Quan hệ hợp tác',   Collaboration::count()],
            ['Workshop',          Workshop::count()],
            ['Thông báo',         Notification::count()],
        ];

        $this->command->newLine();
        $this->command->table(['Dữ liệu', 'Số lượng'], $rows);
        $this->command->info('Mật khẩu của MỌI tài khoản demo: password');
        $this->command->line('  Quản trị viên : admin@demo.local');
        $this->command->line('  Doanh nghiệp  : hr.fpt@demo.local   (nhân sự: ts.fpt@demo.local)');
        $this->command->line('  Trường học    : humg@demo.local     (giáo vụ: vp.humg@demo.local)');
        $this->command->line('  Sinh viên     : an.nv01@demo.local');
    }
}
