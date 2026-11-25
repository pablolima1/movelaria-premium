<?php

namespace App\Services;

use App\Models\Projeto;
use App\Repositories\ProjetoRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProjetoService
{
    public function __construct(
        private readonly ProjetoRepository $repository,
        private readonly ProjetoFinanceiro $financeiro,
        private readonly ParcelaService $parcelas,
    ) {
    }

    public function list(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $projetos = $this->repository->paginate($filters, $perPage);

        $projetos->getCollection()->each(
            fn (Projeto $projeto) => $projeto->setAttribute('financeiro', $this->financeiro->resumo($projeto))
        );

        return $projetos;
    }

    public function detalhe(Projeto $projeto): Projeto
    {
        $this->repository->loadDetalhe($projeto);
        $projeto->setAttribute('financeiro', $this->financeiro->resumo($projeto));

        return $projeto;
    }

    public function formOptions(): array
    {
        return [
            'clientes' => $this->repository->clientesOptions(),
            'tipos' => $this->repository->tiposOptions(),
        ];
    }

    public function create(array $data): Projeto
    {
        return DB::transaction(function () use ($data) {
            $projeto = $this->repository->create($this->normalize($data));
            $this->parcelas->sincronizar($projeto, forcar: true);

            return $projeto;
        });
    }

    public function update(Projeto $projeto, array $data): Projeto
    {
        return DB::transaction(function () use ($projeto, $data) {
            $this->repository->update($projeto, $this->normalize($data));
            $this->parcelas->sincronizar($projeto, forcar: $projeto->wasChanged(ParcelaService::CAMPOS_PLANO));

            return $projeto;
        });
    }

    public function delete(Projeto $projeto): void
    {
        $this->repository->delete($projeto);
    }

    private function normalize(array $data): array
    {
        if (isset($data['nome'])) {
            $data['nome'] = trim((string) $data['nome']);
        }

        foreach (['valor_final', 'numero_parcelas', 'primeiro_vencimento', 'data_inicio', 'data_conclusao', 'observacoes'] as $field) {
            if (array_key_exists($field, $data) && $data[$field] === '') {
                $data[$field] = null;
            }
        }

        return $data;
    }
}
