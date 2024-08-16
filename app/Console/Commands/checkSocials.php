<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class checkSocials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uhclabs:checksocials';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check user socials links';

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

        $users = User::all();

        $bar = $this->output->createProgressBar(count($users));

        $bar->start();

        foreach ($users as $user) {

            $user->github_url = str_replace('https://github.com/', '', $user->github_url);

            $user->linkedin_url = str_replace('https://www.linkedin.com/in/', '', $user->linkedin_url);

            $user->save();

            $bar->advance();
        }

        $bar->finish();

        return 0;
    }
}
