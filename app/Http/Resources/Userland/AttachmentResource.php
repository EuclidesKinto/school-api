<?php

namespace App\Http\Resources\Userland;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AttachmentResource extends JsonResource
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
            'name' => $this->name,
            'url' => $this->getUrl(),
            'type' => $this->type,
        ];
    }

    public function getUrl()
    {
        if ($this->type == 'link') {
            return $this->url;
        } else {
            return Storage::disk('s3')->temporaryUrl($this->url, now()->addMinutes(60));
        }
    }
}
