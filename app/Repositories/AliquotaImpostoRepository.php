<?php

namespace App\Repositories;

use App\Models\AliquotaImposto;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AliquotaImpostoRepository
{
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = AliquotaImposto::query();
        $hoje = now()->toDateString();

        match ($filters['status'] ?? '') {
            'vigente' => $query->vigenteEm($hoje),
            'futura' => $query->whereDate('vigente_desde', '>', $hoje),
            'encerrada' => $query->whereNotNull('vigente_ate')->whereDate('vigente_ate', '<', $hoje),
            default => null,
        };

        return $query
            ->reorder()
            ->orderByDesc('vigente_desde')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function vigente(): ?AliquotaImposto
    {
        return AliquotaImposto::query()->vigenteEm()->first();
    }

    public function create(array $data): AliquotaImposto
    {
        return AliquotaImposto::create($data);
    }

    public function update(AliquotaImposto $aliquota, array $data): AliquotaImposto
    {
        $aliquota->update($data);

        return $aliquota;
    }

    public function delete(AliquotaImposto $aliquota): void
    {
        $aliquota->delete();
    }
}
