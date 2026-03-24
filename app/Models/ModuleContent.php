<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleContent extends Model
{
    protected $table = 'module_content';

    protected $fillable = [
        'idModule',
        'title',
        'content',
        'type',
        'url',
    ];

    // Relasi Inverse ke Module
    public function module()
    {
        return $this->belongsTo(Modul::class, 'idModule', 'id');
    }
}