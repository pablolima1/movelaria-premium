<?php

namespace App\Repositories;

use App\Enums\StatusProjeto;
use App\Models\Cliente;
use App\Models\Projeto;
use App\Models\TipoProjeto;
use App\Services\ProjetoFinanceiro;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ProjetoRepository
{
    public function paginate(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = $this->withFinanceiro(Projeto::query())
            ->with(['cliente:id,nome', 'tipoProjeto:id,nome']);

        $search = trim((string) ($filters['q'] ?? ''));
        if ($search !== '') {
            $query->where(function (Builder $q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                    ->orWhereHas('cliente', fn (Builder $c) => $c->where('nome', 'like', "%{$search}%"));
            });
        }

        if (($filters['status'] ?? '') !== '') {
            $query->where('status', $filters['status']);
        }

        if (($filters['tipo'] ?? '') !== '') {
            $query->where('tipo_projeto_id', (int) $filters['tipo']);
        }

        // Agendados: os próximos a serem produzidos primeiro.
        $proximosPrimeiro = ($filters['status'] ?? '') === StatusProjeto::Agendado->value;

        return $query
            ->orderByRaw('data_inicio is null')
            ->orderBy('data_inicio', $proximosPrimeiro ? 'asc' : 'desc')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function loadDetalhe(Projeto $projeto): Projeto
    {
        $projeto->load([
            'cliente:id,nome,telefone,email',
            'tipoProjeto:id,nome',
            'parcelas.recebimento:id,data_recebimento,forma_pagamento',
            'recebimentos' => fn ($q) => $q->orderByDesc('data_recebimento')->orderByDesc('id'),
            'despesas' => fn ($q) => $q->where('categoria', '!=', ProjetoFinanceiro::CATEGORIA_IMPOSTO)
                ->orderByDesc('data_despesa')->orderByDesc('id'),
        ]);

        $projeto->parcelas->each->setRelation('projeto', $projeto);
        $projeto->setAttribute('recebido_total', $projeto->recebimentos->sum('valor'));
        $projeto->setAttribute('material_total', $projeto->despesas->sum('valor'));

        return $projeto;
    }

    public function clientesOptions(): Collection
    {
        return Cliente::query()->orderBy('nome')->pluck('nome', 'id');
    }

    public function tiposOptions(): Collection
    {
        return TipoProjeto::query()->orderBy('nome')->get(['id', 'nome', 'ativo']);
    }

    public function create(array $data): Projeto
    {
        return Projeto::create($data);
    }

    public function update(Projeto $projeto, array $data): Projeto
    {
        $projeto->update($data);

        return $projeto;
    }

    public function delete(Projeto $projeto): void
    {
        $projeto->delete();
    }

    private function withFinanceiro(Builder $query): Builder
    {
        return $query
            ->withSum('recebimentos as recebido_total', 'valor')
            ->withSum(['despesas as material_total' => fn ($q) => $q->where('categoria', '!=', ProjetoFinanceiro::CATEGORIA_IMPOSTO)], 'valor');
    }
}
