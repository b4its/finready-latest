<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $table = 'profile'; // Deklarasi nama tabel karena singular

    protected $fillable = [
        'idUsers',
        'email',
        'phone',
        'alamat',
        'level',
    ];

    // Relasi Inverse ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'idUsers', 'id');
    }
}