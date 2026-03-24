<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'description', 'type', 'max_point'])]
class Modul extends Model
{
    protected $table = 'modul';

    // Relasi One-to-Many ke ModuleContent
    public function contents(): HasMany
    {
        return $this->hasMany(ModuleContent::class, 'idModul', 'id');
    }

    // Relasi One-to-Many ke Room
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'idModul', 'id');
    }
}