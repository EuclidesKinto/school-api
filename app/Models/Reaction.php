<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'reactable_id',
        'reactable_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reactable()
    {
        return $this->morphTo();
    }

    public function getUserNick($id){
        $user = User::find($id);
        return $user->nick ? $user->nick : $user->name;
    }

   
}
