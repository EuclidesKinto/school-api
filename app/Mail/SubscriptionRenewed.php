<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Content;

class SubscriptionRenewed extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    private $expiresAt;
    private $planValue;
    private $installmentsNumber;

    public function __construct($expiresAt, $planValue, $installmentsNumber)
    {
        $this->expiresAt = $expiresAt;
        $this->planValue = $planValue;
        $this->installmentsNumber = $installmentsNumber;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->markdown('mails.SubscriptionExpiryCard', [
            'expiresAt' => $this->expiresAt->format('d M Y'),
            'planValue' => number_format($this->planValue / 100, 2),
            'installmentsNumber' => $this->installmentsNumber,
        ])->subject('Hacking Club| Novo pagamento de Assinatura Hacking Club');
    }
}
