<?php

namespace App\Console\Commands;

use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Symfony\Component\Console\Cursor;

class UpdateUsersCurrentSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:update-current-subscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Define a assinatura atual de cada usuário';

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
        $total_users_without_sub = User::whereNull('subscription_id')->count();
        if ($total_users_without_sub == 0) {
            $this->info('Nenhum usuário a ser atualizado');
            return 0;
        }
        $sync_count = 0;
        $free_plan = Plan::where('identifier', 'free')->first();
        $this->info('Iniciando a atualização de assinaturas');
        $bar = $this->output->createProgressBar($total_users_without_sub);
        $bar->start();

        do {
            $users = User::whereNull('subscription_id')->skip($sync_count)->take(100)->get();

            foreach ($users as $user) {
                $sub = $user->subscriptions()->latest()->first();
                if ($sub) {
                    $user->subscription_id = $sub->id;
                } else {
                    $sub = $user->newSubscription('main', $free_plan, Carbon::now());
                    $user->subscription_id = $sub->id;
                }
                $user->save();
                $total_users_without_sub--;
                $bar->advance();
            }
        } while ($total_users_without_sub > 0);

        $bar->setMessage("OK");
        $bar->finish();
        $this->info("\nAssinaturas atualizadas");

        return 0;
    }
}
