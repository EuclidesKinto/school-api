<?php

namespace App\Http\Resources\Userland;

use Illuminate\Http\Resources\Json\JsonResource;

class AchievementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        switch ($this->type) {
            case 'completed_resource':
                $title = 'Parabéns! você completou a máquina';
                break;
        }

        return [
            'title' => $title,
            'user' => $this->user_id,
            'resource' =>  $this->achievable_type,
            'time_to_complete' => $this->time_to_complete,
            'image_url' => $this->image_path,
            'first_blood' => $this->when($this->first_blood_points > 0, true),
            'machine_ranking' => $this->position,
        ];
    }
}
