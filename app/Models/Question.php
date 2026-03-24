<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['idRoom', 'question', 'optionA', 'optionB', 'optionC', 'optionD', 'key_answer'])]
class Question extends Model
{
    protected $table = 'question';

    // Relasi Inverse ke Room
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'idRoom', 'id');
    }
}