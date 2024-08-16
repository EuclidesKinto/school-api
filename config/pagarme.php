<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Keys
    |--------------------------------------------------------------------------
    |
    | Here you may set up your authentication keys from Pagar.me, you can find
    | your keys accessing the dashboard at the link below.
    |
    | See: https://dashboard.pagar.me/#/myaccount/apikeys
    */

    'account_id' => env('PAGARME_ACCOUNT_ID', ''),

    'public_key' => env('PAGARME_PUBLIC_KEY', ''),

    'secret_key' => env('PAGARME_SECRET_KEY', ''),

    'base_api' => env('PAGARME_BASE_API', 'https://api.pagar.me/core/v5'),

    'invoice_due_days' => env('INVOICE_DUE_DAYS', 5),

    'billing' => [
        'name' => env('BILLING_PROFILE_NAME', 'Pagar.me'),
        'surname' => env('BILLING_PROFILE_SURNAME', 'Pagar.me'),
        'document' => env('BILLING_PROFILE_DOCUMENT', '00000000000'),
        'document_type' => env('BILLING_PROFILE_DOCUMENT_TYPE', 'cpf'),
    ],

];
