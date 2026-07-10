<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BdNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $payload)
    {
    }

    public function build(): self
    {
        $mail = $this
            ->subject($this->payload['subject'] ?? 'Business Diversity')
            ->view('emails.bd-notification', [
                'payload' => $this->payload,
            ]);

        $replyTo = $this->payload['reply_to'] ?? [];
        $replyToEmail = is_array($replyTo) ? ($replyTo['email'] ?? null) : null;

        if ($replyToEmail && filter_var($replyToEmail, FILTER_VALIDATE_EMAIL)) {
            $mail->replyTo($replyToEmail, $replyTo['name'] ?? null);
        }

        return $mail;
    }
}
