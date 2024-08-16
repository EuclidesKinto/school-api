<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Own;
use Illuminate\Support\Facades\DB;

class Flag extends Model
{
    use HasFactory;

    protected $fillable = [
        'flaggable_id',
        'flaggable_type',
        'flag',
        'points',
        'dificulty'
    ];

    public function flaggable()
    {
        return $this->morphTo();
    }

    public function owns()
    {
        return $this->hasMany(Own::class);
    }

    /**
     * Tags relationship
     */
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function getFirstBloodPoints($resource)
    {

        $flags = DB::table("flags")->Where("flaggable_type", "=", get_class($resource))->Where("flaggable_id", "=", $resource->id)->get();

        $totalPoints = 0;

        foreach ($flags as $flag) {
            $totalPoints += $flag->points;
        }

        $firstBloodPoints = $totalPoints / 10;

        return $firstBloodPoints;
    }
}
