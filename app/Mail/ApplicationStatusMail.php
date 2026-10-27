<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Thư báo kết quả xét duyệt hồ sơ gửi cho sinh viên.
 */
class ApplicationStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Application $application,
        public string $companyName,
    ) {}

    public function envelope(): Envelope
    {
        $jobTitle = $this->application->job->title;

        return new Envelope(
            subject: $this->application->status === 'accepted'
                ? "Chúc mừng! Hồ sơ ứng tuyển vị trí {$jobTitle} đã được chấp nhận"
                : "Kết quả ứng tuyển vị trí {$jobTitle}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.application-status',
            with: [
                'studentName' => $this->application->student->user->name ?? 'bạn',
                'jobTitle'    => $this->application->job->title,
                'companyName' => $this->companyName,
                'isAccepted'  => $this->application->status === 'accepted',
                'feedback'    => $this->application->hr_feedback,
            ],
        );
    }
}
