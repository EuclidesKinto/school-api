<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_id',
        'question_id',
        'answer_id',
        'correct'
    ];

    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }
}
