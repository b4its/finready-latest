<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UmkmProfile extends Model
{
    //
    protected $table = 'umkm_profile';

    protected $fillable = [
        'idUsers',
        'name',
        'jenisUsaha',
        'nib',
        'phone',
        'alamat',
        'modal_awal',
    ];

    // Relasi Inverse ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'idUsers', 'id');
    }

    // Tambahkan Relasi HasMany ke SosialMedia
    public function sosialMedia()
    {
        return $this->hasMany(SosialMedia::class, 'idUmkmProfile', 'id');
    }
}
