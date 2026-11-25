<?php

namespace App\Repositories;

use App\Models\AliquotaImposto;
use App\Models\Despesa;
use App\Models\Projeto;
use App\Models\Recebimento;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;

class DashboardRepository
{
    public const CATEGORIA_IMPOSTO = 'imposto';

    public function aliquotas(): Collection
    {
        return AliquotaImposto::query()
            ->orderByDesc('vigente_desde')
            ->get(['id', 'percentual', 'vigente_desde', 'vigente_ate']);
    }

    public function recebimentosEntre(CarbonInterface $inicio, CarbonInterface $fim): Collection
    {
        return Recebimento::query()
            ->whereBetween('data_recebimento', [$inicio->toDateString(), $fim->toDateString()])
            ->with('projeto:id,valor_final,data_inicio,created_at')
            ->get(['id', 'projeto_id', 'valor', 'data_recebimento']);
    }

    public function despesasEntre(CarbonInterface $inicio, CarbonInterface $fim): Collection
    {
        return Despesa::query()
            ->where('categoria', '!=', self::CATEGORIA_IMPOSTO)
            ->whereBetween('data_despesa', [$inicio->toDateString(), $fim->toDateString()])
            ->get(['id', 'projeto_id', 'valor', 'data_despesa']);
    }

    public function projetosComRecebimentoEntre(CarbonInterface $inicio, CarbonInterface $fim): Collection
    {
        $periodo = [$inicio->toDateString(), $fim->toDateString()];

        return Projeto::query()
            ->whereHas('recebimentos', fn ($q) => $q->whereBetween('data_recebimento', $periodo))
            ->with(['cliente:id,nome', 'tipoProjeto:id,nome'])
            ->withSum('recebimentos as recebido_total', 'valor')
            ->withSum(['recebimentos as recebido_periodo' => fn ($q) => $q->whereBetween('data_recebimento', $periodo)], 'valor')
            ->withSum(['despesas as material_total' => fn ($q) => $q->where('categoria', '!=', self::CATEGORIA_IMPOSTO)], 'valor')
            ->orderByDesc('recebido_periodo')
            ->orderBy('nome')
            ->get();
    }
}
