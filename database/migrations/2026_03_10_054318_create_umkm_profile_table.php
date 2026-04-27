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
        Schema::create('umkm_profile', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idUsers')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('jenisUsaha')->nullable();
            $table->string('nib')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('alamat')->nullable();
            $table->string('level')->nullable()->default('learning')->comment('learning, discipline, structured, ready');
            $table->decimal('modal_awal', 15, 2)->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('umkm_profile');
    }
};
