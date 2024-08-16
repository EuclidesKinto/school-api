<?php

namespace App\Http\Resources\Userland;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class QuestionResource extends JsonResource
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
            'text' => $this->text,
            'answer' => $this->when((Auth::user()->hasRole('admin')), AnswerResource::collection($this->whenLoaded('answers'))),
        ];
    }
}
