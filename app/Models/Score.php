<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    protected $table = 'score';

    protected $fillable = [
        'idUsers',
        'idRoom',
        'score',
    ];

    // Relasi Inverse ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'idUsers', 'id');
    }

    // Relasi Inverse ke Room
    public function room()
    {
        return $this->belongsTo(Room::class, 'idRoom', 'id');
    }
}