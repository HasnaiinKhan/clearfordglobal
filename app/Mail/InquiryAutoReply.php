<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InquiryAutoReply extends Mailable implements ShouldQueue
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
        $recipientName = $this->sanitizeHeaderValue($this->inquiry->name);
        $recipientEmail = $this->sanitizeHeaderValue($this->inquiry->email);

        return new Envelope(
            to: [
                new \Illuminate\Mail\Mailables\Address($recipientEmail, $recipientName),
            ],
            subject: 'We received your inquiry - Clearford Global',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.inquiry-auto-reply',
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
     * Strip newline characters to prevent header injection.
     */
    private function sanitizeHeaderValue(string $value): string
    {
        return preg_replace('/[\r\n]/', ' ', $value) ?? $value;
    }
}
