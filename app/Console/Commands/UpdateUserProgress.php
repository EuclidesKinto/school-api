<?php

namespace App\Console\Commands;

use App\Models\Machine;
use App\Models\Own;
use App\Models\User;
use Illuminate\Console\Command;

class UpdateUserProgress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:update-user-progress';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update user progress on all machines based on their owns';

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
        $all_users = User::all();
        $count = $all_users->count();

        $bar = $this->output->createProgressBar($count);
        $bar->start();
        foreach ($all_users as $user) {

            # get all user owns
            $user_owns = $user->owns()->get();
            foreach ($user_owns as $own) {

                # get current machine of this own
                $machine = Machine::find($own->machine_id);
                if($machine) {
                    # get count of flags of this machine
                    $flags_count = $machine->flags()->count();

                    # get percentage for each flag on this machine
                    $percentage = 100 / $flags_count;
                    $current_progress = 0       ;
                    $all_users_owns_on_this_machine = Own::where('machine_id', $machine->id)->where('user_id', $user->id);
                    for($i = 0; $i < $flags_count; $i++) {
                            # get first flag of this machine and check if it is owned by this user
                            $current = $all_users_owns_on_this_machine->skip($i)->first();
                            if($current) {
                                # if it is owned by this user, update progress
                                $current_progress += $percentage;
                                $current->progress = $current_progress;
                                $current->save();
                            }
                    }
                }


            }

            $bar->advance();
        }
        $bar->finish();
        $this->info("\r\n");
        $this->info(" Users' progress was updated successfully !");
        return 0;
    }
}
