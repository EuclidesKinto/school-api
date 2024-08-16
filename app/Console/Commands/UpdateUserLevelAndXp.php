<?php

namespace App\Console\Commands;

use App\Models\Machine;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateUserLevelAndXp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:update-user-xp-and-level';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all users on database based on owns';

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
        $all_machines = Machine::where('type','default')->get();
        // set all users xp and level to 0
        $affted_users = DB::table('users')->update(['xp' => 0, 'level' => 0]);
        echo "Updated $affted_users users xp and level to 0\n";
        $count = $all_machines->count();

        $bar = $this->output->createProgressBar($count);
        $bar->start();
        foreach ($all_machines as $machine){

            $machine_owns = $machine->owns()->get();
            foreach ($machine_owns as $own) {

                $user = $own->user;
                $user->xp = $user->xp + $own->points;

                if ($user->xp >= 1000) {
                    $user->level += 1;

                    $user->xp -= 1000;
                }

                $user->save();
            }

            // update first blood points
            if($machine->blooder_id){

                $user = User::find($machine->blooder_id);

                $user->xp = $user->xp + $machine->getFirstBloodPoints();

                if ($user->xp >= 1000) {
                    $user->level += 1;

                    $user->xp -= 1000;
                }

                $user->save();
            }

            $bar->advance();
        }
        $bar->finish();
        $this->info("\r\n");
        $this->info(" Users' XP and level was updated successfully !");
        return 0;
    }
}
