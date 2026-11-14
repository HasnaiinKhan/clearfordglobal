<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class InquiryNotification extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public readonly Inquiry $inquiry
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $senderName = $this->sanitizeHeaderValue($this->inquiry->name);
        $senderEmail = $this->sanitizeHeaderValue($this->inquiry->email);

        return new Envelope(
            to: (string) config('mail.inquiry_to'),
            subject: "New project inquiry from {$senderName}",
            replyTo: [
                new \Illuminate\Mail\Mailables\Address($senderEmail, $senderName),
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.inquiry-notification',
            with: [
                'inquiry' => $this->inquiry,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Get the message headers.
     */
    public function headers(): Headers
    {
        return new Headers(
            text: [
                'X-Mailer' => 'Clearford Inquiry System',
            ],
        );
    }

    /**
     * Strip newline characters to prevent header injection.
     */
    private function sanitizeHeaderValue(string $value): string
    {
        return preg_replace('/[\r\n]/', ' ', $value) ?? $value;
    }
}
