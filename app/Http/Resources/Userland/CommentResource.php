<?php

namespace App\Http\Resources\Userland;

use App\Models\Reaction;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //$reaction = new Reaction;

        return [
            'id' => $this->id,
            'message' => $this->message,
            'reactions' => $this->when($this->reactions !== null, $this->getResourceReactions($this)),
            'commentable_id' => $this->commentable_id,
            'commentable_type' => $this->commentable_type,
            'created_at' => $this->created_at,
            'user' => UserResource::make($this->whenLoaded('user')),
        ];
    }

    public function getResourceReactions($resource)
    {
        $like = $resource->reactions->where('type', 'like');
        $love = $resource->reactions->where('type', 'love');
        $fire = $resource->reactions->where('type', 'fire');
        $eye = $resource->reactions->where('type', 'eye');

        return [
            'like' => $like->count(),
            'love' => $love->count(),
            'fire' => $fire->count(),
            'eye' => $eye->count(),
            'like_by' => $this->getLastNicksByReactionType($like, 'like'),
            'love_by' => $this->getLastNicksByReactionType($love, 'love'),
            'fire_by' => $this->getLastNicksByReactionType($fire, 'fire'),
            'eye_by' => $this->getLastNicksByReactionType($eye, 'eye'),
            'current_user_reaction' => $this->when($this->relationLoaded('reactionsUserAuth'), function () {
                return $this->reactionsUserAuth->type ?? null;
            }),
        ];
    }

    private function getLastNicksByReactionType($reactions, string $type)
    {
        return $reactions->take(-3)
            ->map(function ($reaction) {
                return $reaction->user->nickname ?? $reaction->user->name;
            })
            ->values();
    }
}
