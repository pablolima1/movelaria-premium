<?php

namespace App\Models;

use App\Enums\StatusParcela;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Parcela extends Model
{
    protected $table = 'parcelas';

    protected $fillable = [
        'projeto_id',
        'numero',
        'valor',
        'vencimento',
        'recebimento_id',
    ];

    protected function casts(): array
    {
        return [
            'numero' => 'integer',
            'valor' => 'decimal:2',
            'vencimento' => 'date',
        ];
    }

    public function projeto(): BelongsTo
    {
        return $this->belongsTo(Projeto::class, 'projeto_id');
    }

    public function recebimento(): BelongsTo
    {
        return $this->belongsTo(Recebimento::class, 'recebimento_id');
    }

    public function isPaga(): bool
    {
        return $this->recebimento_id !== null;
    }

    public function status(): StatusParcela
    {
        if ($this->isPaga()) {
            return StatusParcela::Paga;
        }

        return $this->vencimento->lt(today()) ? StatusParcela::Atrasada : StatusParcela::AVencer;
    }

    /** Dias de atraso (0 se em dia ou paga). */
    public function diasAtraso(): int
    {
        return $this->status() === StatusParcela::Atrasada ? (int) $this->vencimento->diffInDays(today()) : 0;
    }

    public function scopeEmAberto(Builder $query): Builder
    {
        return $query->whereNull('recebimento_id');
    }

    public function scopePagas(Builder $query): Builder
    {
        return $query->whereNotNull('recebimento_id');
    }
}
