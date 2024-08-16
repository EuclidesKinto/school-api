<?php

namespace App\Http\Controllers\Api\Userland;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Http\Resources\Userland\HacktivityResource;
use App\Http\Resources\Userland\CommentResource;
use App\Models\Hacktivity;
use App\Models\Lesson;
use App\Models\Reaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class InteractionController extends Controller
{
    public function react(Request $request, $resource, $id)
    {

        $reaction = $request->only([
            'type',
            //like, love, fire ,eye
        ]);

        switch ($resource) {
            case 'comment':
                $resource = 'App\Models\Comment';
                break;
            case 'machine':
                $resource = 'App\Models\Machine';
                break;
            case 'challenge':
                $resource = 'App\Models\Challenge';
                break;
            case 'hacktivity':
                $resource = 'App\Models\Hacktivity';
                break;
            case 'lesson':
                $resource = 'App\Models\Lesson';
                break;
            case 'course':
                $resource = 'App\Models\Course';
                break;
        }

        $user = Auth::user();

        $reaction['user_id'] = $user->id;
        $reaction['reactable_id'] = $id;
        $reaction['reactable_type'] = $resource; //machine, challenge, hacktivity, lesson, course, etc..

        $reactionExists = Reaction::where([['user_id', $user->id], ['reactable_id', $reaction['reactable_id']], ['reactable_type', $reaction['reactable_type']]])->first();

        if ($reactionExists) {

            if ($reactionExists->type == $reaction['type']) {

                $reactionExists->delete();

                return Response()->json([
                    'message' => 'Reaction removed successfully',
                    'reaction' => $reaction
                ], 200);
            }
        }

        $reaction = Reaction::updateOrCreate(
            ['user_id' => $reaction['user_id'], 'reactable_id' => $reaction['reactable_id']],
            ['type' => $reaction['type'], 'reactable_type' => $reaction['reactable_type'], 'reactable_id' => $reaction['reactable_id']],
        );

        $reactable_response = null;

        if ($resource == 'App\Models\Hacktivity') {
            $reaction->reactable->load(['reactions.user','reactionsUserAuth','user', 'user.scoreGeneral', 'comments.user.getPatent', 'comments.reactions.user',  'subject']);
            $reactable_response = new HacktivityResource($reaction->reactable);
        } else if ($resource == 'App\Models\Comment') {
            $reaction->load('user');
            $reactable_response = new CommentResource($reaction->reactable);
        } else {
            $reactable_response = $reaction->reactable;
        }


        return Response()->json([
            'message' => 'Reaction added successfully',
            'reaction' => $reaction,
            'reactable' => $reactable_response
        ], 200);
    }

    public function comment(Request $request, $resource, $id)
    {
        switch ($resource) {
            case 'hacktivity':
                $commentable = Hacktivity::findOrFail($id);
                break;
            case 'lesson':
                $commentable = Lesson::findOrFail($id);
                break;
            default:
                return response(['error'], 404);
        }

        $user = Auth::user();
        $comment = new Comment();
        $comment->message = $request->message;
        $comment->user_id = $user->id;
        $comment->commentable()->associate($commentable);
        $comment->save();
        $comment->load('user');
        return new CommentResource($comment);
    }

    public function showComment(Request $request, $resource, $id)
    {
        switch ($resource) {
            case 'hacktivity':
                $resource = 'App\Models\Hacktivity';
                break;
            case 'lesson':
                $resource = 'App\Models\Lesson';
                break;
        }


        $comment = Comment::find($id);

        if ($comment && $comment->commentable_type == $resource) {

            $comment->load('user');
            return new CommentResource($comment);
        }

        return response(['error'], 404);
    }

    public function deleteComment(Request $request, $resource, $id)
    {
        switch ($resource) {
            case 'hacktivity':
                $resource = 'App\Models\Hacktivity';
                break;
            case 'lesson':
                $resource = 'App\Models\Lesson';
                break;
        }
        $comment = Comment::find($id);

        if ($comment->user_id !== Auth::user()->id && !Auth::user()->hasRole('admin')) {

            return response(['error', 'IDOR attempted - this attack has been recorded!'], 403);
        }

        if ($comment && $comment->commentable_type == $resource) {
            $comment->delete();
            return response(['success'], 200);
        }

        return response(['error'], 404);
    }
}