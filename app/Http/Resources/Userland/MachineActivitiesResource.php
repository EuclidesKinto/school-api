<?php

namespace App\Http\Resources\Userland;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class MachineActivitiesResource extends JsonResource
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
            'scoreboard' => $this->scoreboard(),
            'activities' => $this->activities(),
        ];
    }
    public function activities()
    {
        $owns =  $this->owns()->with(['user:id,name,nick,profile_photo_path', 'flag:id,dificulty,points'])->orderby('created_at', 'desc')->get();

        $owns->map(function ($own) {

            if ($own->user) {
                $id = $own->user->id;
                unset($own->user);
                $own->user = (new UserResource(User::find($id)));
                unset($own->user->id);
            }

            unset($own->flag_id);
            unset($own->user_id);
            unset($own->updated_at);
            unset($own->machine_id);
            unset($own->instance_id);
            unset($own->tournament_id);
            unset($own->laravel_through_key);
            unset($own->flag->id);

            $own->flag->points = $own->flag->points ? $own->flag->points : 0;
            return $own;
        });
        return $owns;
    }
    public function scoreboard()
    {
        //get from database all users was get all flags of this current machine 
        $owns = $this->owns()->with(['user:id,name,nick,profile_photo_path'])->orderby('created_at', 'desc')->get();
        $leaderboard = [];
        foreach ($owns as $own) {

            if ($own->user) {
                if (!isset($leaderboard[$own->user->id])) {
                    $leaderboard[$own->user_id] = $own->points;
                } else {
                    $leaderboard[$own->user_id] = $leaderboard[$own->user_id] + $own->points;
                }
            }
        }

        // verify of user get all flags of this current machine

        $final_scoreboard = [];
        foreach ($leaderboard as $user_id => $value) {
            // check if user complete all flags of this machine
            $user = User::find($user_id);
            $current_user_percentage = $this->getUserProgress($user);
            if ($current_user_percentage == 100) {
                $final_scoreboard[] = [
                    'user' => (new UserResource($user)),
                    'time_to_complete' => $this->release_at->diffInSeconds($this->owns()->where('user_id', $user_id)->orderby('created_at', 'desc')->first()->created_at),
                    'points' => $value,
                ];
            }
        }
        $final_scoreboard = collect($final_scoreboard)->sortBy('time_to_complete')->values()->take(10);
        return $final_scoreboard;
    }
}
