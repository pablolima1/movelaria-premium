<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Recebimento extends Model
{
    protected $table = 'recebimentos';

    protected $fillable = [
        'projeto_id',
        'valor',
        'forma_pagamento',
        'data_recebimento',
        'observacoes',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'data_recebimento' => 'date',
        ];
    }

    public function projeto(): BelongsTo
    {
        return $this->belongsTo(Projeto::class, 'projeto_id');
    }

    public function parcela(): HasOne
    {
        return $this->hasOne(Parcela::class, 'recebimento_id');
    }
}
