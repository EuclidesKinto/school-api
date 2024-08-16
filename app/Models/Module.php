<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'active',
        'metadata',
    ];

    protected $dates = ['deleted_at'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->where('active', true);
    }

    public function getModuleInProgress($module)
    {

        $lessons = $module->lessons()->get();

        foreach ($lessons as $lesson) {

            if ($lesson->getLessonStartedByUser($lesson->id)) {
                return true;
            }
        }

        return false;
    }
}
