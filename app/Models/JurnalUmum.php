<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'idUsers',
    'idAkunKeuangan',
    'periode',
    'no_referensi',
    'no_faktur',
    'is_debet',
    'metode_pembayaran',
    'pihak_terkait',
    'amount',
    'lampiran',
    'keterangan',
])]
class JurnalUmum extends Model
{
    use HasFactory;

    // Mendeklarasikan nama tabel secara eksplisit karena bukan standar plural (jurnal_umums)
    protected $table = 'jurnal_umum';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'periode' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    /**
     * Relasi Many-to-One dengan User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idUsers', 'id');
    }

    /**
     * Relasi Many-to-One dengan AkunKeuangan
     */
    public function akunKeuangan(): BelongsTo
    {
        return $this->belongsTo(AkunKeuangan::class, 'idAkunKeuangan', 'id');
    }
}