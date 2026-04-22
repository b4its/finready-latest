<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['idUsers','idDetailAkunKeuangan', 'debet', 'kredit'])]
class SaldoAwal extends Model
{
    protected $table = 'saldo_awal';


    public function detailAkunKeuangan(): BelongsTo
    {
        return $this->belongsTo(DetailAkunKeuangan::class, 'idDetailAkunKeuangan', 'id');
    }
    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idUsers', 'id');
    }

}