<?php

namespace App\Repositories;

use App\Models\TipoProjeto;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TipoProjetoRepository
{
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = TipoProjeto::query()->withCount('projetos');

        $search = trim((string) ($filters['q'] ?? ''));
        if ($search !== '') {
            $query->where('nome', 'like', "%{$search}%");
        }

        $status = $filters['status'] ?? '';
        if ($status === 'ativo') {
            $query->where('ativo', true);
        } elseif ($status === 'inativo') {
            $query->where('ativo', false);
        }

        return $query->orderBy('nome')->paginate($perPage)->withQueryString();
    }

    public function create(array $data): TipoProjeto
    {
        return TipoProjeto::create($data);
    }

    public function update(TipoProjeto $tipoProjeto, array $data): TipoProjeto
    {
        $tipoProjeto->update($data);

        return $tipoProjeto;
    }

    public function delete(TipoProjeto $tipoProjeto): void
    {
        $tipoProjeto->delete();
    }
}
