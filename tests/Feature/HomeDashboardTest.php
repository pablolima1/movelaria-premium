<?php

namespace Tests\Feature;

use App\Models\AliquotaImposto;
use App\Models\Cliente;
use App\Models\Despesa;
use App\Models\Projeto;
use App\Models\Recebimento;
use App\Models\TipoProjeto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());

        AliquotaImposto::create(['percentual' => 6.00, 'vigente_desde' => '2025-01-01', 'vigente_ate' => '2026-05-31']);
        AliquotaImposto::create(['percentual' => 4.50, 'vigente_desde' => '2026-06-01']);

        $cliente = Cliente::create(['nome' => 'João Silva']);
        $tipo = TipoProjeto::create(['nome' => 'Cozinha', 'ativo' => true]);

        // Projeto A: 10.000 a 6% (início em março). Material 3.000.
        $a = Projeto::create(['cliente_id' => $cliente->id, 'tipo_projeto_id' => $tipo->id, 'nome' => 'Projeto A', 'valor_final' => 10000, 'data_inicio' => '2026-03-10']);
        $this->recebimento($a, 5000, '2026-03-10');
        $this->recebimento($a, 5000, '2026-07-15');
        $this->despesa($a, 3000, '2026-03-12');
        $this->despesa($a, 999, '2026-03-12', 'imposto'); // ignorada: imposto é calculado

        // Projeto B: 20.000 a 4,5% (início em julho). Material 8.000.
        $b = Projeto::create(['cliente_id' => $cliente->id, 'tipo_projeto_id' => $tipo->id, 'nome' => 'Projeto B', 'valor_final' => 20000, 'data_inicio' => '2026-07-01']);
        $this->recebimento($b, 10000, '2026-07-01');
        $this->despesa($b, 8000, '2026-07-02');

        // Ano anterior: não entra no resumo de 2026
        $this->recebimento($b, 777, '2025-12-31');
    }

    public function test_resumo_do_ano_e_do_mes(): void
    {
        $data = $this->get('/home?mes=2026-07')->assertOk()->viewData('resumoAno');

        // Entrou 20.000; material 11.000; imposto: 5000*6% + 5000*6% + 10000*4,5% = 1.050
        $this->assertEquals(20000, $data['entrou']);
        $this->assertEquals(11000, $data['material']);
        $this->assertEquals(1050, $data['imposto']);
        $this->assertEquals(9000, $data['lucro_bruto']);
        $this->assertEquals(7950, $data['lucro_liquido']);

        $mes = $this->get('/home?mes=2026-07')->viewData('resumoMes');
        $this->assertEquals(15000, $mes['entrou']);
        $this->assertEquals(8000, $mes['material']);
        $this->assertEquals(750, $mes['imposto']);
        $this->assertEquals(6250, $mes['lucro_liquido']);
    }

    public function test_cards_dos_projetos_do_mes(): void
    {
        $response = $this->get('/home?mes=2026-07')->assertOk();
        $projetos = collect($response->viewData('projetosDoMes'))->keyBy('nome');

        $this->assertCount(2, $projetos);
        $this->assertSame('Projeto B', $response->viewData('projetosDoMes')[0]['nome']); // maior entrada no mês primeiro

        $a = $projetos['Projeto A'];
        $this->assertEquals(5000, $a['recebido_mes']);
        $this->assertEquals(10000, $a['recebido_total']);
        $this->assertEquals(3000, $a['material']);
        $this->assertEquals(600, $a['imposto']);
        $this->assertEquals(6400, $a['lucro_liquido']);
        $this->assertSame(100, $a['progresso']);

        $b = $projetos['Projeto B'];
        $this->assertEquals(900, $b['imposto']);
        $this->assertEquals(11100, $b['lucro_liquido']);
        $this->assertSame(54, $b['progresso']); // (10.000 + 777) / 20.000

        $response->assertSee('Projeto A')->assertSee('R$ 6.400,00');
    }

    public function test_mes_sem_entradas_mostra_estado_vazio(): void
    {
        $this->get('/home?mes=2026-02')
            ->assertOk()
            ->assertSee('Nenhuma entrada em Fevereiro.');
    }

    public function test_mes_invalido_usa_mes_atual(): void
    {
        $response = $this->get('/home?mes=2026-13')->assertOk();

        $this->assertTrue($response->viewData('mes')->isSameMonth(now()));
    }

    private function recebimento(Projeto $projeto, float $valor, string $data): void
    {
        Recebimento::create(['projeto_id' => $projeto->id, 'valor' => $valor, 'forma_pagamento' => 'pix', 'data_recebimento' => $data]);
    }

    private function despesa(Projeto $projeto, float $valor, string $data, string $categoria = 'material'): void
    {
        Despesa::create(['projeto_id' => $projeto->id, 'categoria' => $categoria, 'valor' => $valor, 'data_despesa' => $data]);
    }
}
