<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['idUsers', 'idModul', 'idModulContent', 'idRoom','title', 'type', 'contents', 'point'])]
class LearnProgress extends Model
{
    protected $table = 'learn_progress';

    // Relasi Inverse ke User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idUsers', 'id');
    }

    // Relasi Inverse ke Modul
    public function modul(): BelongsTo
    {
        return $this->belongsTo(Modul::class, 'idModul', 'id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'idRoom', 'id');
    }

    // Relasi Inverse ke ModuleContent
    public function moduleContent(): BelongsTo
    {
        return $this->belongsTo(ModuleContent::class, 'idModulContent', 'id');
    }
}