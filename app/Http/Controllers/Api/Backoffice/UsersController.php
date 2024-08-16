<?php

namespace App\Http\Controllers\Api\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {

            return User::get();
        } catch (\Exception $th) {

            return response()->json([
                "success" => false
            ]);
        }
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
        try {

            return User::findOrFail($id);
        } catch (\Exception $th) {

            return response()->json([
                "success" => false
            ]);
        }
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
        $request = $request->getContent();

        $data = json_decode($request);

        $user = User::find($id);

        try {

            $user->name = $data->name;
            $user->email = $data->email;
            $user->password = bcrypt($data->password);
            $user->current_team_id = $data->current_team_id;
            $user->profile_photo_path = $data->profile_photo_path;
            $user->google_id = $data->google_id;
            $user->stripe_id = $data->stripe_id;
            $user->card_brand = $data->card_brand;
            $user->card_last_four = $data->card_last_four;
            $user->trial_ends_at = $data->trial_ends_at;
            $user->bio = $data->bio;
            $user->cpf = $data->cpf;
            $user->nick = $data->nick;
            $user->site = $data->site;
            $user->lock_subscription = $data->lock_subscription;

            $user->save();

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
     * Soft Delete the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);

        try {
            $user->delete();

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
     * Display a listing of soft deleted users.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexBlocked()
    {
        try {

            return User::onlyTrashed()->get();
        } catch (\Exception $th) {

            return response()->json([
                "success" => false
            ]);
        }
    }

    /**
     * Display the specified soft deleted user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showBlocked($id)
    {
        try {

            return User::onlyTrashed()->where("id", "=", $id)->get();
        } catch (\Exception $th) {

            return response()->json([
                "success" => false
            ]);
        }
    }

    /**
     * Unblock specified soft deleted user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function unblockUser($id)
    {

        $user = User::onlyTrashed()->find($id);

        try {

            $user->restore();

            return response()->json([
                "success" => true,
            ]);
        } catch (\Exception $th) {

            return response()->json([
                "success" => false
            ]);
        }
    }

    public function list()
    {

        $users = User::all();

        return ServiceResource::collection($users);
    }

    public function search(Request $request)
    {

        if (!$request->has('email') && !$request->has('gatewayInvoiceId')) {

            return response()->json(['status' => 'error', 'message' => 'bad request'], 400);
        }

        if ($request->has('email')) {

            $user = User::where('email', $request->email);
        }

        if ($request->has('gatewayInvoiceId')) {

            if (!isset($user) || !$user->exists()) {

                $invoice = Invoice::where('payment_gw_id', $request->gatewayInvoiceId)->get();

                $user = User::where('id', $invoice[0]->user_id);
            }
        }

        if ($user->exists()) {

            return response()->json(ServiceResource::collection($user->get()), 200);
        }

        return response()->json(['status' => 'error', 'message' => 'user not found'], 404);
    }
}
