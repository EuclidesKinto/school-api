<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionExpired extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    private $expiresAt;
    private $planValue;

    public function __construct($expiresAt, $planValue)
    {
        $this->expiresAt = $expiresAt;
        
        $this->planValue = $planValue;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->markdown('mails.SubscriptionExpiryWarning', [
            'expiresAt' => $this->expiresAt->format('d M Y'),
            'planValue' => number_format($this->planValue/100, 2),
        ])->subject('Hacking Club | A renovação acontecerá em ' . $this->expiresAt->format('d M Y'));
    }
}
