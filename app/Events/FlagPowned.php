<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use \App\Models\User;

class FlagPowned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $tournamentId;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\user
     * @return void
     */
    public function __construct(User $user, $tournamentId)
    {
        $this->user = $user;
        $this->tournamentId = $tournamentId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
