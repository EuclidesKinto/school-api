<?php

namespace App\Http\Resources\Userland;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class QuizzResource extends JsonResource
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
            'title' => $this->title,
            'question' => QuestionResource::collection($this->whenLoaded('questions')),
        ];
    }
}
