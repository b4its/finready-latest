<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['idModule', 'name', 'duration'])]
class Room extends Model
{
    protected $table = 'room';

    // Relasi Inverse ke Module
    public function module(): BelongsTo
    {
        return $this->belongsTo(Modul::class, 'idModule', 'id');
    }

    // Relasi One-to-Many ke Question
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'idRoom', 'id');
    }
    

    // Relasi One-to-Many ke Score
    public function scores(): HasMany
    {
        return $this->hasMany(Score::class, 'idRoom', 'id');
    }
}