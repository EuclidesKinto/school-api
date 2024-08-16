<?php

namespace App\Http\Resources\Collections;

use App\Http\Resources\Userland\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MachineCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
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
            'os_name' => $this->os_name,
            'type' => $this->type,
            'creator' => $this->when($this->creator_id !== null, UserResource::make($this->whenLoaded('creator'))),
            'first_blood' => $this->when($this->blooder_id !== null, UserResource::make($this->whenLoaded('blood'))),
            'dificulty' => $this->dificulty,
            'flags' => $this->getTotalFlags(),
            'tags' => $this->when($this->type !== "default", $this->tags),
            'xp' => $this->total_points,
            'photo_path' => $this->photo_path,
            'percentage' => $this->getUserProgress($user),
            'release_at' => $this->release_at,
            'is_freemium' => $this->is_freemium,
        ];
    }
}
