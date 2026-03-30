<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['idModule', 'title', 'content', 'type', 'url', 'is_question','document_json'])]
class ModuleContent extends Model
{
    protected $table = 'module_content';
    protected function casts(): array
    {
        return [
            // Beritahu Laravel untuk mengubah array menjadi JSON string saat disimpan, 
            // dan mengubah JSON string kembali menjadi array saat dibaca.
            'document_json' => 'array', 
        ];
    }

    // Relasi Inverse ke Module
    public function module(): BelongsTo
    {
        return $this->belongsTo(Modul::class, 'idModule', 'id');
    }

    // Relasi One-to-Many ke LearnProgress
    public function learnProgresses(): HasMany
    {
        return $this->hasMany(LearnProgress::class, 'idModulContent', 'id');
    }
}