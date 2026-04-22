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
        Schema::create('detail_akun_keuangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idUsers')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('idAkunKeuangan')->nullable()->constrained('akun_keuangan')->onDelete('cascade');
            $table->char('is_debet', 1)->nullable()->comment("D: Debet| K: Kredit");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_akun_keuangan');
    }
};
