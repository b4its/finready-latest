<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi One-to-One dengan Profile
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class, 'idUsers', 'id');
    }

    // Relasi One-to-Many dengan Score
    public function scores(): HasMany
    {
        return $this->hasMany(Score::class, 'idUsers', 'id');
    }

    // Relasi One-to-Many dengan LearnProgress
    public function learnProgresses(): HasMany
    {
        return $this->hasMany(LearnProgress::class, 'idUsers', 'id');
    }
}