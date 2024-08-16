<?php

namespace App\Services\Iugu;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class IuguCharge
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

    public function createCharge($params)
    {
        $response = $this->http->request('post', 'charge', ['body' => $params]);
       
        $responseBody = json_decode($response->getBody()->getContents());

        return $responseBody;
    }
}
