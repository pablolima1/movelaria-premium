<?php

namespace App\Services;

use App\Models\Projeto;
use App\Models\Recebimento;
use App\Repositories\RecebimentoRepository;

class RecebimentoService
{
    public function __construct(private readonly RecebimentoRepository $repository)
    {
    }

    public function create(Projeto $projeto, array $data): Recebimento
    {
        $recebimento = $this->repository->create($projeto, $data);
        $projeto->touch();

        return $recebimento;
    }

    public function update(Recebimento $recebimento, array $data): Recebimento
    {
        return $this->repository->update($recebimento, $data);
    }

    public function delete(Recebimento $recebimento): void
    {
        $this->repository->delete($recebimento);
    }
}
