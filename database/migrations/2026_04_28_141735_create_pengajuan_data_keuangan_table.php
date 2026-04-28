<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengajuan_data_keuangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idUsers')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('umkm_target')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->text('keterangan')->nullable();
            $table->tinyInteger('status_pengajuan')->default(0)->comment("0. Tidak Diterima, 1. Diterima");
            $table->datetime('waktu_pertemuan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_data_keuangan');
    }
};
