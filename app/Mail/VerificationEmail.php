<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $userName, public string $otpCode)
    {

    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verification Email',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
