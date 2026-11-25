<?php

namespace App\Services;

use App\Models\Despesa;
use App\Models\Projeto;
use App\Repositories\DespesaRepository;

class DespesaService
{
    public function __construct(private readonly DespesaRepository $repository)
    {
    }

    public function create(Projeto $projeto, array $data): Despesa
    {
        $despesa = $this->repository->create($projeto, $this->normalize($data));
        $projeto->touch();

        return $despesa;
    }

    public function update(Despesa $despesa, array $data): Despesa
    {
        return $this->repository->update($despesa, $this->normalize($data));
    }

    public function delete(Despesa $despesa): void
    {
        $this->repository->delete($despesa);
    }

    private function normalize(array $data): array
    {
        if (isset($data['descricao'])) {
            $data['descricao'] = trim((string) $data['descricao']) ?: null;
        }

        return $data;
    }
}
