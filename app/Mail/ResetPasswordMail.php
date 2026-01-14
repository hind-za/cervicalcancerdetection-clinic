<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $email;
    public $user;

    public function __construct($token, $email, $user)
    {
        $this->token = $token;
        $this->email = $email;
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Réinitialisation de votre mot de passe - CervicalCare AI',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reset-password',
            with: [
                'token' => $this->token,
                'email' => $this->email,
                'user' => $this->user,
                'resetUrl' => url('/reset-password/' . $this->token . '?email=' . urlencode($this->email))
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
