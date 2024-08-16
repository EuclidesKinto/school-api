<?php

namespace App\Http\Controllers\Api\Userland;

use App\Http\Controllers\Controller;
use App\Http\Resources\Userland\MachineResource;
use Illuminate\Http\Request;
use App\Models\Machine;
use App\Models\Course;

class NewsController extends Controller
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
        //
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

    public function showEvents()
    {

        $machineDefault = Machine::where('type', 'default')->latest()->firstOrFail();

        $machineDefault->load(['creator', 'creator.scoreGeneral', 'blood', 'blood.scoreGeneral', 'flags', 'flags.tags', 'attachments']);

        $machineTraining = Machine::where('type', 'training')->latest()->firstOrFail();

        $machineTraining->load(['creator', 'creator.scoreGeneral', 'blood', 'blood.scoreGeneral', 'flags', 'flags.tags', 'attachments']);

        $courses = Course::latest()->take(3)->get();

        return [
            'machines' => [new MachineResource($machineDefault), new MachineResource($machineTraining)],
            'courses' => $courses
        ];
    }
}