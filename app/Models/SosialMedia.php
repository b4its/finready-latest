<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['idUmkmProfile', 'name', 'link'])]
class SosialMedia extends Model
{
    protected $table = 'sosial_media';

    // Relasi Inverse ke UmkmProfile
    public function umkmProfile(): BelongsTo
    {
        return $this->belongsTo(UmkmProfile::class, 'idUmkmProfile', 'id');
    }
}