<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'category', 'detail_category'])]

class AkunKeuangan extends Model
{
    //
    protected $table = 'akun_keuangan'; // Deklarasi nama tabel karena singular

    protected $fillable = ['name', 'category', 'detail_category'];
}
