<?php

namespace App\Services\Iugu;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class IuguPaymentMethod
{

    protected $http;

    public function __construct()
    {
        $this->http = new Client([
            'base_uri' => "https://api.iugu.com/v1/",
            'headers' => [
                'Authorization' =>  'Basic ' . base64_encode(config('app.iugu_api_key')),
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ]
        ]);
    }

    public function createPaymentMethod($id, $params)
    {

        $response = $this->http->request('post', 'customers/' . $id . '/payment_methods', ['body' => $params]);

        if ($response->getStatusCode() == 422 || $response->getStatusCode() == 500) {
            Log::error("Erro ao criar forma de pagamento.", ['ctx' => $response->getBody()->getContents()]);
            Log::error($response->getBody()->getContents());
        }

        $responseBody = json_decode($response->getBody()->getContents());
        return $responseBody;
    }

    public function deletePaymentMethod($id, $payment_method_id)
    {

        $response = $this->http->request('post', 'customers/' . $id . '/payment_methods/' . $payment_method_id);

        $responseBody = json_decode($response->getBody()->getContents());

        return $responseBody;
    }

    public function updatePaymentMethod($id, $payment_method_id, $params)
    {
        $response = $this->http->request('post', 'customers/' . $id . '/payment_methods/' . $payment_method_id . '?api_token=37BAFDB2AF4FA093B660EB3D828EC30CB86E5A4641A6E703D7148091849CF5C0', ['body' => $params]);

        $responseBody = json_decode($response->getBody()->getContents());

        return $responseBody;
    }
}
