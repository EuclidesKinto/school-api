<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Machine;
use App\Models\User;
use App\Models\Own;
use Carbon\Carbon;
use App\Services\ScoreService;

class RetireMachine extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uhclabs:retireMachine';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check machine retire date, if it expired then change championship type to training type';

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
        $machines = Machine::where('type', '=', 'default')->get();

        foreach ($machines as $machine) {

            $retireDate = Carbon::parse($machine->retire_at);

            if ($retireDate->lte(Carbon::now())) {

                $machine->type = 'training';

                $owns = Own::where('machine_id', $machine->id)->get();

                foreach ($owns as $own) {

                    $user = User::find($own->user_id);

                    $scoreService = new ScoreService;

                    $scoreService->subtractScore($machine->tournament_id, $own->points, $machine, $user);
                }

                $blooder = User::find($machine->blooder_id);

                if ($user->id == $blooder->id) {

                    $firstBloodPoints = $machine->getFirstBloodPoints();

                    $user = User::find($own->user_id);

                    $scoreService = new ScoreService;

                    $scoreService->subtractScore($machine->tournament_id, $firstBloodPoints, $machine, $user);
                }

                $machine->save();
            }
        }
        return 0;
    }
}
