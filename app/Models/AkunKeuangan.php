<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'category'])]

class AkunKeuangan extends Model
{
    //
    protected $table = 'akun_keuangan'; // Deklarasi nama tabel karena singular
}
