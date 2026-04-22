<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['idJurnalUmum', 'no_faktur','is_debet', 'amount', 'metode_pembayaran', 'keterangan'])]
class DetailJurnalUmum extends Model
{
    protected $table = 'detail_jurnal_umum';

    public function jurnalUmum(): BelongsTo
    {
        return $this->belongsTo(JurnalUmum::class, 'idJurnalUmum', 'id');
    }
}

