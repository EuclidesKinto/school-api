<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\Userland\EmailVerificationController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Resources\Userland\UserResource;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\App;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SsoController extends Controller
{

    public function __construct()
    {
    }

    /**
     * Executa o login via SSO (provider é o nome da plataforma, ex: google, microsoft, facebook, etc)
     */
    public function SsoLogin($provider)
    {
        try {
            $user = Socialite::driver($provider)->stateless()->user();

            if (!isset($user) && !isset($user->id)) {
                return response()->json([
                    'message' => 'Nao foi possível fazer login com o ' . $provider,
                    'error' => true
                ]);
            }

            // if the user exits, use that user and login
            $oldUser = User::where('email', $user->email)->first();

            if ($oldUser) {
                //if the user exists, login and show dashboard
                Auth::login($oldUser, true);
                $oldUser->last_login = \Carbon\Carbon::now()->toDateTimeString();
                $oldUser->saveQuietly();
                $oldUser->load(['scoreGeneral', 'subscriptionPremium']);

                return new UserResource($oldUser);
            } else {
                // if the user does not exist in database, create a new one.
                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'google_id' => $user->id,
                    'profile_photo_path' => $user->avatar,
                    'password' => bcrypt(Str::random()),
                    'last_login' => \Carbon\Carbon::now()->toDateTimeString()
                ]);
                Auth::login($newUser, true);

                // give all new accounts the default role "user"
                $newUser->assignRole('user');
                // after registrating the new User, we signin him into the app

                $newUser->load(['scoreGeneral']);

                return (new UserResource($newUser))->response()->setStatusCode(201);
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Nao foi possível fazer login com o ' . $provider,
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Desloga o usuário da plataforma
     */
    public function SsoLogout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json();
    }

    /**
     * Verifica se o usuário está logado
     */
    public function verify()
    {
        $user = Auth::user();

        $user->load(['scoreGeneral', 'subscriptionPremium']);

        return new UserResource($user);
    }

    /**
     * Login para testes da API com Insonmia
     * ATENÇÃO: SÓ FUNCIONA EM MODO LOCAL
     */
    public function dummyLogin(Request $request)
    {
        $email = $request->input('email', config('development.email'));
        if (App::environment('local')) {
            $user = User::where('email', $email)->firstOrFail();

            Auth::login($user, true);

            $user->load(['scoreGeneral']);

            return new UserResource($user);
        } else {
            return [];
        }
    }

    public function doLogin(Request $request)
    {

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

            $user = $request->user();

            if ($user->hasVerifiedEmail()) {

                $user->last_login = \Carbon\Carbon::now()->toDateTimeString();
                $user->save();

                $user->load(['scoreGeneral']);

                return response()->json([
                    'user' => new UserResource($user),
                    'success' => true
                ]);
            } else {

                return response()->json([
                    'message' => 'E-mail não verificado',
                    'success' => false
                ]);
            }
        } else {

            return response()->json([
                'message' => 'Não foi possível fazer login com os dados informados',
                'success' => false
            ], Response::HTTP_UNAUTHORIZED);
        }
    }
    public function doRegister(Request $request)
    {

        $credentials = $request->only('name', 'email', 'password');

        try {

            $client = new Client([
                'headers' => [
                    'Content-Type' => 'application/json',
                ]
            ]);
            $email = ['email' => $credentials['email']];
            $contactInfo = json_encode($email);
            $contactToCreate = ['body' => $contactInfo];
            $client->post(env('EMAIL_VALIDATOR_SERVICE_URL'), $contactToCreate);
        } catch (\Throwable $th) {

            if ($th->getCode() == 400) {

                return response()->json(['message' => 'Email inválido', 'success' => false], Response::HTTP_UNAUTHORIZED);
            }

            Log::error("Erro ao tentar conectar com o serviço email validator", ['ctx' => $th]);
            return response()->json(['message' => 'Erro de comunicação com o servidor', 'success' => false], Response::HTTP_UNAUTHORIZED);
        }

        // verify if user exists on database based on email
        $user = User::where('email', $credentials['email'])->first();

        // if user exists, return error;
        if ($user)
            return response()->json(['message' => 'Usuário já cadastrado', 'success' => false], Response::HTTP_UNAUTHORIZED);

        // if user does not exist, create a new one

        $user = User::create([
            'name' => $credentials['name'],
            'email' => $credentials['email'],
            'password' => bcrypt($credentials['password']),
            'last_login' => \Carbon\Carbon::now()->toDateTimeString()
        ]);

        $sendEmail = new EmailVerificationController;

        $sendEmail->sendVerificationEmail($request, $user);

        return response()->json(['message' => 'Usuário cadastrado com sucesso, verifique seu email', 'success' => true], Response::HTTP_CREATED);
    }
}
