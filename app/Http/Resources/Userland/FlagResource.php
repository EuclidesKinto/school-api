<?php

namespace App\Http\Resources\Userland;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use App\Models\Machine;
use Carbon\Carbon;

class FlagResource extends JsonResource
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
            'id' => $this->when(Auth::user()->hasRole('admin'), $this->id),
            'dificulty' => $this->dificulty,
            'flag' => $this->when(Auth::user()->hasRole('admin'), $this->flag),
            'points' => $this->points,
            'tags' => TagResource::collection($this->whenLoaded('tags')),
        ];
    }
}
