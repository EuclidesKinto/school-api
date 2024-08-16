<?php

namespace App\Models;

use App\Exceptions\Pagarme\CardVerificationException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Support\Facades\Log;
use App\Services\Pagarme\V2\Facades\Pagarme as PagarmeV2;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Arr;
use stdClass;

class BillingProfile extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'surname',
        'document',
        'document_type',
        'gender',
        'email',
        'birthdate',
        'phones',
        'metadata'
    ];

    protected $casts = [
        'birthdate' => 'datetime:d-m-Y',
        'phones' => 'object',
        'metadata' => AsArrayObject::class
    ];

    // exemplo do campo phones '{"home": {"ddd": "00", "ddi": "+55", "number": "000000000"}, "mobile": {"ddd": "00", "ddi": "+55", "number": "000000000"}}'
    protected $attributes = [
        'phones' => '{"home": {}}',
        'metadata' => '{"default":true}'
    ];

    public function getFullNameAttribute()
    {
        return "{$this->name} {$this->surname}";
    }

    public function getContactPhonesAttribute()
    {
        foreach ($this->phones as $phone) {

            $phones[] = [
                'country_code' => $phone->ddd,
                'area_code' => $phone->ddi,
                'number' => $phone->number
            ];
        }

        $phone_mask = new stdClass();
        $phone_mask->home_phone = $phones[0];
        array_key_exists(1, $phones) ?? $phone_mask->mobile_phone = $phones[1];
        return $phone_mask;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    /**
     * retorna o último método de pagamento registrado pelo usuário
     */
    public function paymentMethod()
    {
        return $this->hasOne(PaymentMethod::class)->latestOfMany();
    }

    /**
     * Retorna todas as cobranças executadas no pedido
     */
    public function charges()
    {
        return $this->hasMany(Charge::class);
    }

    /**
     * Retorna a última cobrança registrada
     */
    public function lastCharge()
    {
        return $this->hasOne(Charge::class)->latest();
    }

    /**
     * Retorna os métodos de pagamento do usuário
     */
    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'payer_id', 'id')->latestOfMany();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'payer_id', 'id');
    }

    public function addresses()
    {
        return $this->hasMany(Address::class, 'billing_profile_id');
    }

    public function address()
    {
        return $this->hasOne(Address::class, 'billing_profile_id')->latestOfMany();
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'billing_profile_id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'billing_profile_id')->latestOfMany();
    }

    /**
     * Registra um novo cartão como método de pagamento do cliente
     */
    public function registerCard($card)
    {
        try {
            $customer_id = $this->metadata['pagarme_id'];
            /**
             * Registra o cartão no perfil do cliente 
             * na pagarme
             */
            $pagarmeCard = [
                'token' => $card['id'],
                'billing_address' => $this->address->only(['line_1', 'line_2', 'state', 'city', 'zip_code', 'country']),
                'options' => ['verify_card' => true]
            ];

            Log::debug("Pagarme Card: ", [$pagarmeCard]);

            try {
                $card = PagarmeV2::post("customers/{$customer_id}/cards", [
                    'json' => $pagarmeCard
                ]);

                if ($card->getStatusCode() == 200) {
                    $card = json_decode($card->getBody());
                    // salva os dados do cartão localmente
                    $card =  $this->paymentMethods()->create([
                        'user_id' => $this->user->id,
                        'gateway_id' => $card->id,
                        'type' => $card->type,
                        'details' => [
                            'holder_name' => $card->holder_name,
                            'exp_year' => $card->exp_year,
                            'exp_month' => $card->exp_month,
                            'status' => $card->status,
                            'brand' => $card->brand,
                            'last_four_digits' => $card->last_four_digits,
                        ]
                    ]);
                    Log::debug('registerCard::Cartão registrado com sucesso - ', ['card' => json_encode($card)]);
                    return $card;
                }
            } catch (ClientException $e) {
                if ($e->getCode() == 412) {
                    Log::error("pagarme::registerCard::Erro ao registrar cartão - ", [$e->getMessage()]);
                    throw new CardVerificationException($e->getMessage());
                }
            }
        } catch (\Exception $th) {
            Log::error('registerCard::Erro ao registrar cartão - ', ['context' => json_encode($th)]);
            return false;
        }
    }

    /**
     * Registra a transação no perfil do usuário
     */
    public function registerTransaction($transaction)
    {
        try {
            $transaction = $this->transactions()->create([
                'user_id' => $this->user->id,
                'amount' => Arr::get($transaction, 'amount'),
                'status' => Arr::get($transaction, 'status'),
                'payment_method_id' => Arr::get($transaction, 'payment_method_id'),
                'gateway' => Arr::get($transaction, 'gateway'),
                'order_id' => Arr::get($transaction, 'order_id'),
                'details' => Arr::get($transaction, 'details'),
            ]);
            Log::debug('registerTransaction::Transação registrada com sucesso - ', ['transaction' => json_encode($transaction)]);
            return $transaction;
        } catch (\Exception $th) {
            Log::error('registerTransaction::Erro ao registrar transação - ', ['context' => json_encode($th)]);
            return false;
        }
    }

    /**
     * Registra a fatura no perfil do usuário
     */
    public function registerInvoice($invoice)
    {

        try {
            $invoice = $this->invoices()->create([
                'user_id' => $this->user->id,
                'status' => Arr::get($invoice, 'status', 'pending'),
                'subtotal' => Arr::get($invoice, 'subtotal'),
                'total' => Arr::get($invoice, 'total'),
                'payment_method_id' => Arr::get($invoice, 'payment_method_id'),
                'order_id' => Arr::get($invoice, 'order_id'),
                'subscription_id' => Arr::get($invoice, 'subscription_id'),
                'metadata' => Arr::get($invoice, 'metadata', []),
                'transaction_id' => Arr::get($invoice, 'transaction_id'),
                'billing_at' => Arr::get($invoice, 'billing_at'), // data da cobrança
                'due_at' => Arr::get($invoice, 'due_at') ? Carbon::createFromTimeString(Arr::get($invoice, 'due_at')) : Carbon::now()->addDays(3), // data de vencimento
                // 'seen_at' => $invoice['seen_at'], // data de visualização
                'total_discount' => Arr::get($invoice, 'total_discount', 0), // soma dos descontos
                'total_increment' => Arr::get($invoice, 'total_increment', 0), // soma dos acréscimos
                'gateway' => Arr::get($invoice, 'gateway', 'pagarme'),
                'gateway_invoice_id' => Arr::get($invoice, 'gateway_invoice_id'),
                'gateway_url' => Arr::get($invoice, 'gateway_url'),
            ]);
            Log::debug('registerInvoice::Fatura registrada com sucesso - ', ['invoice' => json_encode($invoice)]);
            return $invoice;
        } catch (\Exception $th) {
            Log::error('registerInvoice::Erro ao registrar fatura - ', ['context' => json_encode($th)]);
            return false;
        }
    }

    /**
     * Registra o perfil do usuário na pagarme
     */
    public function registerOnPagarme()
    {
        if (empty($this->metadata['pagarme_id'])) {

            try {

                $customer = new \stdclass();
                $customer->code = $this->id;
                $customer->name = $this->full_name;
                $customer->email = $this->email;
                $customer->address = $this->address;

                if (strtolower($this->document_type) == 'cpf') {
                    $customer->type = 'individual';
                } else {
                    $customer->type = 'company';
                }

                $customer->document = $this->document;
                $customer->document_type = $this->document_type;

                /**
                 * Adiciona o telefone do usuário
                 */
                $customer->phones = new stdclass();
                $customer->phones->home_phone = new stdClass();
                $customer->phones->home_phone->country_code = $this->phones->home->ddi;
                $customer->phones->home_phone->area_code = $this->phones->home->ddd;
                $customer->phones->home_phone->number = $this->phones->home->number;

                // make http request to pagarme api and register new customer
                $response = PagarmeV2::post('customers', [
                    'json' => $customer
                ]);

                if ($response->getStatusCode() == 200) {
                    $customer = json_decode($response->getBody());
                    $this->metadata['pagarme_id'] = $customer->id;
                    $this->address->metadata['pagarme_id'] = $customer->address->id;
                    $this->address->save();
                    $this->save();
                    Log::debug('registerOnPagarme::Perfil registrado com sucesso - ', ['customer' => json_encode($customer)]);
                } else {
                    Log::error('registerOnPagarme::Erro ao registrar usuário na pagarme - ', ['context' => json_encode($response)]);
                }
                return true;
            } catch (\Exception $e) {
                Log::error("pagarme::Erro ao cadastrar cliente: ", ['context' => json_encode($e->getMessage())]);
                return false;
            }
        }
    }

    /**
     * Deleta o perfil do cliente na pagarme
     * 
     */
    public function deleteOnPagarme()
    {
    }
}
