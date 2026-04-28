<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['idUsers', 'umkm_target','title', 'keterangan', 'status_pengajuan'])]
class PengajuanDataKeuangan extends Model
{
    //
    protected $table = 'pengajuan_data_keuangan';

    // Relasi Inverse ke Module
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idUsers', 'id');
    }
    public function umkmTarget(): BelongsTo
    {
        return $this->belongsTo(User::class, 'umkm_target', 'id');
    }
}

