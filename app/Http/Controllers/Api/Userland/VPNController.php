<?php

namespace App\Http\Controllers\Api\Userland;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VPNController extends Controller
{
    public $vpn_client;
    public $vpn_organization;

    public function __construct()
    {
        $this->vpn_client = new \GuzzleHttp\Client([
            'base_uri' => env('VPN_MICROSERVICE_URL'),
            'timeout' => 5,
            'connect_timeout' => 4,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'x-api-key' => env('VPN_MICROSERVICE_API_KEY'),
            ],
        ]);
        $this->vpn_organization = env('VPN_MICROSERVICE_ORG');
    }

    public function getProfile()
    {
        $user = Auth::user();
        if ($user->vpn_user_id) {
            $response = $this->vpn_client->request('GET', "", [
                'query' => [
                    'user_id' => $user->vpn_user_id,
                    'organization' => $this->vpn_organization,
                ]
            ]);
            if ($response->getStatusCode() == 200) {
                $vpn = json_decode($response->getBody()->getContents());
                return response()->json([
                    'message' => 'VPN profile retrieved',
                    'success' => true,
                    'vpn' => collect($vpn)->only(['online', 'virtual_ip', 'user_id', 'vpn_file'])
                ]);
            } else if ($response->getStatusCode() == 404) {
                return response()->json([
                    'success' => false,
                    'message' => 'VPN profile not found (micro-service)'
                ]);
            } else if ($response->getStatusCode() == 400) {
                Log::debug('vpn::error erro ao gerar VPN do usuário, API retornou bad request');
                return response()->json(['success' => false, 'message' => 'Error to retrieve your VPN'], 400);
            } else {
            }
        } else {
            return response()->json(['success' => false, 'message' => 'VPN profile not found'], 404);
        }
    }

    public function createProfile()
    {

        // make a put request to vpn microservice and create new profile

        // check if user already an vpn profile in microservice
        $user = Auth::user();
        if ($user->vpn_user_id) {
            return response()->json(['success' => false, 'message' => 'User already have a VPN profile'], 400);
        }

        $response = $this->vpn_client->request('PUT', '', [
            'query' => [
                'username' => explode("@", Auth::user()->email)[0],
                'organization' => $this->vpn_organization
            ]
        ]);

        if ($response->getStatusCode() == 200) {

            $vpn = json_decode($response->getBody()->getContents());
            $user = Auth::user();
            $user->vpn_user_id = $vpn->user_id;
            $user->save();

            return response()->json(
                [
                    'message' => 'VPN profile created',
                    'success' => true,
                    'vpn' => collect($vpn)->only(['online', 'virtual_ip', 'user_id', 'vpn_file'])
                ],
                200
            );
        } else if ($response->getStatusCode() == 400) {
            Log::debug('vpn::error erro ao gerar VPN do usuário, API retornou bad request');
            return response()->json(['success' => false, 'message' => 'Error creating VPN'], 400);
        }
    }
}
