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
        Schema::create('jurnal_umum', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idUsers')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('idAkunKeuangan')->nullable()->constrained('akun_keuangan')->onDelete('cascade');
            $table->date('periode')->nullable();
            $table->string('lampiran')->nullable();
            $table->text('keterangan')->nullable();
            $table->text('keterangan_lain')->nullable();
            $table->tinyInteger('tipe')->default(0)->comment("1. Referensi, 2. Praktek, 3. Akun Riil");
            $table->timestamps();   
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurnal_umum');
    }
};
