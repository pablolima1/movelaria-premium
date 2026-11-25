<?php

namespace App\Models;

use App\Enums\CondicaoPagamento;
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
        'condicao_pagamento',
        'numero_parcelas',
        'primeiro_vencimento',
        'data_inicio',
        'data_conclusao',
        'observacoes',
    ];

    protected function casts(): array
    {
        return [
            'valor_final' => 'decimal:2',
            'numero_parcelas' => 'integer',
            'primeiro_vencimento' => 'date',
            'data_inicio' => 'date',
            'data_conclusao' => 'date',
        ];
    }

    public function isParcelado(): bool
    {
        return $this->condicao_pagamento === CondicaoPagamento::Parcelado->value && $this->numero_parcelas > 1;
    }

    /** Valor de cada parcela (null se à vista ou sem valor definido). */
    public function valorParcela(): ?float
    {
        if (! $this->isParcelado() || $this->valor_final === null) {
            return null;
        }

        return round((float) $this->valor_final / $this->numero_parcelas, 2);
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

    public function parcelas(): HasMany
    {
        return $this->hasMany(Parcela::class, 'projeto_id')->orderBy('numero');
    }

    public function despesas(): HasMany
    {
        return $this->hasMany(Despesa::class, 'projeto_id');
    }
}
