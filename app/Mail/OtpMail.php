<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $otp,
        public readonly string $username,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your OTP Code | ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.otp',
        );
    }
}
