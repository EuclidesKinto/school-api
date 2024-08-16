<?php

namespace App\Http\Controllers\Api\Billing;

use App\Http\Controllers\Controller;
use App\Models\Webhook as ModelsWebhook;
use App\Services\Webhook\Facades\Webhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use stdClass;
use Illuminate\Support\Str;

class WebHooksController extends Controller
{
    /**
     * Handles the webhooks from Pagar.me
     */
    public function pagarme(Request $request)
    {
        Log::debug(__CLASS__ . ':' . __FUNCTION__, [$request->all()]);
        $pagarme_webhook = $request->all();
        $model = Str::before($pagarme_webhook['type'], '.');
        $event = Str::after($pagarme_webhook['type'], '.');
        ModelsWebhook::create([
            'gateway' => 'pagarme',
            'webhook_id' => $pagarme_webhook['id'],
            'model' => $model,
            'event' => $event,
            'timestamp' => $pagarme_webhook['created_at'],
            'data' => $pagarme_webhook['data'],
            'raw_data' => $pagarme_webhook,
        ]);
        Webhook::handle($request->all());
        return response()->json(['status' => 'received', 'success' => true], 200);
    }


    public function stripe(Request $request)
    {
        return response()->json(['status' => 'received', 'success' => true], 200);
    }
}
