<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OldScore extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'flag_id',
        'imported',
        'origin_type',
        'origin_id',
        'resource',
        'flag',
    ];

}
