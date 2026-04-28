<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MagicLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User   $user,
        public readonly string $magicUrl,
        public int $expiryMinutes = 10,
    ) {
        $this->expiryMinutes = (int) config('magic_link.expiry_minutes', 10);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Secure Login Link — ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.magic-link',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
