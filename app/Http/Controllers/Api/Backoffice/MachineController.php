<?php

namespace App\Http\Controllers\Api\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Resources\Collections\MachineCollection;
use App\Http\Resources\Userland\MachineResource;
use App\Models\Tag;
use App\Models\Flag;
use App\Models\Machine;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MachineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $machines = Machine::paginate(10);
        return new MachineCollection($machines);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $machine = $request->only([
            'ami_id',
            'name',
            'description',
            'os_name',
            'tournament_id',
            'dificulty',
            'creator_id',
            'release_at',
            'retire_at',
        ]);

        $machine['active'] = 0;
        $machine['is_freemium'] = 1;
        $machine['type'] = 'championship';

        $machine = Machine::create($machine);

        $machine->load(['creator', 'creator.scoreGeneral', 'blood', 'blood.scoreGeneral', 'flags', 'flags.tags', 'attachments']);

        return new MachineResource($machine);
    }

    /**
     * Retorna uma nova flag associada a uma máquina 
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Machine $machine
     * @return \Illuminate\Http\Response
     */
    public function storeFlags(Request $request, Machine $machine)
    {

        foreach ($request->flags as $flag) {

            $data = array(
                "flag" => $flag['flag'],
                "points" => $flag['points'],
                "dificulty" => $flag['dificulty'],
            );

            $auxFlag = new Flag();

            $auxFlag->fill($data);

            $machine->flags()->save($auxFlag);

            $auxFlag->id;

            foreach ($flag['tags'] as $tag) {

                if (Tag::where('name', '=', $tag)->exists()) {

                    $auxTag = Tag::where('name', '=', $tag)->get();

                    $auxFlag->tags()->attach($auxTag);
                } else {

                    $data = array(
                        "name" => $tag,
                    );

                    $auxTag = new Tag();

                    $auxTag->fill($data);

                    $auxTag['slug'] = Str::slug($tag, '-');

                    $auxFlag->tags()->save($auxTag);
                }
            }
        }

        return response(['success'], 201);
    }

    public function addMachineAvatar(Request $request, Machine $machine)
    {
        $uniqid = uniqid(rand(), true);

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        try {

            Storage::disk('s3')->put('machine/avatar/' . $uniqid, $request->image->get(), 'public');

            $path = Storage::disk('s3')->url('machine/avatar/' . $uniqid);

            Machine::where('id', $machine->id)->update(['photo_path' => $path]);

            return response(['success'], 201);
        } catch (\Exception $th) {

            return response(['failure'], 500);
        }
    }

    public function addMachineAttachments(Request $request, Machine $machine)
    {
        $images = array('APNG', 'AVIF', 'GIF', 'JPEG', 'PNG', 'SVG', 'WEBP', 'JPG');

        $docs = array('DOC', 'PDF', 'HTML', 'ODT', 'TXT');

        foreach ($request->file('attachments') as $attachment) {

            if (in_array(strtoupper($attachment->extension()), $docs)) {

                $fileType = 'doc';
            } elseif (in_array(strtoupper($attachment->extension()), $images)) {

                $fileType = 'image';
            }

            $uniqid = uniqid(rand(), true);

            try {
                $path = 'machine/' . $machine->id . 'attachments/' . $uniqid;
                Storage::disk('s3')->put($path, $attachment->get());

                $auxAttachment = new Attachment;

                $auxAttachment->name = $attachment->getClientOriginalName();
                $auxAttachment->file_size = $attachment->getSize();
                $auxAttachment->type = $fileType;
                $auxAttachment->url = $path;

                $machine->attachments()->save($auxAttachment);
            } catch (\Exception $th) {

                return response(['failure'], 500);
            }
        }

        foreach ($request->get('attachments') as $attachment) {

            $fileType = 'link';

            $auxAttachment = new Attachment;

            $auxAttachment->name = $attachment;
            $auxAttachment->url = $attachment;
            $auxAttachment->type = $fileType;

            $machine->attachments()->save($auxAttachment);
        }

        return response(['success'], 201);
    }

    /**
     * Remove uma flag associada a uma máquina
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Machine $machine
     * @param \App\Models\Flag $flag
     * @return \Illuminate\Http\Response
     */
    public function removeFlag(Request $request, Machine $machine, Flag $flag)
    {

        $flag->delete();
        return response([], 204);
    }

    public function addTag(Request $request, Flag $flag)
    {

        $data = $request->only([
            'name',
        ]);

        $tag = new Tag();

        $tag->fill($data);

        $tag['slug'] = Str::slug($request->name, '-');

        $flag->tags()->save($tag);

        return response(['success'], 201);
    }

    public function removeTag(Request $request, Machine $machine, Tag $tag)
    {
        $tag->delete();
        return response(['success'], 204);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $machine = Machine::findOrFail($id);

        $machine->load(['creator', 'creator.scoreGeneral', 'blood', 'blood.scoreGeneral', 'flags', 'flags.tags', 'attachments']);

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
        $machine = Machine::findOrFail($id);

        $machine->fill($request->only([
            'ami_id',
            'name',
            'description',
            'os_name',
            'tournament_id',
            'type',
            'dificulty',
            'blooder_id',
            'creator_id',
            'photo_path',
            'release_at',
            'retire_at',
        ]));

        $machine->active = $request->input('active');
        $machine->is_freemium = $request->input('is_freemium');
        $machine->save();

        $machine->load(['creator', 'creator.scoreGeneral', 'blood', 'blood.scoreGeneral', 'flags', 'flags.tags', 'attachments']);

        return new MachineResource($machine);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $machine = Machine::find($id);

        try {
            $machine->delete();

            return response()->json([
                "success" => true,
            ]);
        } catch (\Exception $th) {

            return response()->json([
                "success" => false
            ]);
        }
    }

    /**
     * Display a listing of soft deleted machines.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexRemoved()
    {
        try {

            return Machine::onlyTrashed()->get();
        } catch (\Exception $th) {

            return response()->json([
                "success" => false
            ]);
        }
    }

    /**
     * Display the specified soft deleted machine.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showRemoved($id)
    {
        try {

            return Machine::onlyTrashed()->where("id", "=", $id)->get();
        } catch (\Exception $th) {

            return response()->json([
                "success" => false
            ]);
        }
    }

    /**
     * Unblock specified soft deleted machine.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restoreMachines($id)
    {

        $machine = Machine::onlyTrashed()->find($id);

        try {

            $machine->restore();

            return response()->json([
                "success" => true,
            ]);
        } catch (\Exception $th) {

            return response()->json([
                "success" => false
            ]);
        }
    }

    public function showRecommendedMachine(Request $request)
    {
        $machine = new Machine;

        $owns = $machine->getUserOwnedMachines();

        $flags = array();

        $ownedTags = array();

        if ($owns->isEmpty()) {

            return Machine::all()->random(5);
        }

        foreach ($owns as $own) {

            $own->load('flag');
            array_push($flags, $own->flag);

            foreach ($flags as $flag) {

                $tags = $flag->tags;

                foreach ($tags as $tag) {

                    array_push($ownedTags, $tag->id);
                }
            }
        }

        $values = array_count_values($ownedTags);

        arsort($values);

        $popular = array_slice(array_keys($values), 0, 3, true);

        $ownedMachineids = $owns->pluck('machine_id')->unique();

        $machines = $machine->getMachineByTags($popular, true, $ownedMachineids);

        $machines->load(['creator', 'creator.scoreGeneral', 'blood', 'blood.scoreGeneral', 'flags', 'flags.tags', 'attachments']);

        return MachineResource::collection($machines)->collection;
    }

    public function dashboardRecommendedMachines()
    {
        $machines = Machine::orderBy('release_at', 'DESC')->take(2)->get();
        
        $machines->load(['creator', 'creator.scoreGeneral', 'blood', 'blood.scoreGeneral', 'flags', 'flags.tags', 'attachments']);

        return MachineResource::collection($machines)->collection;
    }
}