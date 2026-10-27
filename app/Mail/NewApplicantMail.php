<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Thư báo cho doanh nghiệp khi có sinh viên nộp hồ sơ mới.
 */
class NewApplicantMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Application $application) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ứng viên mới cho vị trí ' . $this->application->job->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.new-applicant',
            with: [
                'studentName'    => $this->application->student->user->name ?? 'Ứng viên',
                'universityName' => $this->application->student->university->university_name ?? null,
                'jobTitle'       => $this->application->job->title,
                'aiScore'        => $this->application->ai_score,
                'reviewUrl'      => route('company.applications.index'),
            ],
        );
    }
}
