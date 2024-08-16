<?php

namespace App\Http\Controllers\Api\Userland;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class CertificateController extends Controller
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

    public function verify(Request $request)
    {

        $certificateUser = DB::table('certificate_user')->where('validation_id', $request->validation_id)->first();

        if ($certificateUser == null) {
            return response([], 404);
        }

        $user = User::find($certificateUser->user_id);

        $certificate = Certificate::find($certificateUser->certificate_id);

        $data = [
            'certificate_name' => $certificate->name,
            'certificate_description' => $certificate->description,
            'user_name' => $user->name,
            'issue_date' => $certificateUser->created_at,
            'validation_id' => $certificateUser->validation_id,
            'certificate_url' => $certificateUser->url
        ];

        return $data;
    }
}
