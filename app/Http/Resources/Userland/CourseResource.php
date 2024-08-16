<?php

namespace App\Http\Resources\Userland;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CourseResource extends JsonResource
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
            'description' => $this->description,
            'image_url' => ($this->image_url)?Storage::disk('s3')->temporaryUrl($this->image_url, now()->addMinutes(60)):null,
            'active' => $this->active,
            'metadata' => $this->metadata,
            'modules' => ModuleResource::collection($this->whenLoaded('modules')),
            'user_status' => $this->checkCourseUserStatus($this->id),
            'is_freemium' => (bool) $this->is_freemium,
        ];
    }
}
