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
        Schema::create('detail_jurnal_umum', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idJurnalUmum')->nullable()->constrained('jurnal_umum')->onDelete('cascade');
            $table->string('no_faktur')->nullable();
            $table->char('is_debet', 1)->nullable()->comment("D: Debet| K: Kredit");
            $table->decimal('amount', 25, 2)->default(0);
            $table->string('metode_pembayaran')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_jurnal_umum');
    }
};
