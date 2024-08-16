<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

class Profile extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'surname',
        'nickname',
        'avatar',
        'bio',
        'metadata'
    ];

    protected $casts = [
        'metadata' => AsArrayObject::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
