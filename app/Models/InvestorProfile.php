<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestorProfile extends Model
{
    //
    protected $table = 'investor_profile'; // Deklarasi nama tabel karena singular

    protected $fillable = [
        'idUsers',
        'name',
    ];

    // Relasi Inverse ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'idUsers', 'id');
    }
}
