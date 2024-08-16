<?php

namespace App\Services\Iugu;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class IuguCustomer
{

    protected $http;

    public function __construct()
    {
        $this->http = new Client([
            'base_uri' => "https://api.iugu.com/v1/",
            'headers' => [
                'Authorization' =>  'Basic ' . base64_encode(config('app.iugu_api_key') . ':'),
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ]
        ]);
    }

    public function createCustomer($params)
    {

        $response = $this->http->request('post', 'customers', ['body' => $params]);

        $customer = json_decode($response->getBody()->getContents());

        return $customer;
    }

    public function updateCustomer($id, $params)
    {

        $response = $this->http->request('put', 'customers/' . $id, ['body' => $params]);

        $customer = json_decode($response->getBody()->getContents());

        return $customer;
    }

    public function deleteCustomer($id)
    {

        $response = $this->http->request('delete', 'customers/' . $id);

        $customer = json_decode($response->getBody()->getContents());

        return $customer;
    }
}
