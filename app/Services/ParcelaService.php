<?php

namespace App\Services;

use App\Models\Parcela;
use App\Models\Projeto;
use App\Models\Recebimento;
use App\Repositories\ParcelaRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ParcelaService
{
    /** Campos do projeto que, ao mudar, refazem o plano de parcelas. */
    public const CAMPOS_PLANO = ['valor_final', 'condicao_pagamento', 'numero_parcelas', 'primeiro_vencimento'];

    public function __construct(private readonly ParcelaRepository $repository)
    {
    }

    public function cobrancas(string $aba, string $busca = ''): LengthAwarePaginator
    {
        return $this->repository->cobrancas($aba, $busca);
    }

    public function resumo(): array
    {
        return $this->repository->resumo();
    }

    /**
     * Mantém as parcelas de acordo com o projeto. As parcelas já pagas nunca
     * são alteradas; as em aberto são refeitas com o saldo restante, uma por
     * mês a partir do 1º vencimento.
     */
    public function sincronizar(Projeto $projeto, bool $forcar = false): void
    {
        $semPlano = $projeto->isParcelado() && ! $projeto->parcelas()->exists();

        if (! $forcar && ! $semPlano) {
            return;
        }

        $pagas = $projeto->parcelas()->pagas()->get();
        $projeto->parcelas()->emAberto()->delete();

        if (! $projeto->isParcelado() || $projeto->valor_final === null || ! $projeto->primeiro_vencimento) {
            return;
        }

        $restantes = $projeto->numero_parcelas - $pagas->count();
        $centavos = (int) round(((float) $projeto->valor_final - (float) $pagas->sum('valor')) * 100);

        if ($restantes <= 0 || $centavos <= 0) {
            return;
        }

        $base = intdiv($centavos, $restantes);
        $resto = $centavos - $base * $restantes;
        $usados = $pagas->pluck('numero')->all();
        $numeros = array_values(array_diff(range(1, max($projeto->numero_parcelas, max($usados ?: [0]) + $restantes)), $usados));
        $numeros = array_slice($numeros, 0, $restantes);

        foreach ($numeros as $i => $numero) {
            $valor = $base + ($i === $restantes - 1 ? $resto : 0); // centavos de arredondamento na última

            $projeto->parcelas()->create([
                'numero' => $numero,
                'valor' => $valor / 100,
                'vencimento' => $projeto->primeiro_vencimento->copy()->addMonthsNoOverflow($numero - 1),
            ]);
        }
    }

    /** Lança o recebimento da parcela e a marca como paga. */
    public function receber(Parcela $parcela, array $data): Recebimento
    {
        return DB::transaction(function () use ($parcela, $data) {
            $projeto = $parcela->projeto;

            $recebimento = $projeto->recebimentos()->create([
                'valor' => $parcela->valor,
                'forma_pagamento' => $data['forma_pagamento'],
                'data_recebimento' => $data['data_recebimento'],
                'observacoes' => "Parcela {$parcela->numero} de {$projeto->numero_parcelas}",
            ]);

            $parcela->update(['recebimento_id' => $recebimento->id]);
            $projeto->touch();

            return $recebimento;
        });
    }

    public function remarcar(Parcela $parcela, string $vencimento): Parcela
    {
        $parcela->update(['vencimento' => $vencimento]);

        return $parcela;
    }
}
