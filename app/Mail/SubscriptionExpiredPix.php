<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Content;

class SubscriptionExpiredPix extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    private $expiresAt;
    private $pixQrcode;
    private $pixText;
    private $planValue;

    public function __construct($expiresAt, $pixQrcode, $pixText, $planValue)
    {
        $this->expiresAt = $expiresAt;
        $this->pixQrcode = $pixQrcode;
        $this->pixText = $pixText;
        $this->planValue = $planValue;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->markdown('mails.SubscriptionExpiryPix', [
            'expiresAt' => $this->expiresAt->format('d M Y'),
            'pixQrcode' => $this->pixQrcode,
            'pixText' => $this->pixText,
            'planValue' => number_format($this->planValue/100, 2),
        ])->subject('Hacking Club | A renovação acontecerá em ' . $this->expiresAt->format('d M Y'));
    }
}
