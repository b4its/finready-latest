<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['idUsers', 'name', 'jenisUsaha', 'nib', 'email', 'phone', 'alamat', 'level'])]
class UmkmProfile extends Model
{
    protected $table = 'umkm_profile';

    // Relasi Inverse ke User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idUsers', 'id');
    }

    // Tambahkan Relasi HasMany ke SosialMedia
    public function sosialMedia(): HasMany
    {
        return $this->hasMany(SosialMedia::class, 'idUmkmProfile', 'id');
    }
}


        // Schema::create('umkm_profile', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('idUsers')->nullable()->constrained('users')->onDelete('cascade');
        //     $table->string('name')->nullable();
        //     $table->string('jenisUsaha')->nullable();
        //     $table->string('nib')->nullable();
        //     $table->string('email')->nullable();
        //     $table->string('phone')->nullable();
        //     $table->string('alamat')->nullable();
        //     $table->string('level')->nullable()->default('learning');
        //     $table->decimal('modal_awal', 15, 2)->nullable()->default(0);
        //     $table->timestamps();
        // });