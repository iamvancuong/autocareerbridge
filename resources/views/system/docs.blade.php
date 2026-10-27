<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AutoCareerBridge — Tài liệu hệ thống (Bảo vệ đồ án)</title>
    <style>
        :root{
            --bg:#0f172a; --panel:#111c33; --panel2:#0b1526; --line:#1e2b45;
            --text:#e8eef7; --muted:#93a4c0; --accent:#4f8cff; --accent2:#22c55e;
            --warn:#f59e0b; --danger:#ef4444; --code:#0a1120; --chip:#182843;
        }
        *{box-sizing:border-box}
        body{margin:0;font-family:"Segoe UI",Roboto,Arial,sans-serif;background:var(--bg);color:var(--text);line-height:1.55;font-size:15px}
        a{color:var(--accent);text-decoration:none}
        header.top{padding:22px 26px;background:linear-gradient(120deg,#0b1526,#132347);border-bottom:1px solid var(--line)}
        header.top h1{margin:0 0 4px;font-size:22px}
        header.top p{margin:0;color:var(--muted);font-size:14px}
        .wrap{max-width:1200px;margin:0 auto;padding:0 20px}
        nav.tabs{position:sticky;top:0;z-index:10;background:var(--panel2);border-bottom:1px solid var(--line);display:flex;flex-wrap:wrap;gap:4px;padding:8px 20px}
        nav.tabs button{background:transparent;border:1px solid transparent;color:var(--muted);padding:9px 15px;border-radius:8px;cursor:pointer;font-size:14px;font-weight:600}
        nav.tabs button:hover{color:var(--text);background:var(--chip)}
        nav.tabs button.active{color:#fff;background:var(--accent);border-color:var(--accent)}
        main{padding:24px 0 80px}
        section.tab{display:none;animation:fade .2s ease}
        section.tab.active{display:block}
        @keyframes fade{from{opacity:0;transform:translateY(4px)}to{opacity:1}}
        h2{font-size:20px;margin:26px 0 12px;padding-bottom:8px;border-bottom:1px solid var(--line)}
        h3{font-size:16px;margin:22px 0 8px;color:#cfe0ff}
        h4{font-size:14px;margin:14px 0 6px;color:var(--muted);text-transform:uppercase;letter-spacing:.4px}
        p{margin:8px 0}
        .muted{color:var(--muted)}
        .grid{display:grid;gap:14px}
        .cols-2{grid-template-columns:1fr 1fr}
        .cols-3{grid-template-columns:repeat(3,1fr)}
        @media(max-width:900px){.cols-2,.cols-3{grid-template-columns:1fr}}
        .card{background:var(--panel);border:1px solid var(--line);border-radius:12px;padding:16px}
        .card.tight{padding:12px 14px}
        .pill{display:inline-block;padding:2px 9px;border-radius:999px;font-size:12px;font-weight:700;background:var(--chip);color:#cfe0ff;border:1px solid var(--line)}
        .pill.get{background:#0e2a1a;color:#6ee7a8;border-color:#144b2c}
        .pill.post{background:#0e1f3a;color:#8fb6ff;border-color:#1c3b6b}
        .pill.put{background:#2a230e;color:#f5cf6e;border-color:#4b3d14}
        .pill.del{background:#2a1010;color:#f59a9a;border-color:#4b1414}
        .pill.role{background:#1a1030;color:#c9a7ff;border-color:#3a2a5b}
        code,pre{font-family:"Cascadia Code",Consolas,Monaco,monospace}
        pre{background:var(--code);border:1px solid var(--line);border-radius:10px;padding:12px 14px;overflow:auto;font-size:12.5px;margin:8px 0}
        pre.sql{color:#bfe3c0}
        pre.php{color:#dfe7ff}
        code.inline{background:var(--chip);padding:1px 6px;border-radius:5px;font-size:12.5px;color:#cfe0ff}
        table{width:100%;border-collapse:collapse;margin:10px 0;font-size:13.5px}
        th,td{border:1px solid var(--line);padding:8px 10px;text-align:left;vertical-align:top}
        th{background:var(--panel2);color:#cfe0ff}
        tr:nth-child(even) td{background:#0d1830}
        details{background:var(--panel);border:1px solid var(--line);border-radius:12px;margin:12px 0;overflow:hidden}
        details>summary{cursor:pointer;padding:14px 16px;font-weight:700;list-style:none;display:flex;align-items:center;gap:10px;user-select:none}
        details>summary::-webkit-details-marker{display:none}
        details>summary::before{content:"▸";color:var(--accent);font-size:14px}
        details[open]>summary::before{content:"▾"}
        details>summary:hover{background:var(--panel2)}
        .body{padding:0 16px 16px}
        .q{border-left:3px solid var(--accent);padding:10px 14px;margin:12px 0;background:var(--panel2);border-radius:0 8px 8px 0}
        .q .qt{font-weight:700;color:#fff}
        .q .probe{color:var(--warn);font-size:13px;margin:4px 0}
        .q .ans{color:var(--text);font-size:14px;margin-top:6px}
        .kv{display:flex;gap:8px;flex-wrap:wrap;margin:6px 0}
        .kv .k{color:var(--muted);min-width:120px;font-size:13px}
        .tag{display:inline-block;background:var(--chip);border:1px solid var(--line);border-radius:6px;padding:1px 7px;margin:2px 3px 2px 0;font-size:12px}
        .good{color:var(--accent2)} .bad{color:var(--danger)} .warnc{color:var(--warn)}
        .callout{border-radius:10px;padding:12px 14px;margin:12px 0;border:1px solid}
        .callout.ok{background:#0c2417;border-color:#1c5636}
        .callout.risk{background:#2a1710;border-color:#5b3416}
        .callout.info{background:#0e1c38;border-color:#22406f}
        .steps{counter-reset:s;list-style:none;padding:0;margin:10px 0}
        .steps li{counter-increment:s;position:relative;padding:8px 8px 8px 40px;margin:6px 0;background:var(--panel2);border:1px solid var(--line);border-radius:10px}
        .steps li::before{content:counter(s);position:absolute;left:10px;top:8px;width:22px;height:22px;background:var(--accent);color:#fff;border-radius:50%;display:grid;place-items:center;font-size:12px;font-weight:700}
        .erd{font-size:12.5px;overflow:auto}
        .footer{color:var(--muted);font-size:13px;border-top:1px solid var(--line);margin-top:30px;padding-top:16px}
        .toc a{display:inline-block;margin:2px 8px 2px 0;font-size:13px}
    </style>
</head>
<body>
@verbatim
<header class="top">
    <div class="wrap">
        <h1>AutoCareerBridge — Tài liệu hệ thống</h1>
        <p>Nền tảng kết nối <b>Trường Đại học — Doanh nghiệp — Sinh viên</b> · Laravel · Tài liệu chuẩn bị bảo vệ đồ án (demo &amp; phản biện hội đồng)</p>
    </div>
</header>

<nav class="tabs wrap-nav">
    <div class="wrap" style="display:flex;flex-wrap:wrap;gap:4px;padding:0">
        <button data-tab="overview" class="active">🧭 Tổng quan &amp; Luồng demo</button>
        <button data-tab="erd">🗄️ Cơ sở dữ liệu (ERD)</button>
        <button data-tab="queries">🔎 Truy vấn theo màn hình</button>
        <button data-tab="qa">🎓 Hỏi &amp; Đáp Hội đồng</button>
        <button data-tab="tech">⚙️ Điểm kỹ thuật &amp; Rủi ro</button>
    </div>
</nav>

<main class="wrap">

<!-- ============ TAB 1: OVERVIEW ============ -->
<section class="tab active" id="tab-overview">
    <h2>1. Hệ thống làm gì?</h2>
    <p>AutoCareerBridge là sàn kết nối tuyển dụng thực tập/việc làm giữa 3 nhóm đối tượng, có <b>AI chấm điểm hồ sơ (ATS)</b> và cơ chế <b>hợp tác Trường–Doanh nghiệp</b>.</p>

    <div class="grid cols-3">
        <div class="card"><h4>Sinh viên</h4>Tạo hồ sơ/CV, tìm việc theo ngành, nộp đơn (có hạn mức), nhận kết quả + email.</div>
        <div class="card"><h4>Doanh nghiệp</h4>Đăng tin (chờ Admin duyệt), xem danh sách ứng viên <b>xếp hạng theo điểm AI</b>, chấp nhận/từ chối, báo cáo.</div>
        <div class="card"><h4>Trường / Admin</h4>Trường quản lý sinh viên, ngành, workshop, hợp tác. Admin duyệt tài khoản &amp; tin tuyển dụng, quản trị danh mục.</div>
    </div>

    <h3>6 vai trò (2 chủ + 2 nhân viên uỷ quyền + SV + Admin)</h3>
    <table>
        <tr><th>Vai trò</th><th>Loại</th><th>Đặc điểm</th></tr>
        <tr><td><span class="pill role">admin</span></td><td>Quản trị</td><td>Duyệt tài khoản, duyệt tin, quản lý danh mục ngành/lĩnh vực.</td></tr>
        <tr><td><span class="pill role">company</span></td><td>Chủ DN</td><td>Sở hữu hồ sơ <code class="inline">Company</code>.</td></tr>
        <tr><td><span class="pill role">hiring</span></td><td>NV tuyển dụng</td><td>Do DN tạo, gắn với <code class="inline">company_id</code>. Thao tác thay mặt DN.</td></tr>
        <tr><td><span class="pill role">university</span></td><td>Chủ trường</td><td>Sở hữu hồ sơ <code class="inline">University</code>.</td></tr>
        <tr><td><span class="pill role">academic_affairs</span></td><td>NV phòng đào tạo</td><td>Do trường tạo, gắn <code class="inline">university_id</code>.</td></tr>
        <tr><td><span class="pill role">student</span></td><td>Người dùng cuối</td><td>Sở hữu hồ sơ <code class="inline">Student</code>.</td></tr>
    </table>
    <div class="callout info">
        <b>Điểm khéo:</b> Model <code class="inline">User</code> có accessor <code class="inline">active_company</code> / <code class="inline">active_university</code>: nhân viên <code class="inline">hiring</code> tự động trỏ về DN chủ quản, <code class="inline">academic_affairs</code> trỏ về trường. Nhờ vậy mọi controller chỉ cần gọi <code class="inline">auth()->user()->active_company</code> mà không cần phân biệt chủ hay nhân viên.
    </div>

    <h2>2. Luồng xương sống — Kịch bản demo cho hội đồng</h2>
    <div class="callout ok"><b>Câu chốt khi demo:</b> "Tin tuyển dụng phải qua Admin duyệt mới hiển thị; sinh viên nộp CV → AI chấm điểm → nhà tuyển dụng thấy danh sách đã xếp hạng → quyết định được ghi nhật ký + gửi thông báo và email."</div>
    <ol class="steps">
        <li><b>DN đăng tin</b> (<code class="inline">/company/jobs/create</code>). Nội dung soạn CKEditor được <b>lọc HTML phía server</b>. Tin lưu với <code class="inline">is_approved=false</code>.</li>
        <li><b>Admin duyệt tin</b> (<code class="inline">/admin/jobs</code>) → <code class="inline">is_approved=true</code>. Tin mới hiện ở trang <code class="inline">/jobs</code>.</li>
        <li><b>SV nộp CV</b> (<code class="inline">POST /jobs/{id}/apply</code>). Kiểm tra: đã nộp chưa, còn hạn mức không (mặc định 5 hồ sơ pending), có CV không.</li>
        <li><b>AI chấm điểm</b> (<code class="inline">ApplicationService::evaluateWithAI</code>): gửi mô tả tin + nội dung CV → nhận <code class="inline">{score, review}</code> lưu vào đơn.</li>
        <li><b>HR xem danh sách xếp hạng</b> (<code class="inline">/company/applications</code>): mặc định sắp theo điểm AI giảm dần, hồ sơ chưa chấm dồn xuống cuối.</li>
        <li><b>Chấp nhận/Từ chối</b> (<code class="inline">PUT /company/applications/{id}</code>): cập nhật trạng thái + <b>ghi 1 dòng nhật ký</b> <code class="inline">application_decisions</code> trong cùng transaction.</li>
        <li><b>Thông báo + Email</b>: SV nhận thông báo in-app và email kết quả. Lỗi email chỉ ghi log, không làm hỏng nghiệp vụ.</li>
    </ol>

    <h3>Luồng phụ nên demo nếu còn thời gian</h3>
    <div class="grid cols-2">
        <div class="card"><h4>Hợp tác Trường–DN</h4>Hai chiều, ai cũng gửi được. Từ chối rồi phải chờ <b>cooldown 30 ngày</b> mới gửi lại. Hợp tác được duyệt → tin của DN đối tác được gắn nhãn "Đối tác" cho SV của trường đó.</div>
        <div class="card"><h4>Duyệt / từ chối tài khoản</h4>DN &amp; trường đăng ký xong ở trạng thái chờ. Admin duyệt → <code class="inline">is_active=true</code>. Từ chối → giữ bản ghi + <code class="inline">rejected_at=now()</code> (để lại dấu vết, rời khỏi hàng chờ). SV được kích hoạt ngay.</div>
    </div>
</section>

<!-- ============ TAB 2: ERD ============ -->
<section class="tab" id="tab-erd">
    <h2>Sơ đồ quan hệ thực thể (ERD)</h2>
    <p class="muted">20 bảng. Trung tâm là <code class="inline">users</code> (1 dòng / 1 tài khoản, phân biệt bằng cột <code class="inline">role</code>). Mỗi vai trò có 1 bảng hồ sơ 1–1.</p>

    <div class="card erd">
<pre class="php">                         ┌───────────────┐
        ┌───1:1──────────►│    companies  │◄──1:N── hirings ──1:1──┐
        │                 └──────┬────────┘                        │
        │                        │1:N                              │
┌───────┴───────┐         ┌──────▼────────┐   ┌──────────────┐     │
│     users     │──1:1───►│     jobs      │──►│ applications │◄─┐  │
│  (role, ...)  │  (via   │ is_approved   │1:N│ status,ai_.. │  │  │
│  SoftDeletes  │ company)└──────┬────────┘   └──────┬───────┘  │  │
└───┬───┬───┬───┘                │major_id           │1:N       │  │
    │   │   │                    ▼                    ▼          │  │
    │   │   │              ┌──────────┐      ┌──────────────────┐│  │
    │   │   └──1:1────────►│ students │─1:N─►│application_       ││  │
    │   │   (via student)  │ major_id │ resumes  decisions      ││  │
    │   │                  │ univ_id  │      │ (audit, append)  ││  │
    │   │                  └────┬─────┘      └──────────────────┘│  │
    │   │                       │N:M (skill_student)             │  │
    │   └──1:1─► universities ◄─┘                                │  │
    │   (via university/academic_affairs)                        │  │
    │           │  │  │                                          │  │
    │           │  │  └─1:N─► workshops (company_id NULLABLE) ────┘  │
    │           │  └────1:N─► collaborations (company_id) ──────────┘
    │           └───N:M────► majors ──N:1──► fields
    └──1:N─► notifications

  Bảng nối (pivot, không có model riêng):
   • skill_student(skill_id, student_id)     • major_university(major_id, university_id)
</pre>
    </div>

    <h3>Các bảng &amp; cột chính</h3>
    <table>
        <tr><th>Bảng</th><th>Cột đáng chú ý</th><th>Ghi chú</th></tr>
        <tr><td><b>users</b></td><td>name, email <span class="tag">unique</span>, password <span class="tag">hashed</span>, role, is_active, rejected_at, avatar, deleted_at</td><td>SoftDeletes. <code class="inline">rejected_at</code> phân biệt "chưa duyệt" vs "đã bị từ chối".</td></tr>
        <tr><td><b>companies / universities</b></td><td>user_id (FK), *_name, website, description, address</td><td>Hồ sơ 1–1 với user.</td></tr>
        <tr><td><b>students</b></td><td>user_id, university_id <span class="tag">nullable</span>, major_id <span class="tag">nullable</span>, student_code <span class="tag">nullable</span>, gpa</td><td>Cho phép hoàn thiện hồ sơ dần.</td></tr>
        <tr><td><b>hirings / academic_affairs</b></td><td>user_id, company_id / university_id, position / department</td><td>Nhân viên uỷ quyền.</td></tr>
        <tr><td><b>jobs</b></td><td>company_id, major_id, title, description, requirements, is_approved <span class="tag">bool</span>, deleted_at</td><td>SoftDeletes. <code class="inline">is_approved</code> quyết định hiển thị.</td></tr>
        <tr><td><b>applications</b></td><td>job_id, student_id, status, ai_score <span class="tag">nullable</span>, ai_review, hr_feedback</td><td><span class="tag">UNIQUE(job_id, student_id)</span> — chống nộp trùng ở tầng DB.</td></tr>
        <tr><td><b>application_decisions</b></td><td>application_id, user_id <span class="tag">nullOnDelete</span>, decided_by_role, decided_by_name, previous_status, status, hr_feedback</td><td>Nhật ký <b>chỉ ghi thêm</b>. Snapshot vai trò/tên tại thời điểm quyết định.</td></tr>
        <tr><td><b>resumes</b></td><td>student_id, file_path, original_name, content, is_default <span class="tag">bool</span></td><td><code class="inline">content</code> = text CV trích xuất để gửi AI.</td></tr>
        <tr><td><b>collaborations</b></td><td>company_id, university_id, status, initiated_by</td><td>Hợp tác 2 chiều.</td></tr>
        <tr><td><b>workshops</b></td><td>company_id <span class="tag">nullable</span>, university_id, title, description, date, scheduled_at</td><td>Trường có thể tổ chức không cần DN.</td></tr>
        <tr><td><b>notifications</b></td><td>user_id, title, message, type, url, is_read <span class="tag">bool</span></td><td>Thông báo in-app.</td></tr>
        <tr><td><b>fields / majors / skills</b></td><td>majors.field_id; pivot major_university, skill_student</td><td>Danh mục do Admin quản lý.</td></tr>
    </table>
    <p class="muted">FK đều <code class="inline">cascadeOnDelete</code>, ngoại trừ <code class="inline">application_decisions.user_id</code> (<code class="inline">nullOnDelete</code> để giữ nhật ký). SoftDeletes chỉ ở <b>users</b> và <b>jobs</b>.</p>
</section>

<!-- ============ TAB 3: QUERIES ============ -->
<section class="tab" id="tab-queries">
    <h2>Toàn bộ truy vấn — theo màn hình</h2>
    <p class="muted">Mỗi mục: <b>Màn hình</b> · <b>Route</b> · <b>Mục đích</b> · <b>Eloquent (code thật)</b> · <b>SQL tương đương</b>. Nhấn để mở rộng. Đây là phần để nhớ khi hội đồng yêu cầu "viết truy vấn cho chức năng X".</p>

    <div class="toc card tight">
        <b>Nhảy nhanh:</b>
        <a href="#g-admin">Admin</a> ·
        <a href="#g-company">Doanh nghiệp</a> ·
        <a href="#g-university">Trường</a> ·
        <a href="#g-student">Sinh viên</a> ·
        <a href="#g-public">Trang công khai</a> ·
        <a href="#g-service">Service / nghiệp vụ</a>
    </div>

    <!-- ADMIN -->
    <h3 id="g-admin">🔴 Nhóm Admin</h3>

    <details open>
        <summary>Dashboard Admin — 8 số liệu + danh sách gần đây</summary>
        <div class="body">
            <div class="kv"><span class="k">Màn hình</span> Bảng điều khiển Admin</div>
            <div class="kv"><span class="k">Route</span> <span class="pill get">GET</span> <code class="inline">/admin</code> · <code class="inline">AdminDashboardController@index</code></div>
            <div class="kv"><span class="k">Mục đích</span> Đếm tổng quan toàn hệ thống + 5 tin &amp; 5 người dùng mới nhất.</div>
            <h4>Eloquent</h4>
<pre class="php">$stats = [
  'companies'    => User::where('role','company')->count(),
  'universities' => User::where('role','university')->count(),
  'students'     => User::where('role','student')->count(),
  'jobs_pending' => Job::where('is_approved',false)->count(),
  'jobs_approved'=> Job::where('is_approved',true)->count(),
  'applications' => Application::count(),
  'collaborations'=> Collaboration::where('status','approved')->count(),
  'pending_accounts'=> User::where('is_active',false)->count(),
];
$recentJobs  = Job::with('company')->latest()->take(5)->get();
$recentUsers = User::whereNot('role','admin')->latest()->take(5)->get();</pre>
            <h4>SQL tương đương</h4>
<pre class="sql">SELECT COUNT(*) FROM users WHERE role='company' AND deleted_at IS NULL;
SELECT COUNT(*) FROM jobs  WHERE is_approved=0 AND deleted_at IS NULL;
SELECT COUNT(*) FROM applications;
SELECT COUNT(*) FROM collaborations WHERE status='approved';
-- 5 tin mới nhất (kèm công ty)
SELECT * FROM jobs WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT 5;
SELECT * FROM users WHERE role <> 'admin' AND deleted_at IS NULL ORDER BY created_at DESC LIMIT 5;</pre>
        </div>
    </details>

    <details>
        <summary>Duyệt tài khoản — hàng chờ + toàn bộ người dùng</summary>
        <div class="body">
            <div class="kv"><span class="k">Màn hình</span> Quản lý người dùng</div>
            <div class="kv"><span class="k">Route</span> <span class="pill get">GET</span> <code class="inline">/admin/users</code> · <code class="inline">AdminUserController@index</code></div>
            <div class="kv"><span class="k">Mục đích</span> Hàng chờ = chưa kích hoạt + chưa bị từ chối + là DN/trường. Hai danh sách phân trang độc lập.</div>
            <h4>Eloquent</h4>
<pre class="php">$pendingUsers = User::where('is_active',false)
    ->whereNull('rejected_at')
    ->whereIn('role',['company','university'])
    ->with('company','university')
    ->latest()->orderByDesc('id')
    ->paginate(10, ['*'], 'pending_page');

$allUsers = User::whereNot('role','admin')
    ->with('company','university')
    ->latest()->orderByDesc('id')
    ->paginate(15);</pre>
            <h4>SQL tương đương (hàng chờ)</h4>
<pre class="sql">SELECT * FROM users
WHERE is_active = 0 AND rejected_at IS NULL
  AND role IN ('company','university') AND deleted_at IS NULL
ORDER BY created_at DESC, id DESC
LIMIT 10 OFFSET 0;</pre>
            <div class="callout info"><code class="inline">orderByDesc('id')</code> phụ thêm vì seeder tạo nhiều tài khoản trong cùng 1 giây → chỉ <code class="inline">latest()</code> thì thứ tự không ổn định, dòng nhảy giữa các trang.</div>
        </div>
    </details>

    <details>
        <summary>Duyệt tin + Duyệt/Từ chối tài khoản (ghi)</summary>
        <div class="body">
            <div class="kv"><span class="k">Route</span> <span class="pill post">POST</span> <code class="inline">/admin/jobs/{job}/approve</code>, <code class="inline">/admin/users/{user}/approve|reject</code></div>
            <h4>Eloquent + SQL</h4>
<pre class="php">// Duyệt tin
$job->update(['is_approved' => true]);
// Duyệt tài khoản (xoá cả dấu từ chối cũ → có thể đảo ngược)
$user->update(['is_active' => true,  'rejected_at' => null]);
// Từ chối (GIỮ bản ghi, đánh dấu thời điểm)
$user->update(['is_active' => false, 'rejected_at' => now()]);</pre>
<pre class="sql">UPDATE jobs  SET is_approved=1, updated_at=NOW() WHERE id=?;
UPDATE users SET is_active=1, rejected_at=NULL, updated_at=NOW() WHERE id=?;
UPDATE users SET is_active=0, rejected_at=NOW(), updated_at=NOW() WHERE id=?;</pre>
            <div class="kv"><span class="k">Danh sách chờ duyệt tin</span> <code class="inline">Job::where('is_approved',false)->with('company','major')->latest()->paginate(10)</code></div>
        </div>
    </details>

    <details>
        <summary>Quản lý danh mục (Field / Major)</summary>
        <div class="body">
            <div class="kv"><span class="k">Route</span> <code class="inline">/admin/catalog</code> (+ store/destroy)</div>
<pre class="php">$fields = Field::withCount('majors')->get();     // mỗi lĩnh vực + số ngành
$majors = Major::with('field')->get();           // mỗi ngành + lĩnh vực cha
Field::create(['name'=>...]);  $field->delete();
Major::create(['name'=>..., 'field_id'=>...]);  $major->delete();</pre>
<pre class="sql">SELECT fields.*, (SELECT COUNT(*) FROM majors WHERE majors.field_id=fields.id) AS majors_count FROM fields;
SELECT majors.* FROM majors;  -- kèm eager load fields theo field_id</pre>
        </div>
    </details>

    <!-- COMPANY -->
    <h3 id="g-company">🔵 Nhóm Doanh nghiệp</h3>

    <details>
        <summary>⭐ Danh sách ứng viên — xếp hạng theo điểm AI + thống kê</summary>
        <div class="body">
            <div class="kv"><span class="k">Màn hình</span> Ứng viên (recruiter)</div>
            <div class="kv"><span class="k">Route</span> <span class="pill get">GET</span> <code class="inline">/company/applications</code> · <code class="inline">CompanyApplicationController@index</code></div>
            <div class="kv"><span class="k">Mục đích</span> Mọi đơn vào tin của DN này; sắp theo điểm AI (null xuống cuối); thống kê đếm trên TOÀN BỘ dữ liệu.</div>
            <h4>Eloquent</h4>
<pre class="php">$baseQuery = fn() => Application::whereHas('job', fn($q) =>
    $q->where('company_id', $company->id));

$applications = $baseQuery()
    ->with('student.user','student.university','job')
    ->when($sort==='ai_score', fn($q) =>
        $q->orderByRaw('ai_score IS NULL, ai_score DESC'))
    ->latest()->orderByDesc('id')
    ->paginate(10)->withQueryString();

$countsByStatus = $baseQuery()
    ->selectRaw('status, COUNT(*) as total')
    ->groupBy('status')->pluck('total','status');</pre>
            <h4>SQL tương đương</h4>
<pre class="sql">SELECT applications.* FROM applications
WHERE EXISTS (SELECT 1 FROM jobs
              WHERE jobs.id = applications.job_id
                AND jobs.company_id = ? AND jobs.deleted_at IS NULL)
ORDER BY (ai_score IS NULL), ai_score DESC, created_at DESC, id DESC
LIMIT 10 OFFSET 0;

-- Thống kê theo trạng thái (trên toàn bộ, không chỉ 1 trang)
SELECT status, COUNT(*) AS total FROM applications
WHERE EXISTS (SELECT 1 FROM jobs
              WHERE jobs.id = applications.job_id AND jobs.company_id = ?)
GROUP BY status;</pre>
            <div class="callout ok"><b>3 chi tiết dễ được hỏi:</b> ① <code class="inline">ai_score IS NULL, ai_score DESC</code> → hồ sơ chưa chấm không bị hiểu nhầm là điểm 0. ② <code class="inline">orderByDesc('id')</code> → phân trang ổn định khi trùng <code class="inline">created_at</code>. ③ Thống kê dùng <code class="inline">GROUP BY</code> riêng vì <code class="inline">count()</code> trên paginator chỉ đếm 1 trang.</div>
        </div>
    </details>

    <details>
        <summary>Chi tiết ứng viên + Chấp nhận/Từ chối (transaction + nhật ký)</summary>
        <div class="body">
            <div class="kv"><span class="k">Route</span> <span class="pill get">GET</span> <code class="inline">/company/applications/{id}</code> · <span class="pill put">PUT</span> cập nhật</div>
            <h4>Xem chi tiết</h4>
<pre class="php">$application->load('student.user','student.university','student.major','job','decisions.user');
$resume = Resume::where('student_id',$application->student_id)->where('is_default',true)->first()
        ?? Resume::where('student_id',$application->student_id)->latest()->first();</pre>
            <h4>Cập nhật — cùng transaction để không lệch trạng thái</h4>
<pre class="php">DB::transaction(function() use (...) {
    $application->update(['status'=>$req->status, 'hr_feedback'=>$req->hr_feedback]);
    $application->decisions()->create([
        'user_id'=>$actor->id, 'decided_by_role'=>$actor->role,
        'decided_by_name'=>$actor->name, 'previous_status'=>$previousStatus,
        'status'=>$req->status, 'hr_feedback'=>$req->hr_feedback,
    ]);
});</pre>
<pre class="sql">START TRANSACTION;
UPDATE applications SET status=?, hr_feedback=?, updated_at=NOW() WHERE id=?;
INSERT INTO application_decisions
  (application_id,user_id,decided_by_role,decided_by_name,previous_status,status,hr_feedback,created_at,updated_at)
  VALUES (?,?,?,?,?,?,?,NOW(),NOW());
COMMIT;</pre>
        </div>
    </details>

    <details>
        <summary>Báo cáo DN — trạng thái, xu hướng 30 ngày, top tin</summary>
        <div class="body">
            <div class="kv"><span class="k">Route</span> <span class="pill get">GET</span> <code class="inline">/company/reports</code> · <code class="inline">CompanyReportController@index</code></div>
            <h4>1) Phân bố trạng thái</h4>
<pre class="php">Application::whereHas('job', fn($q)=>$q->where('company_id',$company->id))
  ->select('status', DB::raw('count(*) as total'))
  ->groupBy('status')->pluck('total','status');</pre>
            <h4>2) Xu hướng nộp 30 ngày</h4>
<pre class="php">Application::whereHas('job', fn($q)=>$q->where('company_id',$company->id))
  ->where('created_at','>=',$thirtyDaysAgo)
  ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
  ->groupBy('date')->orderBy('date')->get()->keyBy('date');</pre>
            <h4>3) Top 5 tin nhiều đơn nhất</h4>
<pre class="php">Job::where('company_id',$company->id)
  ->withCount('applications')
  ->orderBy('applications_count','desc')->take(5)->get();</pre>
            <h4>SQL tương đương</h4>
<pre class="sql">-- 2) Xu hướng
SELECT DATE(created_at) AS date, COUNT(*) AS total FROM applications
WHERE EXISTS (SELECT 1 FROM jobs WHERE jobs.id=applications.job_id AND jobs.company_id=?)
  AND created_at >= ?
GROUP BY date ORDER BY date;
-- 3) Top tin
SELECT jobs.*,
  (SELECT COUNT(*) FROM applications WHERE applications.job_id=jobs.id) AS applications_count
FROM jobs WHERE company_id=? AND deleted_at IS NULL
ORDER BY applications_count DESC LIMIT 5;</pre>
        </div>
    </details>

    <details>
        <summary>Quản lý tin tuyển dụng &amp; nhân viên tuyển dụng</summary>
        <div class="body">
            <div class="kv"><span class="k">Route</span> <code class="inline">resource /company/jobs</code>, <code class="inline">/company/hirings</code></div>
<pre class="php">// Danh sách tin của DN
Job::where('company_id',$company->id)->with('major')->latest()->paginate(10);
// Tạo tin (chờ duyệt) — sau khi lọc HTML
Job::create([... 'is_approved'=>false]);
// Sửa tin → reset duyệt lại
$job->update([... 'is_approved'=>false]);
// Nhân viên tuyển dụng
Hiring::where('company_id',$company->id)->with('user')->latest()->paginate(10);
User::create([... 'role'=>'hiring', 'is_active'=>true]);  // + Hiring::create(...)</pre>
<pre class="sql">SELECT * FROM jobs WHERE company_id=? AND deleted_at IS NULL ORDER BY created_at DESC LIMIT 10 OFFSET 0;
UPDATE jobs SET is_approved=0, ... WHERE id=?;  -- sửa tin buộc duyệt lại</pre>
        </div>
    </details>

    <!-- UNIVERSITY -->
    <h3 id="g-university">🟢 Nhóm Trường</h3>

    <details>
        <summary>Báo cáo Trường — SV theo ngành, phân bố trạng thái đơn</summary>
        <div class="body">
            <div class="kv"><span class="k">Route</span> <span class="pill get">GET</span> <code class="inline">/university/reports</code> · <code class="inline">UniversityReportController@index</code></div>
<pre class="php">$totalStudents = Student::where('university_id',$uid)->count();
$totalApplications = Application::whereHas('student',
    fn($q)=>$q->where('university_id',$uid))->count();
$totalAccepted = Application::whereHas('student',
    fn($q)=>$q->where('university_id',$uid))->where('status','accepted')->count();

// SV theo ngành
Student::where('university_id',$uid)->whereNotNull('major_id')
  ->selectRaw('major_id, count(*) as total')->with('major')
  ->groupBy('major_id')->get();

// Phân bố trạng thái đơn của SV trường
Application::whereHas('student', fn($q)=>$q->where('university_id',$uid))
  ->selectRaw('status, count(*) as total')->groupBy('status')->get();</pre>
<pre class="sql">SELECT major_id, COUNT(*) AS total FROM students
WHERE university_id=? AND major_id IS NOT NULL GROUP BY major_id;

SELECT status, COUNT(*) AS total FROM applications
WHERE EXISTS (SELECT 1 FROM students
              WHERE students.id=applications.student_id AND students.university_id=?)
GROUP BY status;</pre>
        </div>
    </details>

    <details>
        <summary>Danh sách sinh viên (đếm CV + số đơn) · Ngành · Workshop · Nhân viên đào tạo</summary>
        <div class="body">
            <h4>Roster SV — withCount</h4>
<pre class="php">Student::where('university_id',$university->id)
  ->with(['user','major'])
  ->withCount(['resumes','applications'])
  ->latest()->paginate(15);</pre>
<pre class="sql">SELECT students.*,
  (SELECT COUNT(*) FROM resumes WHERE resumes.student_id=students.id) AS resumes_count,
  (SELECT COUNT(*) FROM applications WHERE applications.student_id=students.id) AS applications_count
FROM students WHERE university_id=? ORDER BY created_at DESC LIMIT 15 OFFSET 0;</pre>
            <h4>Ngành đào tạo (N–N)</h4>
<pre class="php">$allMajors = Major::all();
$universityMajors = $university->majors->pluck('id')->toArray();
$university->majors()->sync($majors);   // đồng bộ pivot major_university</pre>
            <h4>Workshop &amp; Nhân viên đào tạo</h4>
<pre class="php">Workshop::where('university_id',$university->id)->latest()->get();
AcademicAffair::where('university_id',$university->id)->with('user')->latest()->paginate(10);</pre>
        </div>
    </details>

    <!-- STUDENT -->
    <h3 id="g-student">🟣 Nhóm Sinh viên</h3>

    <details>
        <summary>Dashboard SV — đơn của tôi, hạn mức, việc gợi ý theo ngành</summary>
        <div class="body">
            <div class="kv"><span class="k">Route</span> <span class="pill get">GET</span> <code class="inline">/dashboard</code> (nhánh student) · <code class="inline">HomeController@dashboard</code></div>
<pre class="php">$applications = Application::where('student_id',$student->id)
    ->with('job.company')->latest()->get();
$stats = [
  'total_apps'=> $applications->count(),
  'pending'   => $applications->where('status','pending')->count(),   // đếm trên collection
  'accepted'  => $applications->where('status','accepted')->count(),
  'resumes'   => Resume::where('student_id',$student->id)->count(),
  'pending_limit'=> app(ApplicationService::class)->pendingLimit(),   // config, không query
];
// Việc gợi ý = cùng ngành + đã duyệt
$suggested = Job::where('major_id',$student->major_id)
    ->where('is_approved',true)->with('company')->latest()->take(5)->get();</pre>
<pre class="sql">SELECT * FROM applications WHERE student_id=? ORDER BY created_at DESC;   -- kèm eager job.company
SELECT * FROM jobs WHERE major_id=? AND is_approved=1 AND deleted_at IS NULL
ORDER BY created_at DESC LIMIT 5;</pre>
            <div class="callout info">Điểm hay: <code class="inline">total/pending/accepted</code> đếm bằng collection đã nạp sẵn — chỉ 1 truy vấn thay vì nhiều <code class="inline">count()</code>.</div>
        </div>
    </details>

    <details>
        <summary>Quản lý CV — mặc định &amp; đặt mặc định</summary>
        <div class="body">
            <div class="kv"><span class="k">Route</span> <code class="inline">/student/resumes</code> (+ setDefault)</div>
<pre class="php">Resume::where('student_id',$student->id)->orderByDesc('is_default')->latest()->get();
// Đặt CV mặc định: gỡ hết default rồi bật 1 cái
Resume::where('student_id',$resume->student_id)->update(['is_default'=>false]);
$resume->update(['is_default'=>true]);</pre>
<pre class="sql">SELECT * FROM resumes WHERE student_id=? ORDER BY is_default DESC, created_at DESC;
UPDATE resumes SET is_default=0 WHERE student_id=?;   -- gỡ hết
UPDATE resumes SET is_default=1 WHERE id=?;           -- bật 1</pre>
        </div>
    </details>

    <!-- PUBLIC -->
    <h3 id="g-public">⚪ Trang công khai</h3>

    <details>
        <summary>Danh sách việc làm — tìm kiếm + lọc ngành + ưu tiên "khớp ngành"</summary>
        <div class="body">
            <div class="kv"><span class="k">Route</span> <span class="pill get">GET</span> <code class="inline">/jobs</code> · <code class="inline">JobUIController@index</code></div>
<pre class="php">$query = Job::with('company','major')->where('is_approved',true);
if ($search) $query->where(fn($q)=>
    $q->where('title','like',"%$search%")->orWhere('description','like',"%$search%"));
if ($majorId) $query->where('major_id',$majorId);
$allJobs = $query->get();

// SV đăng nhập: lấy DN đối tác của trường mình
$partnerCompanyIds = Collaboration::where('university_id',$student->university_id)
    ->where('status','approved')->pluck('company_id')->toArray();
// Ưu tiên tin cùng ngành SV lên đầu (xử lý trong PHP)</pre>
<pre class="sql">SELECT * FROM jobs
WHERE is_approved=1 AND deleted_at IS NULL
  AND (title LIKE ? OR description LIKE ?)
  AND major_id = ?;

SELECT company_id FROM collaborations WHERE university_id=? AND status='approved';</pre>
        </div>
    </details>

    <details>
        <summary>Chi tiết việc làm · Trang chủ · Workshop công khai · Tìm DN/Trường</summary>
        <div class="body">
            <h4>Chi tiết việc làm — <code class="inline">GET /jobs/{id}</code></h4>
<pre class="php">$job = Job::with('company')->findOrFail($id);
$application = Application::where('job_id',$id)->where('student_id',$student->id)->first();
$isPartner = Collaboration::where('university_id',$student->university_id)
    ->where('company_id',$job->company_id)->where('status','approved')->exists();</pre>
            <h4>Trang chủ — <code class="inline">GET /</code></h4>
<pre class="php">Job::with('company','major')->where('is_approved',true)->latest()->take(3)->get();
Workshop::with('university')->whereNotNull('date')
    ->where('date','>=',now()->toDateString())->orderBy('date')->take(3)->get();</pre>
            <h4>Workshop công khai — <code class="inline">GET /workshops</code></h4>
<pre class="php">Workshop::with('university')
  ->when($search, fn($q)=>$q->where('title','like',"%$search%"))
  ->latest()->paginate(9);</pre>
            <h4>Tìm DN / Trường</h4>
<pre class="php">Company::query()->where('company_name','like',"%$search%")->get();
University::with('majors')->where('university_name','like',"%$search%")->get();</pre>
        </div>
    </details>

    <!-- SERVICE -->
    <h3 id="g-service">🧠 Service / nghiệp vụ lõi</h3>

    <details open>
        <summary>⭐ Nộp đơn an toàn tương tranh — ApplicationService::apply</summary>
        <div class="body">
            <div class="kv"><span class="k">Gọi bởi</span> <code class="inline">POST /jobs/{id}/apply</code></div>
            <div class="kv"><span class="k">Mục đích</span> Chặn nộp trùng &amp; vượt hạn mức kể cả khi 2 request chạy song song.</div>
<pre class="php">DB::transaction(function() use ($student,$job) {
    Student::whereKey($student->id)->lockForUpdate()->first();   // khoá dòng SV
    $already = Application::where('student_id',$student->id)
                          ->where('job_id',$job->id)->exists();
    if ($already) throw new Exception('Đã nộp rồi.');
    if ($this->pendingCount($student) >= $this->pendingLimit())
        throw new Exception('Vượt hạn mức hồ sơ chờ.');
    return Application::create([...'status'=>'pending']);       // UNIQUE là chốt chặn cuối
});
$this->evaluateWithAI($application,$resume);   // gọi AI NGOÀI transaction</pre>
<pre class="sql">START TRANSACTION;
SELECT * FROM students WHERE id=? FOR UPDATE;                    -- lockForUpdate
SELECT EXISTS(SELECT 1 FROM applications WHERE student_id=? AND job_id=?);
SELECT COUNT(*) FROM applications WHERE student_id=? AND status='pending';
INSERT INTO applications (student_id,job_id,status,created_at,updated_at)
       VALUES (?,?, 'pending', NOW(), NOW());                   -- có thể ném lỗi 23000 (UNIQUE)
COMMIT;</pre>
            <div class="callout ok"><b>3 lớp phòng thủ:</b> ① khoá dòng (<code class="inline">FOR UPDATE</code>) → cùng SV xếp hàng tuần tự · ② kiểm tra ở PHP · ③ ràng buộc <code class="inline">UNIQUE(job_id,student_id)</code> ở DB là chốt chặn cuối cho 2 request thực sự song song. Gọi AI đặt ngoài transaction vì là I/O mạng dài, không được giữ khoá DB.</div>
        </div>
    </details>

    <details>
        <summary>AI chấm điểm · Đếm hạn mức · Thông báo · Hợp tác</summary>
        <div class="body">
            <h4>AI &amp; hạn mức</h4>
<pre class="php">// Ghi điểm AI vào đơn
$application->update(['ai_score'=>$result['score'], 'ai_review'=>$result['review']]);
// Nếu lỗi/không có key → để null + ghi chú "xem thủ công" (không bịa điểm)
$application->update(['ai_score'=>null, 'ai_review'=>"Chưa có đánh giá..."]);
// Đếm hồ sơ đang chờ
Application::where('student_id',$student->id)->where('status','pending')->count();</pre>
            <h4>Thông báo (in-app)</h4>
<pre class="php">Notification::create(['user_id'=>$id,'title'=>...,'message'=>...,'type'=>...,'is_read'=>false]);
Notification::where('user_id',$id)->where('is_read',false)->update(['is_read'=>true]); // đọc hết
Notification::where('user_id',$id)->where('is_read',false)->count();                   // badge</pre>
            <h4>Hợp tác — cooldown 30 ngày</h4>
<pre class="php">$existing = Collaboration::where('company_id',$c->id)->where('university_id',$u->id)->first();
// approved → 'đang hợp tác'; pending → 'đã có yêu cầu chờ';
// rejected & chưa đủ 30 ngày → báo còn X ngày; đủ 30 ngày → tái sử dụng bản ghi (về pending)
$existing->update(['status'=>'pending','initiated_by'=>$who]);   // hoặc Collaboration::create(...)</pre>
<pre class="sql">SELECT EXISTS(SELECT 1 FROM collaborations
  WHERE company_id=? AND university_id=? AND status='approved');   -- isCollaborating
SELECT * FROM universities u WHERE EXISTS (SELECT 1 FROM collaborations c
  WHERE c.university_id=u.id AND c.company_id=? AND c.status='approved'); -- các trường đối tác</pre>
        </div>
    </details>

    <div class="callout info">
        <b>Ghi chú kỹ thuật truy vấn:</b> SQL viết tay (<code class="inline">DB::raw / selectRaw / orderByRaw</code>) chỉ xuất hiện ở các màn báo cáo/danh sách (<code class="inline">COUNT(*)</code>, <code class="inline">DATE(created_at)</code>, <code class="inline">ai_score IS NULL</code>). Không có <code class="inline">DB::select()</code> chuỗi thô. Transaction + khoá dòng dùng ở 2 chỗ: <code class="inline">apply()</code> và cập nhật đơn.
    </div>
</section>

<!-- ============ TAB 4: Q&A ============ -->
<section class="tab" id="tab-qa">
    <h2>Hỏi &amp; Đáp Hội đồng — chuẩn bị phản biện</h2>
    <p class="muted">Mỗi câu: <b class="qt">câu hỏi</b> · <b class="probe">ý đồ hội đồng</b> · <b>gợi ý trả lời</b>.</p>

    <h3>A. Kiến trúc &amp; CSDL</h3>
    <div class="q"><div class="qt">Vẽ ERD và giải thích quan hệ User với Company/University/Student?</div>
        <div class="probe">Đang dò: bạn có thực sự hiểu sơ đồ mình vẽ.</div>
        <div class="ans"><code class="inline">User</code> là trung tâm, phân biệt bằng cột <code class="inline">role</code>; mỗi vai trò <code class="inline">hasOne</code> một bảng hồ sơ. Nhân viên uỷ quyền: <code class="inline">hiring→company</code>, <code class="inline">academic_affairs→university</code>, giải quyết bằng accessor <code class="inline">active_company/active_university</code>.</div>
    </div>
    <div class="q"><div class="qt">Vì sao dùng soft delete? Xoá User thì dữ liệu con ra sao?</div>
        <div class="probe">Kiểm tra hiểu biết về cascade &amp; toàn vẹn dữ liệu.</div>
        <div class="ans">SoftDeletes ở <code class="inline">users</code> &amp; <code class="inline">jobs</code> để khôi phục được. FK dùng <code class="inline">cascadeOnDelete</code>; riêng <code class="inline">application_decisions.user_id</code> là <code class="inline">nullOnDelete</code> để nhật ký không mất khi xoá người quyết định.</div>
    </div>
    <div class="q"><div class="qt">Có index nào? Đặt ở đâu, vì sao?</div>
        <div class="probe">Hiểu về tối ưu &amp; ràng buộc dữ liệu.</div>
        <div class="ans">Index UNIQUE <code class="inline">(job_id, student_id)</code> trên <code class="inline">applications</code> (chống nộp trùng), index <code class="inline">(application_id, created_at)</code> trên nhật ký. Thừa nhận thẳng: có thể thêm index cho <code class="inline">jobs.is_approved</code>, <code class="inline">applications.status</code> nếu dữ liệu lớn.</div>
    </div>

    <h3>B. Tính năng AI (hội đồng đào sâu nhất)</h3>
    <div class="q"><div class="qt">AI chấm điểm dựa trên gì? API sập thì sao?</div>
        <div class="probe">Kiểm tra tính bền vững của tích hợp bên ngoài.</div>
        <div class="ans">Gửi mô tả tin (đã bỏ HTML) + nội dung CV → nhận JSON <code class="inline">{score, review}</code>, <code class="inline">temperature=0.3</code>. Lỗi/không có key → <code class="inline">ai_score=null</code> + ghi chú "xem thủ công", <b>không chặn nộp đơn, không bịa điểm</b> (graceful degradation).</div>
    </div>
    <div class="q"><div class="qt">Điểm AI có đáng tin không? Chống "ảo giác"?</div>
        <div class="probe">Câu khó — muốn thấy bạn hiểu giới hạn của AI.</div>
        <div class="ans">Trả lời trung thực: đây là <b>công cụ sàng lọc hỗ trợ</b>, quyết định cuối vẫn của HR. Kiểm soát bằng: nhiệt độ thấp, prompt yêu cầu đối chiếu "yêu cầu bắt buộc", validate JSON, hồ sơ chưa chấm dồn cuối để không đánh đồng với điểm thấp.</div>
    </div>
    <div class="q"><div class="qt">Gọi AI đồng bộ — sinh viên phải chờ bao lâu?</div>
        <div class="probe">Điểm yếu về hiệu năng — họ muốn xem bạn có nhận ra không.</div>
        <div class="ans">Thừa nhận: hiện gọi đồng bộ trong request (comment code đã ghi "in a real app, dispatch a Job"). Hướng cải tiến: đẩy vào <b>Queue/Job</b> chạy nền, giao diện báo "AI đang phân tích".</div>
    </div>

    <h3>C. Nghiệp vụ &amp; Bảo mật</h3>
    <div class="q"><div class="qt">Chặn sinh viên nộp trùng / spam thế nào?</div>
        <div class="ans">3 lớp: khoá dòng SV (<code class="inline">lockForUpdate</code>) + kiểm tra PHP + UNIQUE ở DB; cộng hạn mức tối đa 5 hồ sơ <code class="inline">pending</code> cùng lúc (giải phóng ngay khi HR phản hồi), cấu hình ở <code class="inline">config/recruitment.php</code>.</div>
    </div>
    <div class="q"><div class="qt">Công ty A có xem được đơn của công ty B không?</div>
        <div class="ans">Không. Truy vấn luôn <code class="inline">whereHas('job', company_id)</code>; ở <code class="inline">show/update</code> kiểm tra <code class="inline">$application->job->company_id !== active_company->id → abort(403)</code>.</div>
    </div>
    <div class="q"><div class="qt">Phân quyền chặn ở đâu?</div>
        <div class="ans">Hai lớp: middleware <code class="inline">role:...</code> chặn theo khu vực route; kiểm tra nội bộ controller cho thao tác chỉ dành cho chủ (VD chỉ <code class="inline">company</code> mới thêm được <code class="inline">hiring</code>, <code class="inline">hiring</code> thì không).</div>
    </div>
    <div class="q"><div class="qt">Chống XSS khi tin dùng CKEditor?</div>
        <div class="ans">Lọc HTML <b>phía server</b> bằng HTMLPurifier (<code class="inline">config/purifier.php</code>) trước khi lưu, chỉ cho phép thẻ an toàn; loại bỏ <code class="inline">&lt;script&gt;</code>, <code class="inline">onclick</code>, <code class="inline">&lt;img&gt;</code>… Có test kiểm chứng.</div>
    </div>
    <div class="q"><div class="qt">Route chạy test / mock-login có nguy hiểm không?</div>
        <div class="ans">Được bọc <code class="inline">if(app()->environment('local'))</code> — ngoài local trả 404, có test khẳng định. Login còn <code class="inline">session()->regenerate()</code> chống session fixation; tài khoản chưa duyệt bị đăng xuất ngay.</div>
    </div>
    <div class="q"><div class="qt">Nhật ký quyết định để làm gì?</div>
        <div class="ans">Bảng <code class="inline">applications</code> chỉ giữ trạng thái mới nhất. Khi chủ DN chấp nhận rồi nhân viên lại từ chối, cần biết ai đổi gì → <code class="inline">application_decisions</code> chỉ-ghi-thêm, lưu <code class="inline">previous_status</code> + snapshot người quyết định, ghi cùng transaction với cập nhật.</div>
    </div>

    <h3>D. Câu "bẫy" thường gặp</h3>
    <div class="grid cols-2">
        <div class="card"><b>"Thống kê của bạn đúng chưa?"</b><br><span class="muted">→ Đếm bằng <code class="inline">GROUP BY</code> trên toàn bộ dữ liệu, không phải <code class="inline">count()</code> trên 1 trang phân trang. Có test 12 bản ghi qua nhiều trang để chứng minh.</span></div>
        <div class="card"><b>"Phân trang có bị nhảy dòng không?"</b><br><span class="muted">→ Không, vì có tiêu chí phụ <code class="inline">orderByDesc('id')</code> khi <code class="inline">created_at</code> trùng.</span></div>
        <div class="card"><b>"Nếu mất mạng khi đang duyệt hồ sơ?"</b><br><span class="muted">→ Cập nhật trạng thái + ghi nhật ký nằm trong 1 transaction: hoặc cùng thành công, hoặc cùng rollback.</span></div>
        <div class="card"><b>"CV nào được chấm điểm?"</b><br><span class="muted">→ Thành thật: hiện lấy CV mặc định. Hạn chế: đơn chưa lưu <code class="inline">resume_id</code>. Hướng sửa: thêm cột <code class="inline">resume_id</code> vào <code class="inline">applications</code>.</span></div>
    </div>
</section>

<!-- ============ TAB 5: TECH & RISK ============ -->
<section class="tab" id="tab-tech">
    <h2>Điểm kỹ thuật nổi bật (nên chủ động khoe)</h2>
    <table>
        <tr><th>Chủ đề</th><th>Bằng chứng trong code</th><th>Vì sao đáng điểm</th></tr>
        <tr><td>Chống tương tranh khi nộp đơn</td><td><code class="inline">DB::transaction + lockForUpdate + UNIQUE(job_id,student_id)</code></td><td>3 lớp phòng thủ, hiểu về race condition &amp; lock</td></tr>
        <tr><td>Tách I/O khỏi transaction</td><td>Gọi AI đặt ngoài transaction</td><td>Không giữ khoá DB khi chờ mạng</td></tr>
        <tr><td>Xếp hạng có chủ đích</td><td><code class="inline">orderByRaw('ai_score IS NULL, ai_score DESC')</code> + phụ <code class="inline">id</code></td><td>Null xuống cuối; phân trang ổn định</td></tr>
        <tr><td>Nhật ký bất biến</td><td><code class="inline">application_decisions</code> (append-only, snapshot)</td><td>Truy vết trách nhiệm — chuẩn nghiệp vụ thật</td></tr>
        <tr><td>Thống kê không đếm nhầm</td><td><code class="inline">GROUP BY</code> thay vì <code class="inline">count()</code> trên paginator</td><td>Tránh bẫy kinh điển</td></tr>
        <tr><td>Suy giảm mềm (AI/Email)</td><td>Lỗi AI → null + ghi chú; lỗi mail → chỉ log</td><td>Dịch vụ ngoài không phá nghiệp vụ lõi</td></tr>
        <tr><td>Hardening bảo mật</td><td>Route nguy hiểm gate local; HTMLPurifier; session regenerate</td><td>Chống DoS/XSS/session fixation, có test</td></tr>
        <tr><td>Cấu hình hoá nghiệp vụ</td><td>Hạn mức ở <code class="inline">config/recruitment.php</code></td><td>Đổi được không sửa mã, 1 nguồn tham chiếu</td></tr>
    </table>

    <h2>Rủi ro / điểm yếu — chuẩn bị trước để không bị bất ngờ</h2>
    <div class="callout risk">
        <b>Trả lời tốt điểm yếu = điểm cộng</b> (cho thấy bạn hiểu giới hạn hệ thống mình). Đừng giấu — hãy nêu kèm hướng khắc phục.
    </div>
    <ol class="steps">
        <li><b>CV dùng để nộp không được lưu.</b> <code class="inline">applications</code> thiếu <code class="inline">resume_id</code>; màn chi tiết tự dò CV mặc định → có thể lệch với CV đã chấm điểm. <span class="good">Hướng sửa:</span> thêm cột <code class="inline">resume_id</code>.</li>
        <li><b>AI &amp; email chạy đồng bộ</b> trong request nộp đơn → có thể chờ vài giây. <span class="good">Hướng sửa:</span> Queue/Job chạy nền.</li>
        <li><b>Trang <code class="inline">/jobs</code> tải TẤT CẢ tin rồi lọc "khớp ngành" bằng PHP, không phân trang.</b> Nhiều tin sẽ chậm. <span class="good">Hướng sửa:</span> phân trang + sắp xếp bằng SQL.</li>
        <li><b>Tìm kiếm dùng <code class="inline">LIKE '%...%'</code></b> — không tận dụng index, không dấu tiếng Việt. <span class="good">Hướng sửa:</span> full-text search.</li>
        <li><b>Thiếu index cho một số cột lọc thường xuyên</b> (<code class="inline">is_approved</code>, <code class="inline">status</code>). <span class="good">Hướng sửa:</span> thêm index khi dữ liệu lớn.</li>
    </ol>

    <h2>Câu chốt tổng kết (đọc khi mở đầu/kết thúc)</h2>
    <div class="callout ok">
        "Điểm xuyên suốt của hệ thống là <b>phòng thủ nhiều lớp có giải thích</b>: tương tranh xử lý ở 3 mức (khoá dòng, kiểm tra ứng dụng, ràng buộc DB); nhật ký quyết định bất biến xuất phát từ nhu cầu truy vết thật; các dịch vụ ngoài (AI, email) suy giảm mềm để không bao giờ phá nghiệp vụ lõi; route nguy hiểm bị chặn theo môi trường; và nội dung người dùng luôn được lọc phía server. Gần như mọi quyết định không hiển nhiên đều được ghi chú trong code và có test kiểm chứng."
    </div>
</section>

</main>

<footer class="footer wrap">
    AutoCareerBridge · Tài liệu hệ thống sinh cho mục đích bảo vệ đồ án · Route <code class="inline">/system</code>
</footer>

<script>
    const buttons = document.querySelectorAll('nav.tabs button');
    const tabs = document.querySelectorAll('section.tab');
    buttons.forEach(b => b.addEventListener('click', () => {
        buttons.forEach(x => x.classList.remove('active'));
        tabs.forEach(x => x.classList.remove('active'));
        b.classList.add('active');
        document.getElementById('tab-' + b.dataset.tab).classList.add('active');
        window.scrollTo({top:0, behavior:'smooth'});
    }));
    // Mở tab theo hash (#queries ...) nếu có
    if (location.hash) {
        const id = location.hash.replace('#','');
        const btn = document.querySelector('nav.tabs button[data-tab="'+id+'"]');
        if (btn) btn.click();
    }
</script>
@endverbatim
</body>
</html>
