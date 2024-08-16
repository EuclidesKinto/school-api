<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Facades\IuguCustomer;

class CreateIuguUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uhclabs:createiuguusers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create users in Iugu for hackingclub users that dont have an iugu account';

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

        $users = $this->withProgressBar(User::where('payment_gw_id', '=', null)->get(), function (User $user) {

            try {

                $customerToCreate = json_encode([
                    'email' => $user->email,
                    'name' => $user->name,
                ]);

                $userToCreate = IuguCustomer::createCustomer($customerToCreate);
                $user->payment_gw_id = $userToCreate->id;
                $user->save();
            } catch (\Throwable $th) {

                Log::error("Erro ao adicionar usuário no iugu.", ['ctx' => $th]);
            }
        });
    }
}