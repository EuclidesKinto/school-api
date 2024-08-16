<?php

namespace App\Services\Tropa;

use GuzzleHttp\Client;

class TropaService
{
    protected $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client([
            'base_uri' => config('services.whp.endpoint'),
            'headers' => [
                'x-api-key' => config('services.whp.api_key'),
            ],
            'http_errors' => false,
        ]);
    }

    /**
     * Retorna informações de um membro da tropa
     * @param string $email
     * @return array
     */
    public function getMember($email)
    {
        $response = $this->httpClient->post('/default/check_whp_student', [
            'json' => [
                'email' => $email,
            ],
        ]);
        if ($response->getStatusCode() == 200) {
            $response = json_decode($response->getBody());
            $member = collect($response->items)->first();

            if($member->status != 'ACTIVE'){
                return null;
            }else{
                return $member;
            }
        }
        return null;
    }

    /**
     * Retorna se o usuário é membro da tropa
     * 
     * @param string $email
     * @return boolean
     */
    public function isMember($email)
    {

        $membership = $this->getMember($email);
        return $membership;
        if ($membership) {
            return true;
        }

        return false;
    }
}
