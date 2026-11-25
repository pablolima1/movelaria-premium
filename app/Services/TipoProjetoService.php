<?php

namespace App\Services;

use App\Models\TipoProjeto;
use App\Repositories\TipoProjetoRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TipoProjetoService
{
    public function __construct(private readonly TipoProjetoRepository $repository)
    {
    }

    public function list(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    public function create(array $data): TipoProjeto
    {
        return $this->repository->create($this->normalize($data));
    }

    public function update(TipoProjeto $tipoProjeto, array $data): TipoProjeto
    {
        return $this->repository->update($tipoProjeto, $this->normalize($data));
    }

    public function delete(TipoProjeto $tipoProjeto): void
    {
        $this->repository->delete($tipoProjeto);
    }

    private function normalize(array $data): array
    {
        if (isset($data['nome'])) {
            $data['nome'] = trim((string) $data['nome']);
        }

        if (array_key_exists('ativo', $data)) {
            $data['ativo'] = (bool) $data['ativo'];
        }

        return $data;
    }
}
