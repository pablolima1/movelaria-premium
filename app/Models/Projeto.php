<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Projeto extends Model
{
    protected $table = 'projetos';

    protected $fillable = [
        'cliente_id',
        'tipo_projeto_id',
        'nome',
        'status',
        'valor_final',
        'data_inicio',
        'data_conclusao',
        'observacoes',
    ];

    protected function casts(): array
    {
        return [
            'valor_final' => 'decimal:2',
            'data_inicio' => 'date',
            'data_conclusao' => 'date',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function tipoProjeto(): BelongsTo
    {
        return $this->belongsTo(TipoProjeto::class, 'tipo_projeto_id');
    }

    public function recebimentos(): HasMany
    {
        return $this->hasMany(Recebimento::class, 'projeto_id');
    }

    public function despesas(): HasMany
    {
        return $this->hasMany(Despesa::class, 'projeto_id');
    }
}
