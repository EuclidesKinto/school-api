<?php

namespace App\Http\Controllers\Api\Userland;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class EmailVerificationController extends Controller
{

    public function sendVerificationEmail(Request $request, $user = null)
    {

        if (!$user) {
            $user = User::where('email', $request->email)->first();
        }
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email já verificado',
                'error' => true
            ]);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'E-mail de verificação enviado',
            'success' => true
        ]);
    }

    public function verify(Request $request)
    {

        $user = User::find($request->route('id'));

        if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException();
        }

        if ($user->hasverifiedEmail()) {
            return [
                'message' => 'Email já verificado',
                'success' => true
            ];
        }

        if ($user->markEmailAsVerified())
            event(new Verified($user));

        return [
            'message' => 'Email verificado com sucesso',
            'success' => true
        ];
    }
}
