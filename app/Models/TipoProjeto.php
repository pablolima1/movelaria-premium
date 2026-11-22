<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoProjeto extends Model
{
    protected $table = 'tipos_projeto';

    protected $fillable = [
        'nome',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
        ];
    }

    public function projetos(): HasMany
    {
        return $this->hasMany(Projeto::class, 'tipo_projeto_id');
    }
}
