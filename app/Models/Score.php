<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['idUsers', 'idRoom', 'score'])]
class Score extends Model
{
    protected $table = 'score';

    // Relasi Inverse ke User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idUsers', 'id');
    }

    // Relasi Inverse ke Room
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'idRoom', 'id');
    }
}