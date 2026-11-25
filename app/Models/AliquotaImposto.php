<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AliquotaImposto extends Model
{
    protected $table = 'aliquotas_imposto';

    protected $fillable = [
        'percentual',
        'vigente_desde',
        'vigente_ate',
    ];

    protected function casts(): array
    {
        return [
            'percentual' => 'decimal:2',
            'vigente_desde' => 'date',
            'vigente_ate' => 'date',
        ];
    }

    public function scopeVigenteEm(Builder $query, ?string $data = null): Builder
    {
        $data ??= now()->toDateString();

        return $query
            ->whereDate('vigente_desde', '<=', $data)
            ->where(function (Builder $query) use ($data) {
                $query->whereNull('vigente_ate')
                    ->orWhereDate('vigente_ate', '>=', $data);
            })
            ->orderByDesc('vigente_desde');
    }

    public function situacao(?string $data = null): string
    {
        $data ??= now()->toDateString();

        if ($this->vigente_desde->toDateString() > $data) {
            return 'futura';
        }

        if ($this->vigente_ate && $this->vigente_ate->toDateString() < $data) {
            return 'encerrada';
        }

        return 'vigente';
    }
}
