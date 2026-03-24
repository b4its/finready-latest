<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = 'room';

    protected $fillable = [
        'idModule',
        'name',
    ];

    // Relasi Inverse ke Module
    public function module()
    {
        return $this->belongsTo(Modul::class, 'idModule', 'id');
    }

    // Relasi One-to-Many ke Question
    public function questions()
    {
        return $this->hasMany(Question::class, 'idRoom', 'id');
    }

    // Relasi One-to-Many ke Score
    public function scores()
    {
        return $this->hasMany(Score::class, 'idRoom', 'id');
    }
}