<?php

namespace App\Http\Resources\Userland;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $is_user_logged = (Auth::user()->id == $this->id);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'nick' => $this->nick,
            'country' => $this->country,
            'language' => $this->when($is_user_logged, $this->language),
            'email' => $this->when($is_user_logged or Auth::user()->is_admin, $this->email),
            'username' => $this->when($is_user_logged, $this->nick),
            'avatar' => $this->avatar,
            'bio' => $this->bio,
            'cpf' => $this->when($is_user_logged or Auth::user()->is_admin, $this->cpf),
            'instance' => $this->when($is_user_logged, $this->instance),
            'instanceChallenge' => $this->when($is_user_logged, $this->instanceChallenge),
            'is_premium' => $this->when($is_user_logged && $this->relationLoaded('subscriptionPremium'), function () {
                return isset($this->subscriptionPremium) || Auth::user()->is_admin;
            }),
            'is_admin' => $this->is_admin,
            'user_activities' => [
                'machines_completed' => $this->owned_machines,
                'owned_tags' => $this->when($this->relationLoaded('owns.flag.tags'), function () {
                    return $this->owned_tags;
                }),
            ],
            'score' => new ScoreResource($this->whenLoaded('scoreGeneral')),
            'user_patent' => $this->when($this->relationLoaded('getPatent'), function () {
                return $this->getUserPatent($this->getPatent);
            }),
        ];
    }
}
