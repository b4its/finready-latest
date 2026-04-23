<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['idUsers', 'idAkunKeuangan', 'idDetailAkunKeuangan', 'idSaldoAwal','idJurnalUmum', 'table_name', 'title', 'answer', 'status_answer'])]
class PraktekKeuangan extends Model
{
    //
    protected $table = 'praktek_keuangan';

    // Relasi Inverse ke Room
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idUsers', 'id');
    }

    public function akunKeuangan(): BelongsTo
    {
        return $this->belongsTo(AkunKeuangan::class, 'idAkunKeuangan', 'id');
    }
    public function detailAkunKeuangan(): BelongsTo
    {
        return $this->belongsTo(DetailAkunKeuangan::class, 'idDetailAkunKeuangan', 'id');
    }

    public function jurnalUmum(): BelongsTo
    {
        return $this->belongsTo(JurnalUmum::class, 'idJurnalUmum', 'id');
    }
}


        // Schema::create('praktek_keuangan', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('idUsers')->nullable()->constrained('users')->onDelete('cascade');
        //     $table->foreignId('idAkunKeuangan')->nullable()->constrained('akun_keuangan')->onDelete('cascade');
        //     $table->foreignId('idJurnalUmum')->nullable()->constrained('jurnal_umum')->onDelete('cascade');
        //     $table->string('table_name')->nullable();
        //     $table->string('title')->nullable();
        //     $table->string('answer')->nullable();
        //     $table->tinyInteger('status')->default(0)->comment("0. salah, 1. benar");
        //     $table->timestamps();
        // });