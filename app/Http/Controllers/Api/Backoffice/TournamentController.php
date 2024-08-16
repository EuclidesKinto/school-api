<?php

namespace App\Http\Controllers\Api\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tournament;

class TournamentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tournaments = Tournament::paginate(10);

        return $tournaments;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $tournament = $request->only([
            'name',
            'description',
            'parent_id',
            'begin_at',
            'finish_at',
        ]);

        try {
            $tournament = Tournament::create($tournament);

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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tournament = Tournament::findOrFail($id);

        return $tournament;
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
        $tournament = Tournament::findOrFail($id);

        $tournament->fill($request->only([
            'name',
            'description',
            'parent_id',
            'begin_at',
            'finish_at',
        ]));

        try {
            $tournament->save();

            return response()->json([
                "success" => true,
            ]);
        } catch (\Exception $th) {

            return response()->json([
                "success" => false
            ]);
        }
    }

    public function destroy($id)
    {
        $tournament = Tournament::find($id);

        try {
            $tournament->delete();

            return response()->json([
                "success" => true,
            ]);
        } catch (\Exception $th) {

            return response()->json([
                "success" => false
            ]);
        }
    }

    public function indexRemoved()
    {
        try {

            return Tournament::onlyTrashed()->get();
        } catch (\Exception $th) {

            return response()->json([
                "success" => false
            ]);
        }
    }

    public function showRemoved($id)
    {
        try {

            return Tournament::onlyTrashed()->where("id", "=", $id)->get();
        } catch (\Exception $th) {

            return response()->json([
                "success" => false
            ]);
        }
    }
}
