<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use app\Models\User;
use app\Models\Instance;

class InstancesManager
{

    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => env('IM_API'),
            'headers' => [
                'Authorization' => 'Bearer ' . env('IM_API_KEY')
            ]
        ]);
    }

    public function start($resource_id, $deadline = null)
    {
        $response = $this->client->post('/api/instances', [
            'json' => [
                'resource_id' => $resource_id,
                'remote_user_id' => auth()->user()->id,
                'deadline' => $deadline
            ]
        ]);

        if ($response->getStatusCode() != 201) return false;

        $body = $response->getBody();
        $data = json_decode($body);

        return $data->id ? $data : false;
    }

    public function terminate($instance)
    {
        if (!$instance) return false;

        try {
            $response = $this->client->delete('/api/instances/' . $instance->remote_instance_id);
            if ($response->getStatusCode() != 200) return false;

            $body = $response->getBody();
            $data = json_decode($body);
            return $data->id ? true : false;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    public function getLastInstance(User $user)
    {

        if (!$user) return false;

        $response = $this->client->get("/api/users/{$user->id}/current");
        if ($response->getStatusCode() != 200) return false;

        $body = $response->getBody();
        $data = json_decode($body);

        return $data->success ? $data->instance : false;
    }

    public function getCurrentInstance()
    {
        $user_id = auth()->user()->id;
        try {
            $response = $this->client->get("/api/users/{$user_id}/current");
        } catch (\Exception $e) {
            if ($e->getCode() != 200) return false;
        }

        $body = $response->getBody();
        $data = json_decode($body);

        return $data->success ? $data->instance : false;
    }

    public function addTime($instance)
    {
        if (!$instance) return false;

        try {
            $response = $this->client->post('/api/instances/' . $instance->remote_instance_id . '/add-time');

            if ($response->getStatusCode() != 200) return false;

            $body = $response->getBody();
            $data = json_decode($body);
            return $data->success ? true : false;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }
}
