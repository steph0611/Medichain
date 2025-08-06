<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    public $verifyUrl;

    /**
     * Create a new message instance.
     *
     * @param string $verifyUrl
     */
    public function __construct(string $verifyUrl)
    {
        $this->verifyUrl = $verifyUrl;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('Verify Your Email Address')
                    ->view('emails.verify_email')
                    ->with([
                        'verifyUrl' => $this->verifyUrl
                    ]);
    }
}
