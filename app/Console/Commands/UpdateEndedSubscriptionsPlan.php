<?php

namespace App\Console\Commands;

use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateEndedSubscriptionsPlan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:update-ended';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description =  "Atualiza a subscription de usuários que cancelaram o plano anterior para plano gratuito.";

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
        $free_plan = Plan::where('identifier', 'free')->first();
        $users_canceled_subscriptions = User::whereHas('subscription', function ($sub) use ($free_plan) {
            return $sub->where([
                ['plan_id', '<>', $free_plan->id],
                ['cancels_at', '<=', Carbon::now()]
            ]);
        })->with('subscription')->get();

        $count = $users_canceled_subscriptions->count();
        $this->info('Iniciando a atualização de assinaturas');
        $bar = $this->output->createProgressBar($count);

        foreach ($users_canceled_subscriptions as $user) {
            $subscription = $user->newSubscription('main', $free_plan);
            $user->subscription()->associate($subscription);
            $user->save();
            $bar->advance();
        }
        $bar->finish();
        $this->info("\nAssinaturas atualizadas");
        return 0;
    }
}
