<?php

namespace App\Http\Resources\Collections;

use App\Http\Resources\Userland\UserResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ChallengeCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'data' => $this->collection->map(
                function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'description' => $item->description,
                        'type' => $item->type,
                        'first_blood' => $this->when($item->blooder_id !== null, (new UserResource($item->blood))),
                        'dificulty' => $item->dificulty,
                        'flags' => $item->flags->count(),
                        'completion_status' => $item->getUserChallengeStatus(),
                        'is_freemium' => $item->is_freemium,
                    ];
                },

            )
        ];
    }
}
