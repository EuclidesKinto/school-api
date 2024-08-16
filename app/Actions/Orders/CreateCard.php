<?php

namespace App\Actions\Orders;

use App\Exceptions\Pagarme\CardCreationException;
use App\Exceptions\Pagarme\TokenNotFoundException;
use App\Models\BillingProfile;
use App\Services\Pagarme\V2\Facades\Pagarme as PagarmeV2;
use Lorisleiva\Actions\Action;

class CreateCard extends Action
{
    /**
     * Determine if the user is authorized to make this action.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * Execute the action and return a result.
     *
     * @return mixed
     */
    public function handle(BillingProfile $payer, array $card)
    {
        $customer_id = data_get($payer, 'metadata.pagarme_id');

        $pagarme_card = [
            'token' => $card['id'],
            'billing_address' => $payer->address->only(['line_1', 'line_2', 'state', 'city', 'zip_code', 'country']),
            'options' => ['verify_card' => true]
        ];

        try {

            $card = PagarmeV2::post("customers/{$customer_id}/cards", [
                'json' => $pagarme_card
            ]);

            if ($card->getStatusCode() == 200) {
                $card = json_decode($card->getBody());
                // salva os dados do cartão localmente
                $pm =  $payer->paymentMethods()->create([
                    'user_id' => $payer->user->id,
                    'gateway_id' => $card->id,
                    'type' => $card->type,
                    'gateway' => 'pagarme',
                    'details' => [
                        'holder_name' => $card->holder_name,
                        'exp_year' => $card->exp_year,
                        'exp_month' => $card->exp_month,
                        'status' => $card->status,
                        'brand' => $card->brand,
                        'last_four_digits' => $card->last_four_digits,
                    ]
                ]);
                return $pm;
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            switch ($e->getCode()) {
                case 412:
                    throw new CardCreationException($e->getMessage(), 412);
                    break;
                case 404:
                    throw new TokenNotFoundException($e->getMessage(), 404);
                    break;
                default:
                    throw new CardCreationException($e->getMessage(), 422);
                    break;
            }
        }
    }
}
