<?php

namespace App\Console\Commands;

use App\Actions\Instances\ShutdownInstances as InstancesShutdownInstances;
use Illuminate\Console\Command;

class ShutdownInstances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uhclabs:si';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Shutdown instances';

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
        $action = new InstancesShutdownInstances();
        $action->run();
    }
}
