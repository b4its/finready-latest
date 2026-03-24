<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table = 'question';

    protected $fillable = [
        'idRoom',
        'question',
        'optionA',
        'optionB',
        'optionC',
        'optionD',
        'key_answer',
    ];

    // Relasi Inverse ke Room
    public function room()
    {
        return $this->belongsTo(Room::class, 'idRoom', 'id');
    }
}