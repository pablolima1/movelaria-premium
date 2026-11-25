<?php

namespace App\Services;

use App\Models\AliquotaImposto;
use App\Models\Projeto;
use Illuminate\Support\Collection;

/**
 * Resolve a alíquota vigente na data de um projeto (data_inicio ou data de cadastro).
 * Mesma regra do scope AliquotaImposto::vigenteEm, resolvida em memória para
 * evitar uma consulta por projeto em listagens.
 */
class AliquotaResolver
{
    private ?Collection $aliquotas = null;

    /** @var array<int, float|null> */
    private array $cache = [];

    public function paraProjeto(?Projeto $projeto): ?float
    {
        if (! $projeto) {
            return null;
        }

        if ($projeto->id && array_key_exists($projeto->id, $this->cache)) {
            return $this->cache[$projeto->id];
        }

        $data = ($projeto->data_inicio ?? $projeto->created_at ?? now())->toDateString();
        $aliquota = $this->paraData($data);

        if ($projeto->id) {
            $this->cache[$projeto->id] = $aliquota;
        }

        return $aliquota;
    }

    public function paraData(string $data): ?float
    {
        $this->aliquotas ??= AliquotaImposto::query()
            ->orderByDesc('vigente_desde')
            ->get(['id', 'percentual', 'vigente_desde', 'vigente_ate'])
            ->toBase();

        $aliquota = $this->aliquotas->first(fn ($a) => $a->vigente_desde->toDateString() <= $data
            && ($a->vigente_ate === null || $a->vigente_ate->toDateString() >= $data));

        return $aliquota ? (float) $aliquota->percentual : null;
    }
}
