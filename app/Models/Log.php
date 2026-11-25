<?php

namespace App\Models;

use App\Enums\EventoLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Log extends Model
{
    protected $table = 'logs';

    public const UPDATED_AT = null;

    protected $fillable = [
        'evento',
        'user_id',
        'referencia_type',
        'referencia_id',
        'descricao',
        'dados',
        'ip',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'dados' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function referencia(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeDoEvento(Builder $query, EventoLog|string $evento): Builder
    {
        return $query->where('evento', $evento instanceof EventoLog ? $evento->value : $evento);
    }
}
