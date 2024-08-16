<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Notifications\Notifiable;

class AnsweredQuestion extends Model
{
    use HasFactory;
    use Notifiable;


    /**
     * The event map for the model.
     *
     * @var array
     */

    protected $fillable = [
        'user_id',
        'course_id',
        'question_id',
        'answer_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
