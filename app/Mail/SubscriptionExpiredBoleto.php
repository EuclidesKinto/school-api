<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Content;

class SubscriptionExpiredBoleto extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    private $expiresAt;
    private $boletoUrl;
    private $boletoPdf;
    private $planValue;

    public function __construct($expiresAt, $boletoUrl, $boletoPdf, $planValue)
    {
        $this->expiresAt = $expiresAt;
        $this->boletoUrl = $boletoUrl;
        $this->boletoPdf = $boletoPdf;
        $this->planValue = $planValue;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->markdown('mails.SubscriptionExpiryBoleto', [
            'expiresAt' => $this->expiresAt->format('d M Y'),
            'boletoUrl' => $this->boletoUrl,
            'boletoPdf' => $this->boletoPdf,
            'planValue' => number_format($this->planValue/100, 2),
        ])->subject('Hacking Club | A renovação acontecerá em ' . $this->expiresAt->format('d M Y'));
    }
}
