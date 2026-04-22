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
        Schema::create('praktek_keuangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idUsers')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('idAkunKeuangan')->nullable()->constrained('akun_keuangan')->onDelete('cascade');
            $table->foreignId('idJurnalUmum')->nullable()->constrained('jurnal_umum')->onDelete('cascade');
            $table->string('table_name')->nullable();
            $table->string('title')->nullable();
            $table->string('answer')->nullable();
            $table->tinyInteger('status_answer')->default(0)->comment("0. salah, 1. benar");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('praktek_keuangan');
    }
};
