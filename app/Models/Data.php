<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Data extends Model
{
    use HasFactory;

    protected $table = 'datas';

    protected $fillable = ['nama', 'alamat'];

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }
}
