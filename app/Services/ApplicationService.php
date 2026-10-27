<?php

namespace App\Services;

use App\Models\Job;
use App\Models\Student;
use App\Models\Resume;
use App\Models\Application;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApplicationService
{
    /**
     * Student applies for a job with a specific resume
     */
    public function apply(Student $student, Job $job, Resume $resume): Application
    {
        // Việc kiểm tra điều kiện và tạo hồ sơ phải nằm trong cùng một giao dịch,
        // nếu không hai request song song đều đọc ra "chưa đạt hạn mức" rồi cùng
        // ghi. Lời gọi dịch vụ AI được đặt NGOÀI giao dịch vì đó là lời gọi mạng
        // kéo dài, không được phép giữ khóa cơ sở dữ liệu trong lúc chờ.
        $application = \Illuminate\Support\Facades\DB::transaction(function () use ($student, $job) {
            // Khóa bản ghi sinh viên để mọi lượt nộp của cùng một sinh viên phải
            // xếp hàng lần lượt.
            Student::whereKey($student->id)->lockForUpdate()->first();

            $alreadyApplied = Application::where('student_id', $student->id)
                                         ->where('job_id', $job->id)
                                         ->exists();

            if ($alreadyApplied) {
                throw new \Exception('Bạn đã nộp đơn vào vị trí này rồi.');
            }

            $limit   = $this->pendingLimit();
            $pending = $this->pendingCount($student);

            if ($pending >= $limit) {
                throw new \Exception(
                    "Bạn đang có {$pending} hồ sơ chờ nhà tuyển dụng phản hồi (tối đa {$limit}). "
                    . 'Hãy chờ có kết quả trước khi nộp thêm hồ sơ mới.'
                );
            }

            try {
                return Application::create([
                    'student_id' => $student->id,
                    'job_id' => $job->id,
                    'status' => 'pending',
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                // Ràng buộc UNIQUE (job_id, student_id) chặn trường hợp hai request
                // song song cùng vượt qua bước kiểm tra ở trên.
                if ($e->getCode() === '23000') {
                    throw new \Exception('Bạn đã nộp đơn vào vị trí này rồi.');
                }
                throw $e;
            }
        });

        // Trigger AI evaluation asynchronously (in a real app, dispatch a Job here)
        $this->evaluateWithAI($application, $resume);

        return $application;
    }

    /** Hạn mức hồ sơ được phép chờ cùng lúc. */
    public function pendingLimit(): int
    {
        return (int) config('recruitment.max_pending_applications');
    }

    /** Số hồ sơ đang chờ nhà tuyển dụng phản hồi của một sinh viên. */
    public function pendingCount(Student $student): int
    {
        return Application::where('student_id', $student->id)
                          ->where('status', 'pending')
                          ->count();
    }

    /**
     * Số lượt nộp còn lại. Dùng cho giao diện để báo trước cho sinh viên, thay vì
     * để họ bấm nộp rồi mới nhận thông báo từ chối.
     */
    public function remainingQuota(Student $student): int
    {
        return max(0, $this->pendingLimit() - $this->pendingCount($student));
    }

    /**
     * Evaluate the application using OpenAI API
     */
    public function evaluateWithAI(Application $application, Resume $resume): void
    {
        $job = $application->job;

        // Mô tả công việc được soạn bằng CKEditor nên chứa thẻ HTML. Bỏ thẻ đi
        // để prompt gửi sang AI là văn bản thuần, không lãng phí token vào markup.
        $jobDescription  = $job->plain_description;
        $jobRequirements = $job->plain_requirements;

        $prompt = "Bạn là một chuyên gia Tuyển dụng (HR) và Hệ thống ATS (Applicant Tracking System) khắt khe và công tâm.\n\n" .
                  "Nhiệm vụ của bạn là đánh giá mức độ phù hợp của Ứng viên so với công việc dựa trên HỒ SƠ (CV) của họ và YÊU CẦU CÔNG VIỆC.\n\n" .
                  "--- THÔNG TIN CÔNG VIỆC ---\n" .
                  "- Vị trí: {$job->title}\n" .
                  "- Mô tả: {$jobDescription}\n" .
                  "- Yêu cầu bắt buộc: {$jobRequirements}\n\n" .
                  "--- HỒ SƠ ỨNG VIÊN (CV) ---\n" .
                  "{$resume->content}\n\n" .
                  "--- HƯỚNG DẪN ĐÁNH GIÁ ---\n" .
                  "1. Đối chiếu sát sao các kỹ năng, kinh nghiệm trong CV với 'Yêu cầu bắt buộc' của công việc.\n" .
                  "2. Chỉ chấm điểm cao (trên 80) nếu ứng viên thực sự có các kỹ năng hoặc kinh nghiệm cốt lõi được yêu cầu.\n" .
                  "3. Nếu CV không liên quan hoặc thiếu quá nhiều kỹ năng quan trọng, hãy chấm điểm thấp (dưới 50).\n\n" .
                  "Vui lòng trả về kết quả định dạng JSON chuẩn (không chứa code block markdown) với đúng 2 key sau:\n" .
                  "- 'score': Một số nguyên từ 0 đến 100 thể hiện phần trăm độ khít (match) giữa CV và Công việc.\n" .
                  "- 'review': Một đoạn nhận xét ngắn gọn, súc tích (tiếng Việt, khoảng 50-100 từ) giải thích lý do cho số điểm này. Nêu rõ ứng viên có điểm mạnh gì và đang thiếu hụt yêu cầu cốt lõi nào.";

        try {
            // Dùng config() thay vì env(): env() trả về null sau `php artisan config:cache`.
            $apiKey = config('services.openai.key');
            $apiBase = config('services.openai.base');

            if (!$apiKey || $apiKey == '') {
                Log::warning('Bỏ qua chấm điểm AI: chưa cấu hình OPENAI_API_KEY.', ['application_id' => $application->id]);
                $this->markEvaluationUnavailable($application, 'Chưa cấu hình API key cho dịch vụ AI.');
                return;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => config('app.url'), // OpenRouter requirement
                'X-Title' => 'Auto Career Bridge', // OpenRouter requirement
            ])->post($apiBase . '/chat/completions', [
                'model' => config('services.openai.model'),
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a strict and objective ATS (Applicant Tracking System).'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.3,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $aiContent = $data['choices'][0]['message']['content'];
                
                $result = json_decode($aiContent, true);
                
                if (json_last_error() === JSON_ERROR_NONE && isset($result['score']) && isset($result['review'])) {
                    $application->update([
                        'ai_score' => $result['score'],
                        'ai_review' => $result['review'],
                    ]);
                } else {
                    Log::error('OpenAI response was not valid JSON.', ['content' => $aiContent]);
                    $this->markEvaluationUnavailable($application, 'Dịch vụ AI trả về dữ liệu không hợp lệ.');
                }
            } else {
                Log::error('OpenAI API request failed.', ['status' => $response->status(), 'body' => $response->body()]);
                $this->markEvaluationUnavailable($application, 'Không gọi được dịch vụ AI.');
            }
        } catch (\Exception $e) {
            Log::error('Error during AI Evaluation: ' . $e->getMessage());
            $this->markEvaluationUnavailable($application, 'Lỗi hệ thống khi chấm điểm.');
        }
    }

    /**
     * Khi không chấm được điểm, để ai_score = null.
     * HR phải thấy rõ là "chưa có đánh giá".
     */
    private function markEvaluationUnavailable(Application $application, string $reason): void
    {
        $application->update([
            'ai_score' => null,
            'ai_review' => "Chưa có đánh giá tự động. Lý do: {$reason} Vui lòng xem xét hồ sơ thủ công.",
        ]);
    }
}
