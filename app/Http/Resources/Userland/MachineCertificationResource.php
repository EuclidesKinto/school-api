<?php

namespace App\Http\Resources\Userland;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class MachineCertificationResource extends JsonResource
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
            'dificulty' => $this->dificulty,
            'active' => $this->when(Auth::user()->is_admin, $this->active),
            'photo_path' => $this->photo_path,
            'instance' => $this->whenLoaded('instanceActive'),
            'tags' => $this->when($this->type == 'training', $this->tags),
            'xp' => $this->total_points,
            'total_flags' => $this->getTotalFlags(),
            'release_at' => $this->release_at,
        ];
    }
}
