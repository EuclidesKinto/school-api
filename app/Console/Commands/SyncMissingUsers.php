<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LeadLovers;
use App\Models\User;

class SyncMissingUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uhclabs:syncmissingusers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send to ll = new LeadLovers(); users who have not logged in for more than 15 days.';

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
        $ll = new LeadLovers();
        $users = User::all();
        
        foreach($users as $user){
            if(env('APP_ENV') == 'production'){
                if($this->is_premium($user)){
                    if($user->last_login){
                        if(\Carbon\Carbon::parse($user->last_login)->lt(\Carbon\Carbon::now()->subDays(15))){
                            $result = $ll->AddMissingUser($user);
                            
                        }
                     }
                }
            }
        }
    }

    public function is_premium($user){
        if($user->subscription('default')){
            // pega a inscrição atual do stripe;
            $sub = $user->subscription('default')->asStripeSubscription();
            // verifica o status da inscrição
            if($sub->status == 'active'){
                // retorna o valor do subscribed se ativa
               
                return true;
            }
        }
    }
}
