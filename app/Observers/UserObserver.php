<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Player;
use App\Models\Tournament;
use Illuminate\Support\Carbon;
use App\Facades\IuguCustomer;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\Backoffice\MailingContactController;
use App\Http\Controllers\Api\Backoffice\MailingSubscriptionController;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;

class UserObserver
{

    /**
     * Handle the User "creating" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function creating(User $user)
    {
        $user->last_login = Carbon::now();
        if (empty($user->country)) {
            $user->country = 'BR';
        }
    }

    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        $tournament = Tournament::first();

        Player::create([
            'user_id' => $user->id,
            'tournament_id' => $tournament->id,
            'score' => 0
        ]);

        $plan = DB::table('plans')->where('identifier', 'freemium')->get();

        Subscription::create([
            'plan_id' => $plan[0]->id,
            'user_id' => $user->id,
            'status' => 'active',
            'expires_at' => \Carbon\Carbon::maxValue(),
            'started_at' => \Carbon\Carbon::now(),
            'renewable' => null
        ]);

        try {

            $customerToCreate = json_encode([
                'email' => $user->email,
                'name' => $user->name,
            ]);

            $userToCreate = IuguCustomer::createCustomer($customerToCreate);
            $user->payment_gw_id = $userToCreate->id;
            $user->save();
        } catch (\Throwable $th) {

            Log::error("Erro ao adicionar usuário no iugu.", ['ctx' => $th]);
        }

        $mailingContact = new MailingContactController;
        $mailingContact_created = $mailingContact->create($user);
        if ($mailingContact_created) {
            $mailingSubscription = new MailingSubscriptionController;
            $mailingSubscription->create($mailingContact_created);
        } else {
            return;
        }
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        //
    }

    /**
     * Handle the User "restored" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
