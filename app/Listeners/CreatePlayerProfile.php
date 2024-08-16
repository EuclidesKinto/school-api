<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Models\Player;
use App\Models\Tournament;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreatePlayerProfile
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(UserCreated $event)
    {
        $tournament = Tournament::first();

        Player::create([
            'user_id' => $event->user->id,
            'tournament_id' => $tournament->id,
            'score' => 0
        ]);
    }
}
