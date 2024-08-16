<?php

namespace App\Http\Resources\Userland;

use App\Http\Resources\Collections\MachineCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Userland\ChallengeResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LessonResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'image_url' => ($this->image_url)?Storage::disk('s3')->temporaryUrl($this->image_url, now()->addMinutes(60)):null,
            'active' => $this->active,
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'quizzes' => QuizzResource::collection($this->whenLoaded('quizzes')),
            'challenges' => ChallengeResource::collection($this->whenLoaded('challenges')),
            'video_url' => $this->when(Auth::user()->is_admin || $user->is_premium() || $this->module->course->is_freemium, $this->video_unique_id . '&watermark=' . \App\Services\PandaVideo\JwtVideo::GenerateSignedUrl($this->video_unique_id)), 
            'hacktivities' => $this->when(Auth::user()->is_admin || $user->is_premium() || $this->module->course->is_freemium, (HacktivityResource::collection($this->hacktivities))),
            'is_started' => $this->getLessonStartedByUser($this->id),
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'links' => $this->links,
        ];
    }
}
