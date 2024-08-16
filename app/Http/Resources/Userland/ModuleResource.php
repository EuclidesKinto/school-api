<?php

namespace App\Http\Resources\Userland;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ModuleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'active' => $this->active,
            'metadata' => $this->metadata,
            'lessons' => LessonResource::collection($this->whenLoaded('lessons')),
            'in_progress' => $this->getModuleInProgress($this),
            'is_freemium' => $this->course->is_freemium,
            'image_url' => ($this->image_url)?Storage::disk('s3')->temporaryUrl($this->image_url, now()->addMinutes(60)):null,
        ];
    }
}
