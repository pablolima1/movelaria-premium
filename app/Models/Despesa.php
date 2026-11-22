<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Despesa extends Model
{
    protected $table = 'despesas';

    protected $fillable = [
        'projeto_id',
        'categoria',
        'descricao',
        'valor',
        'aliquota_aplicada',
        'data_despesa',
        'status',
        'data_pagamento',
        'forma_pagamento',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'aliquota_aplicada' => 'decimal:2',
            'data_despesa' => 'date',
            'data_pagamento' => 'date',
        ];
    }

    public function projeto(): BelongsTo
    {
        return $this->belongsTo(Projeto::class, 'projeto_id');
    }
}
