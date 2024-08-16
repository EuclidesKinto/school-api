<?php

namespace App\Console\Commands;

use App\Actions\Subscriptions\ImportStripeSubscription;
use App\Actions\Subscriptions\SyncStripeSubscription;
use Illuminate\Console\Command;
use App\Services\Stripe\Facades\Stripe;
use Illuminate\Support\Facades\DB;

class ImportUhcLabsSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uhclabs:importsubscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria o acesso dos usuários do UHCLabs na plataforma HackingClub.';

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
        $this->info('Iniciando a sincronização dos usuários do UHCLabs.');
        $subs = Stripe::subscriptions()->all(['status' => 'active']);
        $paid_subscriptions = 0;
        $users_not_found = 0;
        $users_already_subscribed = 0;
        $unpaid_subscriptions = 0;
        $total_subscriptions = 0;
        foreach ($subs->autoPagingIterator() as $sub) {
            $total_subscriptions++;
            try {
                $this->line('Sincronizando a subscription: ' . $sub->id);
                DB::beginTransaction();
                $success = ImportStripeSubscription::make()->handle($sub);
                switch ($success) {
                    case 1:
                        $paid_subscriptions++;
                        $this->info("Subscription $sub->id sincronizada com sucesso.");
                        break;
                    case 2:
                        $this->warn('Usuário não encontrado na base de dados.');
                        $users_not_found++;
                        break;
                    case 3: // already subscribed
                        $this->warn('Usuário já está inscrito.');
                        $users_already_subscribed++;
                        break;
                    case 4:
                        $this->warn('Subscription não paga.');
                        $unpaid_subscriptions++;
                        break;
                }
                DB::commit();
            } catch (\Exception $th) {
                $this->warn('Erro ao sincronizar a subscription: ' . $sub->id);
                $this->error($th->getMessage() . ' on ' . $th->getFile() . ':' . $th->getLine() . '\n' . $th->getTraceAsString());
                DB::rollBack();
                continue;
            }
        }
        $this->table(['', 'Subscriptions', 'Pagas', 'Não pagas', 'Usuários não encontrados', 'Usuários já inscritos'], [
            ['Total', $total_subscriptions, $paid_subscriptions, $unpaid_subscriptions, $users_not_found, $users_already_subscribed],
        ]);
        return 0;
    }
}
