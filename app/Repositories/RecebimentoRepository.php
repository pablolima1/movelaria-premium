<?php

namespace App\Repositories;

use App\Models\Projeto;
use App\Models\Recebimento;

class RecebimentoRepository
{
    public function create(Projeto $projeto, array $data): Recebimento
    {
        return $projeto->recebimentos()->create($data);
    }

    public function update(Recebimento $recebimento, array $data): Recebimento
    {
        $recebimento->update($data);

        return $recebimento;
    }

    public function delete(Recebimento $recebimento): void
    {
        $recebimento->delete();
    }
}
