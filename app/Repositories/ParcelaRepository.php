<?php

namespace App\Repositories;

use App\Enums\StatusProjeto;
use App\Models\Parcela;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ParcelaRepository
{
    /**
     * Parcelas para a tela de cobranças.
     * $aba: "abertas" (padrão), "atrasadas" ou "pagas".
     */
    public function cobrancas(string $aba, string $busca = '', int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->base()->with(['projeto.cliente:id,nome,telefone', 'recebimento:id,data_recebimento,forma_pagamento']);

        if ($busca !== '') {
            $query->whereHas('projeto', fn (Builder $p) => $p->where('nome', 'like', "%{$busca}%")
                ->orWhereHas('cliente', fn (Builder $c) => $c->where('nome', 'like', "%{$busca}%")));
        }

        match ($aba) {
            'pagas' => $query->pagas()
                ->join('recebimentos', 'recebimentos.id', '=', 'parcelas.recebimento_id')
                ->orderByDesc('recebimentos.data_recebimento')
                ->orderByDesc('parcelas.id')
                ->select('parcelas.*'),
            'atrasadas' => $query->emAberto()->whereDate('vencimento', '<', today())->orderBy('vencimento')->orderBy('id'),
            default => $query->emAberto()->orderBy('vencimento')->orderBy('id'),
        };

        return $query->paginate($perPage)->withQueryString();
    }

    /** Totais em aberto: atrasado, vence nos próximos 30 dias e total. */
    public function resumo(): array
    {
        $hoje = today()->toDateString();
        $limite = today()->addDays(30)->toDateString();

        $row = $this->base()->emAberto()
            ->selectRaw('COALESCE(SUM(CASE WHEN vencimento < ? THEN valor END), 0) as atrasado', [$hoje])
            ->selectRaw('COUNT(CASE WHEN vencimento < ? THEN 1 END) as atrasadas_qtd', [$hoje])
            ->selectRaw('COALESCE(SUM(CASE WHEN vencimento >= ? AND vencimento <= ? THEN valor END), 0) as proximos', [$hoje, $limite])
            ->selectRaw('COUNT(CASE WHEN vencimento >= ? AND vencimento <= ? THEN 1 END) as proximos_qtd', [$hoje, $limite])
            ->selectRaw('COALESCE(SUM(valor), 0) as em_aberto')
            ->selectRaw('COUNT(*) as em_aberto_qtd')
            ->toBase()
            ->first();

        return [
            'atrasado' => (float) $row->atrasado,
            'atrasadas_qtd' => (int) $row->atrasadas_qtd,
            'proximos' => (float) $row->proximos,
            'proximos_qtd' => (int) $row->proximos_qtd,
            'em_aberto' => (float) $row->em_aberto,
            'em_aberto_qtd' => (int) $row->em_aberto_qtd,
        ];
    }

    /** Ignora projetos cancelados. */
    private function base(): Builder
    {
        return Parcela::query()->whereHas(
            'projeto',
            fn (Builder $p) => $p->where('status', '!=', StatusProjeto::Cancelado->value)
        );
    }
}
