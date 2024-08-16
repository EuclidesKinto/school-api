<?php

namespace App\Http\Controllers\Api\Userland;

use App\Http\Controllers\Controller;
use App\Http\Resources\Userland\MachineCertificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Machine;
use App\Http\Resources\Collections\MachineCollection;
use App\Http\Resources\Userland\MachineResource;
use App\Http\Resources\Userland\MachineActivitiesResource;
use App\Models\Challenge;
use App\Models\Tag;
use App\ValueClasses\UserProgress;
use Illuminate\Database\Eloquent\Builder;

class LabController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function machines(Request $request)
    {
        $should_cache = false;
        $user = $request->user();
        $type = $request->get('type');
        $dificulty = $request->get('dificulty');
        $progress = $request->get('progress');
        $search = $request->get('search');

        $query = Machine::query();
        $q = Machine::query();

        if ($search && !(new Tag)->isTag($search)) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', '%' . $search . '%')->orWhere('os_name', 'LIKE', '%' . $search . '%');
            });
            $q->where('type', $type);
        }

        if ($dificulty) {
            $query->where('dificulty', $dificulty);
            $q->where('type', $type);
        }

        if ($type == 'training' && (new Tag)->isTag($search)) {
            $query->hasTag($search);
            $q->where('type', $type);
        }

        switch ($progress) {
            case UserProgress::UNSTARTED:
                $query->notStartedBy($user);
                break;
            case UserProgress::STARTED:
                $completed = $q->completedBy($user)->pluck('id');
                $query->whereNotIn('id', $completed);
                $query->startedBy($user);
                break;
            case UserProgress::COMPLETED:
                $query->completedBy($user);
                break;
            default:
                $should_cache = true;
                break;
        }

        if ($type) {
            $query->where('type', $type);
            $q->where('type', $type);
        }

        $machines = $query->with(['creator', 'blood', 'flags.tags'])->latest()->paginate(9);

        return MachineCollection::collection($machines);
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
     * @return JsonResponse | MachineResource
     */
    public function show(int $id)
    {
        $machine = Machine::findOrFail($id);
        if ($machine->type == 'certification') {
            return response()->json(['message' => 'Certification machine not found'], 404);
        }

        $machine->load(['creator', 'creator.scoreGeneral', 'blood', 'blood.scoreGeneral', 'flags', 'flags.tags', 'attachments', 'instanceActive' => function ($query) {
            $query->where('user_id', auth()->user()->id);
        }]);

        return new MachineResource($machine);
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

    public function pownMachine(Request $request, $id)
    {

        $user = $request->user();
        $flag = trim($request->input('flag'));

        $machine = Machine::findOrFail($id);

        if (!$user->is_premium() && !$machine->is_freemium) {

            return response()->json(['message' => 'Você precisa ser assinante pra conseguir submeter esta flag!', 'success' => false], 200);
        }

        $flagController = new FlagController;

        return $flagController->own($flag, $machine);
    }

    public function pownChallenge(Request $request, $id)
    {

        $user = $request->user();
        $flag = trim($request->input('flag'));

        $challenge = Challenge::findOrFail($id);

        if (!$user->is_premium() && !$challenge->is_freemium) {

            return response()->json(['message' => 'Você precisa ser assinante pra conseguir submeter esta flag!', 'success' => false], 200);
        }

        $flagController = new FlagController;

        return $flagController->own($flag, $challenge);
    }

    public function activities(Request $request, $id)
    {
        $machine = Machine::findOrFail($id);
        // get current machine scoreboard
        if ($machine->type == 'certification') {
            return response()->json(['message' => 'Certification machine not found'], 404);
        }

        // get current machine activities (last 10)
        return new MachineActivitiesResource($machine);
        //owns

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return MachineCertificationResource
     */
    public function showMachineCertification(int $id): MachineCertificationResource
    {
        $machine = Machine::findOrFail($id);

        $machine->load(['creator', 'creator.scoreGeneral','flags', 'flags.tags', 'instanceActive' => function ($query) {
            $query->where('user_id', auth()->user()->id);
        }]);

        return new MachineCertificationResource($machine);
    }
}
