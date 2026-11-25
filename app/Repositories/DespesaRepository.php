<?php

namespace App\Repositories;

use App\Models\Despesa;
use App\Models\Projeto;

class DespesaRepository
{
    public function create(Projeto $projeto, array $data): Despesa
    {
        return $projeto->despesas()->create($data);
    }

    public function update(Despesa $despesa, array $data): Despesa
    {
        $despesa->update($data);

        return $despesa;
    }

    public function delete(Despesa $despesa): void
    {
        $despesa->delete();
    }
}
