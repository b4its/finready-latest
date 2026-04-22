<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['idUsers', 'idAkunKeuangan', 'periode', 'lampiran', 'keterangan', 'keterangan_lain', 'tipe'])]
class JurnalUmum extends Model
{
    protected $table = 'jurnal_umum';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idUsers', 'id');
    }
    public function akunKeuangan(): BelongsTo
    {
        return $this->belongsTo(AkunKeuangan::class, 'idAkunKeuangan', 'id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailJurnalUmum::class, 'idJurnalUmum', 'id');
    }

}


            // $table->foreignId('idUsers')->nullable()->constrained('users')->onDelete('cascade');
            // $table->foreignId('idAkunKeuangan')->nullable()->constrained('akun_keuangan')->onDelete('cascade');
            // $table->date('periode')->nullable();
            // $table->string('lampiran')->nullable();
            // $table->text('keterangan')->nullable();
            // $table->text('keterangan_lain')->nullable();