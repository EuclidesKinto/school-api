<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\MostRecentScope;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'filename',
        'original_name',
        'url',
        'file_size',
        'mime_type',
        'metadata'
    ];

    public function attachable()
    {
        return $this->morphTo();
    }
}
