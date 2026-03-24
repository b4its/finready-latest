<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modul extends Model
{
    protected $table = 'modul';

    protected $fillable = [
        'name',
        'description',
        'type',
        'max_point'
    ];

    // Relasi One-to-Many ke ModuleContent
    public function contents()
    {
        return $this->hasMany(ModuleContent::class, 'idModul', 'id');
    }

    // Relasi One-to-Many ke Room
    public function rooms()
    {
        return $this->hasMany(Room::class, 'idModul', 'id');
    }
}