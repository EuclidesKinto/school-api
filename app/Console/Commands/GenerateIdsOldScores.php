<?php

namespace App\Console\Commands;

use App\Models\Challenge;
use App\Models\Flag;
use App\Models\Machine;
use App\Models\OldScore;
use App\Models\User;
use Illuminate\Console\Command;

class GenerateIdsOldScores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:ids-old-scores';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $old_scores = OldScore::where('user_id', null)->get();
        $progressBar = $this->output->createProgressBar($old_scores->count());
        foreach ($old_scores as $old_score) {

            $user = User::where('email', $old_score->email)->first();
            if ($user) {
                $old_score->user_id = $user->id;
            }

            $flag = Flag::where('flag', $old_score->flag)->first();
            if ($flag) {
                $old_score->flag_id = $flag->id;
            }

            if ($old_score->origin_type == 'App\Models\Challenge') {
                $challenge = Challenge::where('name', $old_score->resource)->first();
                if ($challenge) {
                    $old_score->origin_id = $challenge->id;
                }
            }
            if ($old_score->origin_type == 'App\Models\Machine') {
                $machine = Machine::where('name', $old_score->resource)->first();
                if ($machine) {
                    $old_score->origin_id = $machine->id;
                }
            }
            $old_score->save();
            $progressBar->advance();
        }
        $progressBar->finish();
        $this->output->writeln('');
    }
}
