<?php

namespace App\Console\Commands;

use App\Actions\Subscriptions\SyncSubscriptionOnGateway;
use App\Models\Subscription;
use Illuminate\Console\Command;

class SyncStripeSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stripe:syncsubscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza os dados das subscriptions do stripe com os dados das subscriptions locais';

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
        $stripe_subs = Subscription::where('gateway', 'stripe')->get();
        $count = $stripe_subs->count();

        $bar = $this->output->createProgressBar($count);
        $bar->start();
        foreach ($stripe_subs as $sub) {
            SyncSubscriptionOnGateway::make()->handle($sub);
            $bar->advance();
        }
        $bar->finish();
        $this->info(" Subscriptions sincronizadas com sucesso!");
        return 0;
    }
}
