<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SosialMedia extends Model
{
    //
    protected $table = 'sosial_media';

    protected $fillable = [
        'idUmkmProfile', // Diperbaiki: sebelumnya idUsers
        'name',
        'link',
    ];

    // Relasi Inverse ke UmkmProfile
    public function umkmProfile()
    {
        return $this->belongsTo(UmkmProfile::class, 'idUmkmProfile', 'id');
    }
}
