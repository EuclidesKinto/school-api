<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Quizz extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'quizzes';

    protected $fillable = [
        'quizzable_id',
        'quizzable_type',
        'title'
    ];

    protected $dates = ['deleted_at'];

    public function quizzable()
    {
        return $this->morphTo();
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'quizz_id');
    }
}
