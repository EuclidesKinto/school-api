<?php

namespace App\Http\Resources\Userland;

use App\Models\Reaction;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Userland\CommentResource;

class HacktivityResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $reaction = new Reaction();

        return [
            'id' => $this->id,
            'headline' => $this->headline,
            'type' => $this->type,
            'subject_type' => $this->subject_type,
            'subject_id' => $this->subject_id,
            'module_id' => $this->subject->module_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
            'reaction' => $this->when($this->reactions !== null, ($this->getResourceReactions($this))),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'is_fixed' => $this->is_fixed,
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
