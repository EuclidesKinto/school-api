<?php

namespace App\Http\Controllers\Api\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Http\Resources\Userland\CourseResource;
use Illuminate\Support\Facades\Storage;

class CoursesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // get all courses
        $courses = Course::paginate(10);

        return $courses;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // create a new course and save it to the database
        $course = $request->only([
            'name',
            'description',
            'image_url',
            'active',
            'metadata',
        ]);

        $course['active'] = 1;

        $course = Course::create($course);

        return response()->json([
            'success' => true,
            'message' => 'Course created successfully',
            'course' => $course,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // get a single course
        $course = Course::findOrFail($id);

        $course->load([
            'modules.lessons',
            'modules.lessons.tags',
            'modules.lessons.challenges',
            'modules.lessons.challenges.blood',
            'modules.lessons.challenges.blood.scoreGeneral',
            'modules.lessons.challenges.flags',
            'modules.lessons.challenges.flags.tags',
            'modules.lessons.challenges.quizzes',
            'modules.lessons.challenges.quizzes.questions',
            'modules.lessons.challenges.quizzes.questions.answers',
            'modules.lessons.hacktivities',
            'modules.lessons.attachments',
            'modules.lessons.quizzes',
            'modules.lessons.quizzes.questions',
            'modules.lessons.quizzes.questions.answers'
        ]);

        return new CourseResource($course);
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
        // update a course
        $course = Course::findOrFail($id);

        $course->update($request->only([
            'name',
            'description',
            'image_url',
            'active',
            'metadata',
        ]));
        return response()->json([
            'success' => true,
            'message' => 'Course updated successfully!',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // delete a course using softDeletes
        $course = Course::findOrFail($id);

        $course->delete();

        return response()->json(['message' => 'Course deleted successfully'], 200);
    }

    public function addCourseAvatar(Request $request, Course $course)
    {
        $uniqid = uniqid(rand(), true);

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        try {

            Storage::disk('s3')->put('course/avatar/' . $uniqid, $request->image->get(), 'public');

            $path = Storage::disk('s3')->url('course/avatar/' . $uniqid);

            Course::where('id', $course->id)->update(['image_url' => $path]);

            return response(['success' => true], 201);
        } catch (\Exception $th) {

            return response(['failure' => false], 500);
        }
    }

    public function signVideoUrl()
    {

        $uniqid = uniqid(rand(), true);

        $s3 = Storage::disk('s3');
        $client = $s3->getDriver()->getAdapter()->getClient();
        $expiry = "+1 days";

        $options = ['user-data' => 'user-meta-value'];

        $cmd = $client->getCommand('PutObject', [
            'Bucket' => env('AWS_BUCKET'),
            'Key' => 'courses/videos/' . $uniqid,
            'ACL' => 'public-read',
            'Metadata' => $options,
        ]);

        $request = $client->createPresignedRequest($cmd, $expiry);

        $presignedUrl = (string)$request->getUri();

        return response([
            'success' => true,
            'url' => $presignedUrl,
            'uniqId' => $uniqid
        ]);
    }
}
