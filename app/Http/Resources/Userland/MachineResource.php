<?php

namespace App\Http\Resources\Userland;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class MachineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->when((Auth::user()->is_admin || $this->type == 'training'), $this->description),
            'ami_id' => $this->when(Auth::user()->is_admin, $this->ami_id),
            'os_name' => $this->os_name,
            'type' => $this->type,
            'creator' => new UserResource($this->whenLoaded('creator')),
            'first_blood' => new UserResource($this->whenLoaded('blood')),
            'tournament' => $this->when(Auth::user()->is_admin, $this->tournament_id),
            'dificulty' => $this->dificulty,
            'active' => $this->when(Auth::user()->is_admin, $this->active),
            'is_freemium' => $this->is_freemium,
            'photo_path' => $this->photo_path,
            'percentage' => $this->getUserProgress(Auth::user()),
            'instance' => $this->whenLoaded('instanceActive'),
            'tags' => $this->when($this->type == 'training', $this->tags),
            'xp' => $this->total_points,
            'total_flags' => $this->getTotalFlags(),
            'attachments' => $this->when((Auth::user()->is_premium() && $this->type == 'training'), AttachmentResource::collection($this->whenLoaded('attachments'))),
            'release_at' => $this->release_at,
        ];
    }
}
