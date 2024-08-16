<?php

namespace App\Http\Resources\Userland;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ChallengeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = $request->user();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'difficulty' => $this->difficulty,
            'release_at' => $this->release_at,
            'first_blood' => new UserResource($this->whenLoaded('blood')),
            'container_image' => $this->when((Auth::user()->hasRole('admin')), $this->container_image),
            'flags' => $this->when((Auth::user()->is_admin), FlagResource::collection($this->whenLoaded('flags'))),
            'points' => $this->flags->sum('points'),
            'is_solved' => $this->isChallengeCompleted($user->id),
            'quizzes' => QuizzResource::collection($this->whenLoaded('quizzes')),
            'instance' => $this->whenLoaded('instanceActive'),
            'is_freemium' => $this->is_freemium
        ];
    }
}
