<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['idUsers', 'idAkunKeuangan', 'is_debet'])]
class DetailAkunKeuangan extends Model
{
    protected $table = 'detail_akun_keuangan';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idUsers', 'id');
    }

    public function akunKeuangan(): BelongsTo
    {
        return $this->belongsTo(AkunKeuangan::class, 'idAkunKeuangan', 'id');
    }
}