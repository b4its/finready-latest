<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['idAkunKeuangan', 'idJurnalUmum', 'is_debet', 'amount'])]
class DetailJurnalUmum extends Model
{
    protected $table = 'detail_jurnal_umum';

    public function akunKeuangan(): BelongsTo
    {
        return $this->belongsTo(AkunKeuangan::class, 'idAkunKeuangan', 'id');
    }

    public function jurnalUmum(): BelongsTo
    {
        return $this->belongsTo(JurnalUmum::class, 'idJurnalUmum', 'id');
    }
}