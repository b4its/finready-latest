<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['idUsers', 'name', 'jenisUsaha', 'nib', 'phone', 'alamat', 'modal_awal'])]
class UmkmProfile extends Model
{
    protected $table = 'umkm_profile';

    // Relasi Inverse ke User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idUsers', 'id');
    }

    // Tambahkan Relasi HasMany ke SosialMedia
    public function sosialMedia(): HasMany
    {
        return $this->hasMany(SosialMedia::class, 'idUmkmProfile', 'id');
    }
}