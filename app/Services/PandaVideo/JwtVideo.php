<?php

namespace App\Services\PandaVideo;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtVideo
{
    public static function GenerateSignedUrl($url)
    {
        $current_user = auth()->user();

        if($current_user->hasRole('admin') || $current_user->is_premium()){
            if(!empty($current_user->cpf)){
                $string3 = 'CPF: ' . $current_user->cpf;
            }else{
                $string3 = 'Email: ' . $current_user->email;
            }

            $group_id = env('PANDA_API_KEY');
            $key = env('PANDA_SECRET_KEY');
            $payload = [
                'drm_group_id' => $group_id,
                'string1' => 'Licenciado para ' . $current_user->name,
                'string2' => 'Validade: ' . Carbon::now()->addDays(1)->format('d/m/Y'),
                'string3' => $string3
            ];
            $expiresIn = 86400; // 24h
            return JWT::encode(
                array_merge($payload, ['exp' => time() + $expiresIn]),
                $key,
                'HS256'
            );
        }else{
            return null;
        }


    }
}
