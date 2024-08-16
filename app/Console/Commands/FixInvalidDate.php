<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;

class FixInvalidDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uhclabs:fixinvaliddate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fixes invalid dates on subscriptions';

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
        $subscriptions = Subscription::all();
        foreach ($subscriptions as $subscription) {

            if ($subscription->expires_at == 'Invalid date') {

                $subscription->expires_at = $subscription->updated_at;

                $subscription->save();
            }
        }
        return Command::SUCCESS;
    }
}
