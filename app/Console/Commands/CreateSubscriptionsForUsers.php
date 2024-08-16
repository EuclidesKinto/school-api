<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateSubscriptionsForUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uhclabs:createsubscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create subscriptions for users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $users = $this->withProgressBar(User::all(), function (User $user) {

            if (!$user->subscription->isNotEmpty()) {

                $plan = DB::table('plans')->where('identifier', 'freemium')->get();

                Subscription::create([
                    'plan_id' => $plan[0]->id,
                    'user_id' => $user->id,
                    'status' => 'active',
                    'expires_at' => \Carbon\Carbon::maxValue(),
                    'started_at' => \Carbon\Carbon::now(),
                    'renewable' => null,
                ]);
            }
        });
    }
}