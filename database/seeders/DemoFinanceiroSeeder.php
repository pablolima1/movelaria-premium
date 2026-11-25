<?php

namespace Database\Seeders;

use App\Models\AliquotaImposto;
use App\Models\Cliente;
use App\Models\Despesa;
use App\Models\Projeto;
use App\Models\Recebimento;
use App\Models\TipoProjeto;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Dados fictícios para visualizar o dashboard da Home.
 * Não faz parte do DatabaseSeeder. Rode com:
 *   php artisan db:seed --class=DemoFinanceiroSeeder
 * Pode ser executado de novo: remove os dados demo anteriores (marcados com [demo]).
 */
class DemoFinanceiroSeeder extends Seeder
{
    private const MARCA = '[demo]';

    public function run(): void
    {
        mt_srand(2026);

        DB::transaction(function () {
            $this->limparDemo();
            $this->garantirAliquotaHistorica();

            $tipos = TipoProjeto::query()->where('ativo', true)->pluck('id')->all();
            if ($tipos === []) {
                $tipos = [TipoProjeto::query()->firstOrCreate(['nome' => 'Guarda-roupa'], ['ativo' => true])->id];
            }

            $clientes = collect(['Ana Souza', 'Bruno Lima', 'Carla Mendes', 'Diego Rocha', 'Elisa Martins'])
                ->map(fn ($nome) => Cliente::create(['nome' => $nome, 'observacoes' => self::MARCA]));

            $projetos = [
                ['Cozinha completa', 1, 28500], ['Closet casal', 1, 18900], ['Painel home theater', 2, 7400],
                ['Guarda-roupa infantil', 3, 9800], ['Armários lavanderia', 4, 6200], ['Cozinha gourmet', 5, 34600],
                ['Escritório planejado', 6, 12300], ['Dormitório casal', 8, 21700], ['Banheiro suíte', 9, 8900],
                ['Painel sala', 10, 6800], ['Closet solteiro', 11, 11200],
            ];

            $hoje = CarbonImmutable::today();

            foreach ($projetos as $i => [$nome, $mes, $valor]) {
                $inicio = CarbonImmutable::create(2026, $mes, mt_rand(2, 20));
                if ($inicio->greaterThan($hoje)) {
                    continue;
                }

                $projeto = Projeto::create([
                    'cliente_id' => $clientes[$i % $clientes->count()]->id,
                    'tipo_projeto_id' => $tipos[$i % count($tipos)],
                    'nome' => $nome,
                    'status' => 'em_producao',
                    'valor_final' => $valor,
                    'data_inicio' => $inicio,
                    'observacoes' => self::MARCA,
                ]);

                // Entrada de 50% e duas parcelas de 25% nos meses seguintes
                $parcelas = [[$inicio, 0.5], [$inicio->addMonth(), 0.25], [$inicio->addMonths(2), 0.25]];
                $recebidoTudo = true;
                foreach ($parcelas as [$data, $fracao]) {
                    if ($data->greaterThan($hoje)) {
                        $recebidoTudo = false;
                        continue;
                    }
                    Recebimento::create([
                        'projeto_id' => $projeto->id,
                        'valor' => round($valor * $fracao, 2),
                        'forma_pagamento' => mt_rand(0, 1) ? 'pix' : 'cartao_credito',
                        'data_recebimento' => $data,
                        'observacoes' => self::MARCA,
                    ]);
                }

                // Material: entre 28% e 42% do valor, em 1 ou 2 compras
                $material = $valor * mt_rand(28, 42) / 100;
                $compras = mt_rand(1, 2);
                for ($c = 0; $c < $compras; $c++) {
                    $data = $inicio->addDays(3 + $c * 12);
                    Despesa::create([
                        'projeto_id' => $projeto->id,
                        'categoria' => 'material',
                        'descricao' => self::MARCA . ' Compra de material ' . ($c + 1),
                        'valor' => round($material / $compras, 2),
                        'data_despesa' => $data->greaterThan($hoje) ? $hoje : $data,
                        'status' => 'pago',
                        'data_pagamento' => $data->greaterThan($hoje) ? $hoje : $data,
                        'forma_pagamento' => 'pix',
                    ]);
                }

                if ($recebidoTudo && $inicio->addMonths(2)->lessThan($hoje->subMonth())) {
                    $projeto->update(['status' => 'concluido', 'data_conclusao' => $inicio->addMonths(2)]);
                }
            }
        });
    }

    private function limparDemo(): void
    {
        $projetoIds = Projeto::query()->where('observacoes', self::MARCA)->pluck('id');

        Recebimento::query()->whereIn('projeto_id', $projetoIds)->delete();
        Despesa::query()->whereIn('projeto_id', $projetoIds)->delete();
        Projeto::query()->whereIn('id', $projetoIds)->delete();
        Cliente::query()->where('observacoes', self::MARCA)->whereDoesntHave('projetos')->delete();
    }

    /**
     * Garante uma alíquota cobrindo o início de 2026 (o seeder padrão cria
     * apenas uma a partir da data em que rodou), sem sobrepor as existentes.
     */
    private function garantirAliquotaHistorica(): void
    {
        $inicio = '2026-01-01';

        if (AliquotaImposto::query()->vigenteEm($inicio)->exists()) {
            return;
        }

        $proxima = AliquotaImposto::query()
            ->whereDate('vigente_desde', '>', $inicio)
            ->orderBy('vigente_desde')
            ->value('vigente_desde');

        AliquotaImposto::create([
            'percentual' => 6.00,
            'vigente_desde' => '2025-01-01',
            'vigente_ate' => $proxima ? CarbonImmutable::parse($proxima)->subDay()->toDateString() : null,
        ]);
    }
}
