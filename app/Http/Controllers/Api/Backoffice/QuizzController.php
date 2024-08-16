<?php

namespace App\Http\Controllers\Api\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quiz;

class QuizzController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $quiz = Quiz::find($id);

        try {
            $quiz->delete();

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
     * Display a listing of soft deleted quizzes.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexRemoved()
    {
        try {

            return Quiz::onlyTrashed()->get();
        } catch (\Exception $th) {

            return response()->json([
                "success" => false
            ]);
        }
    }

    /**
     * Display the specified soft deleted challenge.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showRemoved($id)
    {
        try {

            return Quiz::onlyTrashed()->where("id", "=", $id)->get();
        } catch (\Exception $th) {

            return response()->json([
                "success" => false
            ]);
        }
    }

    /**
     * Unblock specified soft deleted challenge.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restoreQuizzes($id)
    {

        $quiz = Quiz::onlyTrashed()->find($id);

        try {

            $quiz->restore();

            return response()->json([
                "success" => true,
            ]);
        } catch (\Exception $th) {

            return response()->json([
                "success" => false
            ]);
        }
    }
}
