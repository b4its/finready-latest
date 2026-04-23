<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['idUsers', 'no_referensi', 'name', 'category', 'detail_category', 'tipe'])]
class AkunKeuangan extends Model
{
    protected $table = 'akun_keuangan';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idUsers', 'id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailAkunKeuangan::class, 'idAkunKeuangan', 'id');
    }
    
    public function saldoAwal(): HasMany
    {
        return $this->hasMany(SaldoAwal::class, 'idAkunKeuangan', 'id');
    }

    public function detailJurnal(): HasMany
    {
        return $this->hasMany(DetailJurnalUmum::class, 'idAkunKeuangan', 'id');
    }
}