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
            $table->string('no_referensi')->nullable();
            $table->string('no_faktur')->nullable();
            $table->char('is_debet', 1)->nullable()->comment("D: Debet| K: Kredit");
            $table->string('metode_pembayaran')->nullable();
            $table->decimal('amount', 25, 2)->default(0);
            $table->string('lampiran')->nullable();
            $table->text('keterangan')->nullable();
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
