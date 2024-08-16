<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Instance;
use App\Models\Machine;
use App\Models\Own;
use App\Models\User; 
use App\Http\Controllers\Api\Userland\HacktivityController;

class OwnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 1; $i <= 10; $i++) {
            Instance::create([
                'machine_id' => rand(1, 10),
                'user_id' => rand(1, 10),
                'is_active' => 1,
                'startup' => \Carbon\Carbon::now(),
                'shutdown' => \Carbon\Carbon::now()->addMinutes(60),
                'ip_address' => long2ip(rand(0, "4294967295")),
                'aws_instance_id' => 'aws_instance_id',
                'remote_instance_id' => 1
            ]);
        }
        for($i = 1; $i <= 10; $i++) {
            $machine = Machine::find(rand(1, 10));
            $flag = $machine->flags()->first();
            $user = User::find(rand(1, 10));
            Own::create([
                'flag_id' => $flag->id,
                'user_id' => $user->id,
                'points' => $flag->points,
                'instance_id' => rand(1, 10),
                'tournament_id' => 1,
                'machine_id' => $machine->id,
                'progress' => 100
            ]);

            $userNick = $user->nick;

            if ($user->nick == null) {
                $userNick = $user->name;
            };

            $machineInfo = [
                'user_nick' => $userNick,
                'resource_name' => $machine->name,
                'type' => 'own_flag',
                'subject_type' => get_class($machine),
                'subject_id' => $machine->id,
                'user_id' => $user->id,
                'flag_dificulty' => $flag->dificulty,
            ];

            $hacktivity = new HacktivityController;

            $hacktivity->create($machineInfo);
        }
    }
}
