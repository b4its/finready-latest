<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['idUsers', 'no_faktur', 'periode', 'metode_pembayaran', 'lampiran', 'keterangan', 'keterangan_lain'])]
class JurnalUmum extends Model
{
    protected $table = 'jurnal_umum';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idUsers', 'id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailJurnalUmum::class, 'idJurnalUmum', 'id');
    }
}