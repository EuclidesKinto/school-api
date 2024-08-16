<?php

namespace App\Listeners;

use App\Events\FlagPowned;
use App\Http\Controllers\Api\Userland\ScoreboardController;

class UpdateScoreboard
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
    public function handle(FlagPowned $event)
    {
        $scoreboard = new ScoreboardController;

        // $scoreboard->update($event->tournamentId);
    }
}
