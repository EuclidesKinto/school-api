<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\Userland\HacktivityController;
use Illuminate\Console\Command;
use App\Models\Machine;
use App\Models\User;
use Carbon\Carbon;

class ReleaseMachine extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uhclabs:releaseMachine';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for machine release dates if its less than today, activate the machine and freemium state';

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
        $machines = Machine::where('active', '=', 0)->get();

        foreach ($machines as $machine) {

            if ($machine->release_at->lte(Carbon::now())) {

                $machine->active = 1;
                $machine->is_freemium = 1;

                $machine->save();

                $user = User::find($machine->creator_id);

                $userNick = $user->nick;

                if ($user->nick == null) {
                    $userNick = $user->name;
                };
                $machineInfo = [
                    'user_nick' => $userNick,
                    'resource_name' => $machine->name,
                    'type' => 'release',
                    'subject_type' => get_class($machine),
                    'subject_id' => $machine->id,
                    'user_id' => $machine->creator_id,
                ];
    
                $hacktivity = new HacktivityController;
    
                $hacktivity->create($machineInfo);
            }
        }

        return 0;
    }
}
