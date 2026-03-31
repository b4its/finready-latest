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
        Schema::create('learn_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idUsers')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('idModul')->nullable()->constrained('modul')->onDelete('cascade');
            $table->foreignId('idRoom')->nullable()->constrained('room')->onDelete('cascade');
            $table->foreignId('idModulContent')->nullable()->constrained('module_content')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->string('type')->nullable();
            $table->text('contents')->nullable();
            $table->bigInteger('point')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learn_progress');
    }
};
