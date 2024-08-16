<?php

namespace App\Http\Controllers\Api\Userland;

use App\Http\Controllers\Controller;
use App\Http\Resources\Userland\PublicProfileResource;
use App\Http\Resources\Userland\UserResource;
use App\Models\Certificate;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;

class AccountController extends Controller
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
     * Retrieves the current user profile information
     */
    public function profile(Request $request)
    {
        if ($request->user) {

            $user = User::findOrFail($request->user);

            $user->load(['scoreGeneral']);

            return new UserResource($user);
        } else {

            $user = Auth::user();

            $user->load(['scoreGeneral']);

            return new UserResource($user);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request = $request->getContent();

        $data = json_decode($request);

        $checkNick = User::where('nick', $data->nick)->first();

        if ($checkNick != null && $user->nick != $data->nick) {

            return response()->json([
                "success" => false,
                "message" => 'O nick selecionado já está em uso!'
            ]);
        }

        try {

            User::where("id", $user->id)->update([
                "name" => $data->name,
                "nick" => $data->nick,
                "bio" => $data->bio,
                "country" => $data->country,
                "language" => $data->language,
                "github_url" => $data->github_url,
                "linkedin_url" => $data->linkedin_url,
            ]);

            return response()->json([
                "success" => true,
                "message" => 'Profile Updated',
            ]);
        } catch (\Exception $th) {

            return response()->json([
                "success" => false,
                "message" => 'Something went wrong'
            ]);
        }
    }

    public function addProfileAvatar(Request $request)
    {

        $user = Auth::user();

        $uniqid = uniqid(rand(), true);

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg',
        ]);

        try {

            Storage::disk('s3')->put('user/avatar/' . $uniqid, $request->image->get(), 'public');

            $path = 'https://assets.hackingclub.com/user/avatar/' . $uniqid;

            User::where('id', $user->id)->update(['profile_photo_path' => $path]);

            return response(['success'], 201);
        } catch (\Exception $th) {

            return response(['failure'], 500);
        }
    }

    public function showPublicProfile($id)
    {
        $user = User::find($id);

        $user->load([
            'scoreGeneral',
            'lessons',
            'blooders',
            'certificates'
        ]);

        $userCertificates = DB::table('certificates as c')
            ->leftJoin('certificate_user as cu', function ($join) use ($user) {
                $join->on('cu.certificate_id', '=', 'c.id')
                    ->where('cu.user_id', $user->id);
            })
            ->select(
                'c.name',
                'cu.validation_id',
                DB::raw('(CASE WHEN cu.user_id IS NOT NULL THEN 1 ELSE 0 END) as has_certificate')
            )->get();

        foreach ($userCertificates as $userCertificate) {
            $userCertificate->full_name = $userCertificate->name;
            $userCertificate->name = Str::slug($userCertificate->name);
        }

        $userTimeline = $user->getUserTimeline();
        $userTagsOwned = $user->getTagsOwned();

        return new PublicProfileResource($user, $userTimeline, $userTagsOwned, $userCertificates);
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
     * Check if nick is available
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkNick(Request $request)
    {
        $user = Auth::user();
        $currentNick = User::where('nick', $request->get('nick'))->first();

        if ($currentNick == null || $user->nick == $request->get('nick')) {

            return response()->json([
                "success" => true,
            ]);
        } else {
            return response()->json([
                "success" => false,
            ]);
        }
    }

    public function indexUserCertificates()
    {
        $user = Auth::user();

        $certificates = Certificate::all();

        $userCertificates = [];

        $userCertificate = DB::table('certificates as c')
            ->leftJoin('certificate_user as cu', function ($join) use ($user) {
                $join->on('cu.certificate_id', '=', 'c.id')
                    ->where('cu.user_id', $user->id);
            })
            ->select(
                'cu.validation_id',
                'cu.url',
            )->get();

        $i = 0;
        foreach ($certificates as $certificate) {

            $course = Course::find($certificate->course_id);

            $coursePercentage = $course->getCourseCompletionPercentage($course->id);

            if (!empty($course->getChallenges())) {
                $challengePercentage = $course->getChallengeCompletionPercentage($course->id);
            } else {
                $challengePercentage = 100;
            }

            if ($challengePercentage >= $certificate->percentage_challenges && $coursePercentage >= $certificate->percentage_course) {
                $certificateStatus = 'completed';

                $certificatePercentage = 100;
            } else {
                $certificateStatus = 'in_progress';

                if (!empty($course->getChallenges())) {
                    $certificatePercentage = ($challengePercentage + $coursePercentage) / 2;
                } else {
                    $certificatePercentage  = $coursePercentage;
                }
            }
            array_push($userCertificates, [
                'user_linkedin' => $user->linkedin_url,
                'certificate_name' => $certificate->name,
                'certificate_progress' => $certificatePercentage,
                'certificate_status' => $certificateStatus,
                'certificate_validation_id' => $userCertificate[$i]->validation_id,
                'certificate_url' => $userCertificate[$i]->url,
            ]);
            $i += 1;
        }

        return $userCertificates;
    }
}
