<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['idModule', 'title', 'content', 'type', 'url'])]
class ModuleContent extends Model
{
    protected $table = 'module_content';

    // Relasi Inverse ke Module
    public function module(): BelongsTo
    {
        return $this->belongsTo(Modul::class, 'idModule', 'id');
    }
}