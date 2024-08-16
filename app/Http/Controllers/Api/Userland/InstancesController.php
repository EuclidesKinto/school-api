<?php

namespace App\Http\Controllers\Api\Userland;

use App\Http\Controllers\Controller;
use App\Http\Resources\Userland\ChallengeResource;
use App\Http\Resources\Userland\MachineResource;
use Illuminate\Http\Request;
use App\Models\Instance;
use App\Models\Machine;
use Carbon\Carbon;
use GrahamCampbell\ResultType\successs;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\Userland\InstanceResource;
use App\Models\Challenge;
use App\Models\ChallengeInstance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Services\InstancesManager;

class InstancesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Instance::get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Instance::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Deploy new instance on aws using AWS Service
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deploy(Request $request, $id)
    {
        $machine = (new Machine())->findorFail($id);

        $machine->load(['creator', 'creator.scoreGeneral', 'blood', 'blood.scoreGeneral', 'flags', 'flags.tags', 'attachments']);

        if (!$machine->release_at->lte(Carbon::now())) {
            return response()->json([
                'message' => 'Máquina ainda não disponivel para jogar',
                'succes' => false
            ], 400);
        }

        if ($machine->active == 0) {
            return response()->json([
                'message' => 'Esta máquina ainda não está ativa!',
                'success' => false
            ]);
        }

        // check if user has already deployed this instance
        $user = $request->user();
        $im = new InstancesManager;
        if ($im->getCurrentInstance()) {
            return response()->json([
                'message' => 'Você possui uma instância ativa. Para criar uma nova instância, desative a instância ativa.',
                'success' => false
            ], 200);
        }

        if ($machine->is_freemium || $user->is_premium()) {

            try {

                if (env('APP_ENV') == 'local') {

                    $instance = new \Stdclass;
                    $instance->instance_id = 'i-test-instance-' . Str::random(10);
                    $instance->instance_ip = long2ip(mt_rand());
                    $instance->instance_remote_id = 123123;
                    $instance->instance_ip_address = long2ip(mt_rand());
                    $instance->id = rand(1000, 100000);
                } else {
                    $instance = $im->start($machine->remote_resource_id);
                }

                if ($instance && $instance->instance_ip_address && $instance->instance_remote_id) {

                    $instance = Instance::create([
                        'remote_instance_id' => $instance->id,
                        'aws_instance_id' => $instance->instance_remote_id,
                        'ip_address' => $instance->instance_ip_address,
                        'machine_id' => $machine->id,
                        'startup' => (new \Carbon\Carbon())->toDateTimeString(),
                        'shutdown' => (new \Carbon\Carbon())->addHour()->toDateTimeString(),
                        'user_id' => $user->id,
                        'is_active' => 1
                    ]);
                    $machine->load(
                        [
                            'creator',
                            'creator.scoreGeneral',
                            'blood',
                            'blood.scoreGeneral',
                            'flags',
                            'flags.tags',
                            'attachments',
                            'instanceActive' => function ($query) use ($user) {
                                $query->where('user_id', $user->id);
                            },

                        ]
                    );

                    return response()->json([
                        'message' => 'instância iniciada com sucesso!',
                        'success' => true,
                        'machine' => new MachineResource($machine)
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'Oops, tivemos um problema para iniciar sua instância. tente novamente...',
                        'success' => false
                    ], 200);
                }
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return response()->json([
                    'message' => 'Oops, tivemos um problema para iniciar sua instância. tente novamente...',
                    'success' => false
                ], 200);
            }
        } else {
            return response()->json([
                'message' => 'Somente assinantes podem iniciar esta máquina.',
                'success' => false
            ], 200);
        }
    }

    public function deployChallenge(Request $request, $id)
    {
        $challenge = (new Challenge())->findorFail($id);

        if (!$challenge->release_at->lte(Carbon::now())) {
            return response()->json([
                'message' => 'Desafio ainda não disponivel para jogar',
                'succes' => false
            ], 400);
        }



        // check if user has already deployed this instance
        $user = $request->user();
        $im = new InstancesManager;
        if ($im->getCurrentInstance()) {

            $current_instance = ChallengeInstance::where('user_id', $user->id)->first();

            if ($current_instance->count() > 0) {
                $current_instance->update([
                    'is_active' => 0,
                    'shutdown' => (new \Carbon\Carbon())->toDateTimeString()
                ]);
            } else {
                return response()->json([
                    'message' => 'Você possui uma instância ativa. Para criar uma nova instância, desative a instância ativa.',
                    'success' => false
                ], 200);
            }
        }

        if ($challenge->is_freemium || $user->is_premium()) {

            try {

                if (env('APP_ENV') == 'local') {

                    $instance = new \Stdclass;
                    $instance->instance_id = 'i-test-instance-' . Str::random(10);
                    $instance->instance_ip = long2ip(mt_rand());
                    $instance->instance_remote_id = 123123;
                    $instance->instance_ip_address = long2ip(mt_rand());
                    $instance->remote_instance_id = rand(1000, 100000);
                    $instance->id = rand(1000, 100000);
                } else {
                    $instance = $im->start($challenge->remote_resource_id);
                }

                if ($instance && $instance->instance_ip_address && $instance->instance_remote_id) {

                    $instance = ChallengeInstance::create([
                        'remote_instance_id' => $instance->id,
                        'ip_address' => $instance->instance_ip_address,
                        'challenge_id' => $challenge->id,
                        'startup' => (new \Carbon\Carbon())->toDateTimeString(),
                        'shutdown' => (new \Carbon\Carbon())->addHour()->toDateTimeString(),
                        'user_id' => $user->id,
                        'is_active' => 1
                    ]);

                    $challenge->load([
                        'instanceActive' => function ($query) use ($user) {
                            $query->where('user_id', $user->id);
                        },
                    ]);

                    return response()->json([
                        'message' => 'instância iniciada com sucesso!',
                        'success' => true,
                        'challenge' => new ChallengeResource($challenge)
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'Oops, tivemos um problema para iniciar sua instância. tente novamente...',
                        'success' => false
                    ], 200);
                }
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return response()->json([
                    'message' => 'Oops, tivemos um problema para iniciar sua instância. tente novamente...',
                    'success' => false
                ], 200);
            }
        } else {
            return response()->json([
                'message' => 'Somente assinantes podem iniciar esta máquina.',
                'success' => false
            ], 200);
        }
    }

    public function deployCertification($machine, $deadline)
    {

        $machine->load(['creator', 'creator.scoreGeneral', 'blood', 'blood.scoreGeneral', 'flags', 'flags.tags', 'attachments']);

        $user = Auth::user();

        $im = new InstancesManager;

        try {

            if (env('APP_ENV') == 'local') {

                $instance = new \Stdclass;
                $instance->instance_id = 'i-test-instance-' . Str::random(10);
                $instance->instance_ip = long2ip(mt_rand());
                $instance->instance_remote_id = 123123;
                $instance->instance_ip_address = long2ip(mt_rand());
                $instance->id = rand(1000, 100000);
            } else {
                $instance = $im->start($machine->remote_resource_id, $deadline);
            }

            if ($instance && $instance->instance_ip_address && $instance->instance_remote_id) {

                $instance = Instance::create([
                    'remote_instance_id' => $instance->id,
                    'aws_instance_id' => $instance->instance_remote_id,
                    'ip_address' => $instance->instance_ip_address,
                    'machine_id' => $machine->id,
                    'startup' => (new \Carbon\Carbon())->toDateTimeString(),
                    'shutdown' => $deadline->toDateTimeString(),
                    'user_id' => $user->id,
                    'is_active' => 1
                ]);
                $machine->load(
                    [
                        'creator',
                        'creator.scoreGeneral',
                        'blood',
                        'blood.scoreGeneral',
                        'flags',
                        'flags.tags',
                        'attachments',
                        'instanceActive' => function ($query) use ($user) {
                            $query->where('user_id', $user->id);
                        },

                    ]
                );
                return true;
            } else {
                return response()->json([
                    'message' => 'Oops, tivemos um problema para iniciar sua instância. tente novamente...',
                    'success' => false
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'message' => 'Oops, tivemos um problema para iniciar sua instância. tente novamente...',
                'success' => false
            ], 500);
        }
    }

    /**
     * Terminate instance on aws using IM Service   
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function terminate(Request $request, $id)
    {

        $machine = (new Machine())->findorFail($id);
        // check if user has already deployed this instance

        $machine->load(['creator', 'creator.scoreGeneral', 'blood', 'blood.scoreGeneral', 'flags', 'flags.tags', 'attachments']);
        $user = Auth::user();
        $user->load('instances');
        $im = new InstancesManager;
        $instance = $im->getCurrentInstance();

        if ($instance) {

            try {

                $localInstance = Instance::where('remote_instance_id', $instance->id);

                $localInstance->update([
                    'is_active' => 0,
                    'shutdown' => (new \Carbon\Carbon())->toDateTimeString()
                ]);

                $im->terminate($localInstance->get()->first());

                return response()->json([
                    'message' => 'instância finalizada com sucesso!',
                    'success' => true,
                    'machine' => new MachineResource($machine)
                ], 200);
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return response()->json([
                    'message' => 'Oops, tivemos um problema para finalizar sua instância. tente novamente...',
                    'success' => false,
                    'machine' => new MachineResource($machine)
                ], 200);
            }
        } else {

            $currentInstance = $user->instances->where('is_active', 1)->where('machine_id', $id)->first();

            if ($currentInstance) {
                $currentInstance->update([
                    'is_active' => 0,
                    'shutdown' => (new \Carbon\Carbon())->toDateTimeString()
                ]);
                return response()->json([
                    'message' => 'instância finalizada com sucesso!',
                    'success' => true,
                    'machine' => new MachineResource($machine)
                ], 200);
            } else {

                return response()->json([
                    'message' => 'Você não possui uma instância ativa para ser desativada.',
                    'success' => false,
                    'machine' => new MachineResource($machine)
                ], 200);
            }
        }
    }

    public function terminateChallenge(Request $request, $id)
    {

        $challenge = (new Challenge())->findorFail($id);
        // check if user has already deployed this instance
        $user = $request->user();
        $user->load('instancesChallenge');
        $im = new InstancesManager;
        $instance = $im->getCurrentInstance();

        if ($instance) {

            try {

                $localInstance = ChallengeInstance::where('remote_instance_id', $instance->id);

                $localInstance->update([
                    'is_active' => 0,
                    'shutdown' => (new \Carbon\Carbon())->toDateTimeString()
                ]);

                $im->terminate($localInstance->get()->first());

                return response()->json([
                    'message' => 'instância finalizada com sucesso!',
                    'success' => true,
                    'challenge' => new ChallengeResource($challenge)
                ], 200);
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return response()->json([
                    'message' => 'Oops, tivemos um problema para finalizar sua instância. tente novamente...',
                    'success' => false,
                    'challenge' => new ChallengeResource($challenge)
                ], 200);
            }
        } else {

            $currentInstance = $user->instancesChallenge->where('is_active', 1)->where('challenge_id', $id)->first();

            if ($currentInstance) {
                $currentInstance->update([
                    'is_active' => 0,
                    'shutdown' => (new \Carbon\Carbon())->toDateTimeString()
                ]);
                return response()->json([
                    'message' => 'instância finalizada com sucesso!',
                    'success' => true,
                    'challenge' => new ChallengeResource($challenge)
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Você não possui uma instância ativa para ser desativada.',
                    'success' => false,
                    'challenge' => new ChallengeResource($challenge)
                ], 200);
            }
        }
    }

    public function terminateCertification()
    {
    }

    public function addTime(Request $request, $id)
    {

        $instance = Auth::user()->instances->where('is_active', 1)->where('machine_id', $id)->first();
        $instance->load('machine');
        $instance->machine->load([
            'creator',
            'instanceActive' => function ($query) {
                $query->where('user_id', Auth::user()->id);
            }, 'creator.scoreGeneral', 'blood', 'blood.scoreGeneral', 'flags', 'flags.tags', 'attachments'
        ]);

        if (!$instance) {
            return response()->json([
                'message' => 'Você não possui uma instância ativa para ser adicionada mais tempo.',
                'success' => false
            ], 200);
        }

        // verificar se o tempo restante é menor que 1 hora
        if ($instance->shutdown->diffInMinutes(Carbon::now()) < 60) {

            $im = new InstancesManager;
            $im->addTime($instance);

            $instance->shutdown = $instance->shutdown->addHour();
            $instance->save();

            $instance->machine->refresh();
            $instance->machine->load(['creator', 'creator.scoreGeneral', 'blood', 'blood.scoreGeneral', 'flags', 'flags.tags', 'attachments',  'instanceActive' => function ($query) {
                $query->where('user_id', Auth::user()->id);
            }]);

            return response()->json([
                'message' => 'Mais tempo de instância adicionado com sucesso!',
                'success' => true,
                'machine' => new MachineResource($instance->machine)
            ], 200);
        } else {

            return response()->json([
                'message' => 'Você já adicionou tempo a instância!',
                'success' => false,
                'machine' => new MachineResource($instance->machine)
            ], 200);
        }
    }
    public function addTimeChallenge(Request $request, $id)
    {

        $instance = Auth::user()->instancesChallenge->where('is_active', 1)->where('challenge_id', $id)->first();
        $instance->load('challenge');
        $instance->challenge->load([
            'instanceActive' => function ($query) {
                $query->where('user_id', Auth::user()->id);
            }, 'blood', 'flags'
        ]);
        if (!$instance) {
            return response()->json([
                'message' => 'Você não possui uma instância ativa para ser adicionada mais tempo.',
                'success' => false
            ], 200);
        }

        // verificar se o tempo restante é menor que 1 hora
        if ($instance->shutdown->diffInMinutes(Carbon::now()) < 60) {

            $im = new InstancesManager;
            $im->addTime($instance);

            $instance->shutdown = $instance->shutdown->addHour();
            $instance->save();

            $instance->challenge->refresh();
            $instance->challenge->load([
                'instanceActive' => function ($query) {
                    $query->where('user_id', Auth::user()->id);
                }, 'blood', 'flags'
            ]);

            return response()->json([
                'message' => 'Mais tempo de instância adicionado com sucesso!',
                'success' => true,
                'challenge' => new ChallengeResource($instance->challenge)
            ], 200);
        } else {

            return response()->json([
                'message' => 'Você já adicionou tempo a instância!',
                'success' => false,
                'challenge' => new ChallengeResource($instance->challenge)
            ], 200);
        }
    }
}
