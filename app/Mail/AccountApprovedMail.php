<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Thư báo tài khoản Doanh nghiệp / Trường học đã được quản trị viên phê duyệt.
 */
class AccountApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Tài khoản của bạn đã được phê duyệt');
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.account-approved',
            with: [
                'name'     => $this->user->name,
                'roleName' => $this->user->role === 'company' ? 'Doanh nghiệp' : 'Trường đại học',
                'loginUrl' => route('login'),
            ],
        );
    }
}
