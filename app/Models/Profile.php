<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['idUsers', 'email', 'phone', 'alamat', 'level'])]
class Profile extends Model
{
    protected $table = 'profile'; // Deklarasi nama tabel karena singular

    // Relasi Inverse ke User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idUsers', 'id');
    }
}