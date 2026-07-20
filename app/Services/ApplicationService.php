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
        // Check if already applied
        $existing = Application::where('student_id', $student->id)
                               ->where('job_id', $job->id)
                               ->first();

        if ($existing) {
            throw new \Exception('Student has already applied for this job.');
        }

        $application = Application::create([
            'student_id' => $student->id,
            'job_id' => $job->id,
            'status' => 'pending',
        ]);

        // Trigger AI evaluation asynchronously (in a real app, dispatch a Job here)
        $this->evaluateWithAI($application, $resume);

        return $application;
    }

    /**
     * Evaluate the application using OpenAI API
     */
    public function evaluateWithAI(Application $application, Resume $resume): void
    {
        $job = $application->job;
        
        $prompt = "Bạn là một chuyên gia Tuyển dụng (HR) và Hệ thống ATS (Applicant Tracking System) khắt khe và công tâm.\n\n" .
                  "Nhiệm vụ của bạn là đánh giá mức độ phù hợp của Ứng viên so với công việc dựa trên HỒ SƠ (CV) của họ và YÊU CẦU CÔNG VIỆC.\n\n" .
                  "--- THÔNG TIN CÔNG VIỆC ---\n" .
                  "- Vị trí: {$job->title}\n" .
                  "- Mô tả: {$job->description}\n" .
                  "- Yêu cầu bắt buộc: {$job->requirements}\n\n" .
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
     * Khi không chấm được điểm, để ai_score = null thay vì bịa một con số.
     * HR phải thấy rõ là "chưa có đánh giá", không phải một điểm số trông có vẻ thật.
     */
    private function markEvaluationUnavailable(Application $application, string $reason): void
    {
        $application->update([
            'ai_score' => null,
            'ai_review' => "Chưa có đánh giá tự động. Lý do: {$reason} Vui lòng xem xét hồ sơ thủ công.",
        ]);
    }
}
