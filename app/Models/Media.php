<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Media extends Model
{
    use HasFactory;

    protected $table = 'medias';

    protected $fillable = [
        'filename',
        'mediable_id',
        'mediable_type',
        'attribute'
    ];

    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }
}
