<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Machine;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'user_id',
        'achievable_id',
        'achievable_type',
        'time_to_complete',
        'image_path',
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function achievable()
    {
        return $this->morphTo();
    }

    public function getPosition($achievableId)
    {

        $machine = Machine::find($achievableId);

        $owns = $machine->owns()->with(['user:id,name,nick,profile_photo_path'])->orderby('created_at', 'desc')->get();

        $leaderboard = [];

        foreach ($owns as $own) {
            if (!isset($leaderboard[$own->user->id])) {
                $leaderboard[$own->user_id] = $own->points;
            } else {
                $leaderboard[$own->user_id] = $leaderboard[$own->user_id] + $own->points;
            }
        }

        $final_scoreboard = [];

        foreach ($leaderboard as $userId => $value) {

            $user = User::find($userId);

            $current_user_percentage = $machine->getUserProgress($user);

            if ($current_user_percentage == 100) {

                $final_scoreboard[] = [
                    'user' => $user,
                    'time_to_complete' => $machine->release_at->diffInSeconds($machine->owns()->where('user_id', $userId)->orderby('created_at', 'desc')->first()->created_at),
                    'points' => $value,
                ];
            }
        }

        $final_scoreboard = collect($final_scoreboard)->sortBy('time_to_complete')->values();

        $position = $final_scoreboard->search(function ($value) {
            $user = Auth::user();
            return $value['user']->id == $user->id;
        }) + 1;

        return $position;
    }

    public function getImageUrl($position)
    {
        $title = 'Parabéns, você completou a máquina';

        // create achievement image
        $image = App::make('snappy.image.wrapper');

        $rawImage = $image->getOutputFromHtml(view('achievement.show', ['resource' => $this, 'title' => $title, 'position' => $position])->render());

        // save and get path
        $uniqid = uniqid(rand(), true);

        Storage::disk('s3')->put('user/achievement/' . $uniqid, $rawImage, 'public');

        return 'user/achievement/' . $uniqid;
    }
}
