<?php

namespace App\Http\Resources\Userland;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PublicProfileResource extends JsonResource
{


    protected $userTimeLine;
    protected $userTagsOwned;
    protected $userCertificates;

    public function __construct($resource, $userTimeLine = [], $userTagsOwned = [], $userCertificates)
    {
        parent::__construct($resource);
        $this->userTimeLine = $userTimeLine;
        $this->userTagsOwned = $userTagsOwned;
        $this->userCertificates = $userCertificates;
    }

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
            'github_url' => $this->github_url,
            'linkedin_url' => $this->linkedin_url,
            'cpf' => $this->when($is_user_logged or Auth::user()->is_admin, $this->cpf),
            'machines_completed' => $this->owned_machines,
            'challenges_completed' => $this->owned_challenges,
            'scoreboard_position' => $this->getUserCompetitiveScoreboardPosition(),
            'score' => new ScoreResource($this->whenLoaded('scoreGeneral')),
            'total_first_bloods' => $this->blooders->count(),
            'modules_completed' => $this->modulesFinishedCounts ?? 0,
            'lessons_completed' => $this->lessons->count(),
            'timeline'=> $this->userTimeLine ?? [],
            'user_patent' => $this->when($this->relationLoaded('getPatent'), function () {
                return $this->getUserPatent($this->getGeralPatent(true));
            }),
            'certificates' => $this->userCertificates,
            'tags_owned' => $this->userTagsOwned ?? [],
        ];
    }
}
