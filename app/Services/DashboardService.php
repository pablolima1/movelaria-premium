<?php

namespace App\Services;

use App\Models\Projeto;
use App\Repositories\DashboardRepository;
use Carbon\CarbonImmutable;

class DashboardService
{
    public function __construct(
        private readonly DashboardRepository $repository,
        private readonly AliquotaResolver $aliquotas,
        private readonly ProjetoFinanceiro $financeiro,
    ) {
    }

    /**
     * Monta todos os dados da Home para o mês informado.
     * Valores somados em centavos (inteiros) para evitar erro de arredondamento.
     */
    public function montar(CarbonImmutable $mes): array
    {
        $inicioAno = $mes->startOfYear();
        $fimAno = $mes->endOfYear();

        $meses = [];
        for ($m = 1; $m <= 12; $m++) {
            $meses[$m] = ['entrou' => 0, 'material' => 0, 'imposto' => 0];
        }

        foreach ($this->repository->recebimentosEntre($inicioAno, $fimAno) as $recebimento) {
            $m = $recebimento->data_recebimento->month;
            $centavos = $this->toCents($recebimento->valor);
            $aliquota = $this->aliquotas->paraProjeto($recebimento->projeto) ?? 0.0;

            $meses[$m]['entrou'] += $centavos;
            $meses[$m]['imposto'] += (int) round($centavos * $aliquota / 100);
        }

        foreach ($this->repository->despesasEntre($inicioAno, $fimAno) as $despesa) {
            $meses[$despesa->data_despesa->month]['material'] += $this->toCents($despesa->valor);
        }

        $serie = [];
        $totalAno = ['entrou' => 0, 'material' => 0, 'imposto' => 0];
        foreach ($meses as $m => $valores) {
            $serie[$m] = $this->resumo($valores);
            foreach ($totalAno as $k => $_) {
                $totalAno[$k] += $valores[$k];
            }
        }

        return [
            'mes' => $mes,
            'resumoAno' => $this->resumo($totalAno),
            'resumoMes' => $serie[$mes->month],
            'serieMensal' => $serie,
            'projetosDoMes' => $this->projetosDoMes($mes),
        ];
    }

    private function projetosDoMes(CarbonImmutable $mes): array
    {
        return $this->repository
            ->projetosComRecebimentoEntre($mes->startOfMonth(), $mes->endOfMonth())
            ->map(fn (Projeto $projeto) => [
                'id' => $projeto->id,
                'nome' => $projeto->nome,
                'cliente' => $projeto->cliente?->nome,
                'tipo' => $projeto->tipoProjeto?->nome,
                'status' => $projeto->status,
                'recebido_mes' => $this->toCents($projeto->recebido_periodo) / 100,
            ] + $this->financeiro->resumo($projeto))
            ->all();
    }

    private function resumo(array $centavos): array
    {
        $bruto = $centavos['entrou'] - $centavos['material'];

        return [
            'entrou' => $centavos['entrou'] / 100,
            'material' => $centavos['material'] / 100,
            'imposto' => $centavos['imposto'] / 100,
            'lucro_bruto' => $bruto / 100,
            'lucro_liquido' => ($bruto - $centavos['imposto']) / 100,
        ];
    }

    private function toCents(mixed $valor): int
    {
        return ProjetoFinanceiro::cents($valor);
    }
}
