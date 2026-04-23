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
        Schema::create('saldo_awal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idUsers')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('idDetailAkunKeuangan')->nullable()->constrained('detail_akun_keuangan')->onDelete('cascade');
            $table->decimal('debet', 25, 2)->default(0);
            $table->decimal('kredit', 25, 2)->default(0);
            $table->tinyInteger('tipe')->default(0)->comment("0. Referensi, 1. Praktek, 2. Akun Riil");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saldo_awal');
    }
};
