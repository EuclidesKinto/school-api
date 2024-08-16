<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Machine;
use Carbon\Carbon;

class RemoveFreemium extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uhclabs:removeFreemium';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check machine release date, if its past one week today: deactivates freemium';

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
        $machines = Machine::where('is_freemium', '=', 1)->get();

        $today = Carbon::now();

        $oneWeekAgo = $today->subDays(7);

        foreach ($machines as $machine) {

            if ($machine->release_at->lte($oneWeekAgo)) {

                $machine->is_freemium = 0;

                $machine->save();
            }
        }

        return 0;
    }
}
