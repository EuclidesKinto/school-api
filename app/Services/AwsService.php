<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class AwsService
{

    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('lab.deploy_url')
        ]);
    }

    public function start($ami_id)
    {
        $response = $this->client->post('/create08437ca8956b7c3b079ecb47a0216bb4978d5e1b4aef337cd34a290cb82134ce', [
            'json' => ['AMI_ID' => $ami_id]
        ]);

        $body = $response->getBody();
        return json_decode($body);
    }

    public function terminate($instance_id)
    {
        try{
            $this->client->post('/delete64faabc314bff38871340b182aeed2c3e5baa957027512419d70b4cd75374e5f', [
                'json' => ['INSTANCE_ID' => $instance_id],
                'timeout'=> 2
            ]);
        }catch(\Exception $e){
            Log::error($e->getMessage());
        }
    }
}
