<?php

namespace App\Services;

use App\Models\Projeto;
use App\Repositories\DashboardRepository;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class DashboardService
{
    private ?Collection $aliquotas = null;

    /** @var array<int, float|null> */
    private array $aliquotaPorProjeto = [];

    public function __construct(private readonly DashboardRepository $repository)
    {
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
            $aliquota = $this->aliquotaDoProjeto($recebimento->projeto) ?? 0.0;

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
            ->map(function (Projeto $projeto) {
                $aliquota = $this->aliquotaDoProjeto($projeto);
                $total = $this->toCents($projeto->valor_final);
                $recebido = $this->toCents($projeto->recebido_total);
                $material = $this->toCents($projeto->material_total);
                $imposto = (int) round($total * ($aliquota ?? 0) / 100);

                return [
                    'id' => $projeto->id,
                    'nome' => $projeto->nome,
                    'cliente' => $projeto->cliente?->nome,
                    'tipo' => $projeto->tipoProjeto?->nome,
                    'status' => $projeto->status,
                    'aliquota' => $aliquota,
                    'valor_total' => $total / 100,
                    'recebido_total' => $recebido / 100,
                    'recebido_mes' => $this->toCents($projeto->recebido_periodo) / 100,
                    'material' => $material / 100,
                    'imposto' => $imposto / 100,
                    'lucro_liquido' => ($total - $material - $imposto) / 100,
                    'progresso' => $total > 0 ? min(100, (int) round($recebido / $total * 100)) : null,
                ];
            })
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

    /**
     * Alíquota vigente na data do projeto (data_inicio, ou data de cadastro).
     * Mesma regra do scope AliquotaImposto::vigenteEm, resolvida em memória.
     */
    private function aliquotaDoProjeto(?Projeto $projeto): ?float
    {
        if (! $projeto) {
            return null;
        }

        if (array_key_exists($projeto->id, $this->aliquotaPorProjeto)) {
            return $this->aliquotaPorProjeto[$projeto->id];
        }

        $this->aliquotas ??= $this->repository->aliquotas()->toBase();

        /** @var CarbonInterface|null $dataBase */
        $dataBase = $projeto->data_inicio ?? $projeto->created_at;
        $data = ($dataBase ?? now())->toDateString();

        $aliquota = $this->aliquotas->first(fn ($a) => $a->vigente_desde->toDateString() <= $data
            && ($a->vigente_ate === null || $a->vigente_ate->toDateString() >= $data));

        return $this->aliquotaPorProjeto[$projeto->id] = $aliquota ? (float) $aliquota->percentual : null;
    }

    private function toCents(mixed $valor): int
    {
        return (int) round(((float) ($valor ?? 0)) * 100);
    }
}
