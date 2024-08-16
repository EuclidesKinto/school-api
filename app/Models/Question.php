<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'quizz_id',
        'text',
        'answer_id'
    ];

    public function quizz()
    {
        return $this->belongsTo(Quizz::class, 'quizz_id');
    }

    // relacionamento com a repsosta correta
    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }

    // relacionamento com todas as possíveis respostas (caso de múltipla escolha)
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
