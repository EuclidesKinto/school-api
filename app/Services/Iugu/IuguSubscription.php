<?php

namespace App\Services\Iugu;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class IuguSubscription
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

    public function createSubscription($params)
    {
        $response = $this->http->request('post', 'subscriptions', ['body' => $params]);

        $responseBody = json_decode($response->getBody()->getContents());

        return $responseBody;
    }

    public function deleteSubscription($id)
    {
        $response = $this->http->request('delete', 'subscriptions/' . $id);

        $responseBody = json_decode($response->getBody()->getContents());

        return $responseBody;
    }

    public function activateSubscription($id)
    {

        $response = $this->http->request('post', 'subscriptions/' . $id . '/activate');

        $responseBody = json_decode($response->getBody()->getContents());

        return $responseBody;
    }

    public function suspendSubscription($id)
    {

        $response = $this->http->request('post', 'subscriptions/' . $id . '/suspend');

        $responseBody = json_decode($response->getBody()->getContents());

        return $responseBody;
    }

    public function changePlanSubscription($id, $planIdentifier)
    {

        $response = $this->http->request('post', 'subscriptions/' . $id . '/change_plan/' . $planIdentifier);

        $responseBody = json_decode($response->getBody()->getContents());

        return $responseBody;

    }
}
