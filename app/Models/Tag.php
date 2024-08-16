<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;
    use Sluggable;
    use SluggableScopeHelpers;

    protected $fillable = [
        'name',
        'slug'
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * Para entender a magia portrás deste método, veja a doc
     * @see https://github.com/cviebrock/eloquent-sluggable
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    /**
     * Machines relationship
     */
    public function machines()
    {
        return $this->morphedByMany(Machine::class, 'taggable');
    }

    /**
     * Courses relationship
     */
    public function courses()
    {
        return $this->morphedByMany(Course::class, 'taggable');
    }

    /**
     * Flags relationship
     */
    public function flags()
    {
        return $this->morphedByMany(Flag::class, 'taggable');
    }

    /**
     * Modules relationship
     */
    public function modules()
    {
        return $this->morphedByMany(Module::class, 'taggable');
    }

    /**
     * Modules relationship
     */
    public function lessons()
    {
        return $this->morphedByMany(Lesson::class, 'taggable');
    }

    public function challenges()
    {
        return $this->morphedByMany(Challenge::class, 'taggable');
    }

    public function isTag($search)
    {

        if (empty($search)) return false;

        $tag = Tag::where('slug', 'LIKE', '%' . Str::slug($search, '-') . '%')->first();

        if ($tag) {
            return true;
        } else {
            return false;
        }
    }
}
