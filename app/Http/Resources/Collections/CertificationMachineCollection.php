<?php

namespace App\Http\Resources\Collections;

use App\Http\Resources\Userland\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CertificationMachineCollection extends JsonResource
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
            'dificulty' => $this->dificulty,
            'flags' => $this->getTotalFlags(),
            'tags' => $this->when($this->type !== "default", $this->tags),
            'xp' => $this->total_points,
            'photo_path' => $this->photo_path,
            'percentage' => $this->getUserProgress($user),
            'is_freemium' => $this->is_freemium,
        ];
    }
}
