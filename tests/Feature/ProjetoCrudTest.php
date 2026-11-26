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

class ProjetoCrudTest extends TestCase
{
    use RefreshDatabase;

    private Cliente $cliente;

    private TipoProjeto $tipo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
        AliquotaImposto::create(['percentual' => 5.00, 'vigente_desde' => '2020-01-01']);
        $this->cliente = Cliente::create(['nome' => 'Maria Oliveira']);
        $this->tipo = TipoProjeto::create(['nome' => 'Cozinha', 'ativo' => true]);
    }

    private function projeto(array $attrs = []): Projeto
    {
        return Projeto::create($attrs + [
            'cliente_id' => $this->cliente->id,
            'tipo_projeto_id' => $this->tipo->id,
            'nome' => 'Cozinha Maria',
            'status' => 'em_producao',
            'valor_final' => 10000,
            'data_inicio' => '2026-05-01',
        ]);
    }

    public function test_cria_projeto_com_valor_mascarado_e_redireciona_para_detalhe(): void
    {
        $response = $this->post('/projetos', [
            '_modal' => 'projeto-create',
            'nome' => '  Closet suíte ',
            'cliente_id' => $this->cliente->id,
            'tipo_projeto_id' => $this->tipo->id,
            'status' => 'orcamento',
            'valor_final' => '12.345,67',
            'data_inicio' => '2026-06-10',
            'data_conclusao' => '',
        ]);

        $projeto = Projeto::firstOrFail();
        $response->assertRedirect(route('projetos.show', $projeto));
        $this->assertSame('Closet suíte', $projeto->nome);
        $this->assertSame('12345.67', $projeto->valor_final);
        $this->assertNull($projeto->data_conclusao);
    }

    public function test_valor_sem_centavos_e_interpretado_como_milhar(): void
    {
        $base = ['nome' => 'Cozinha', 'cliente_id' => $this->cliente->id, 'tipo_projeto_id' => $this->tipo->id, 'status' => 'agendado'];

        $this->post('/projetos', $base + ['valor_final' => '20.000'])->assertSessionHasNoErrors();
        $this->assertSame('20000.00', Projeto::latest('id')->first()->valor_final);

        $this->post('/projetos', $base + ['valor_final' => '10.500,80'])->assertSessionHasNoErrors();
        $this->assertSame('10500.80', Projeto::latest('id')->first()->valor_final);

        $this->post('/projetos', $base + ['valor_final' => '1.250.000'])->assertSessionHasNoErrors();
        $this->assertSame('1250000.00', Projeto::latest('id')->first()->valor_final);
    }

    public function test_condicao_de_pagamento_a_vista_ou_parcelado(): void
    {
        $base = ['nome' => 'Closet', 'cliente_id' => $this->cliente->id, 'tipo_projeto_id' => $this->tipo->id, 'status' => 'agendado', 'valor_final' => '12.000,00'];

        // Parcelado exige o número de parcelas
        $this->post('/projetos', $base + ['condicao_pagamento' => 'parcelado'])->assertSessionHasErrors('numero_parcelas');
        $this->post('/projetos', $base + ['condicao_pagamento' => 'parcelado', 'numero_parcelas' => 1])->assertSessionHasErrors('numero_parcelas');

        $this->post('/projetos', $base + ['condicao_pagamento' => 'parcelado', 'numero_parcelas' => 10])->assertSessionHasErrors('primeiro_vencimento');
        $this->post('/projetos', $base + ['condicao_pagamento' => 'parcelado', 'numero_parcelas' => 10, 'primeiro_vencimento' => '2026-12-10'])->assertSessionHasNoErrors();
        $projeto = Projeto::firstOrFail();
        $this->assertSame('parcelado', $projeto->condicao_pagamento);
        $this->assertSame(10, $projeto->numero_parcelas);
        $this->get("/projetos/{$projeto->id}")->assertSee('Parcelado em 10x')->assertSee('R$ 1.200,00');

        // Voltando para à vista, descarta as parcelas
        $this->put("/projetos/{$projeto->id}", $base + ['condicao_pagamento' => 'a_vista', 'numero_parcelas' => 10])->assertSessionHasNoErrors();
        $projeto->refresh();
        $this->assertSame('a_vista', $projeto->condicao_pagamento);
        $this->assertNull($projeto->numero_parcelas);
    }

    public function test_listagem_mostra_datas_e_agendados_em_ordem_cronologica(): void
    {
        $this->projeto(['nome' => 'Depois', 'status' => 'agendado', 'data_inicio' => '2026-12-20', 'data_conclusao' => '2027-01-10']);
        $this->projeto(['nome' => 'Antes', 'status' => 'agendado', 'data_inicio' => '2026-12-05']);

        $this->get('/projetos?status=agendado&ano=2026')
            ->assertOk()
            ->assertSee('Agendado')
            ->assertSee('20/12/2026')
            ->assertSee('10/01/2027')
            ->assertSeeInOrder(['Antes', 'Depois']);
    }

    public function test_listagem_filtra_pelo_ano_atual_por_padrao(): void
    {
        $this->travelTo('2026-11-26 10:00:00');

        $this->projeto(['nome' => 'Projeto Deste Ano', 'data_inicio' => '2026-03-10']);
        $this->projeto(['nome' => 'Projeto Ano Passado', 'data_inicio' => '2025-08-01']);
        $this->projeto(['nome' => 'Orcamento Sem Data', 'status' => 'orcamento']); // cadastrado em 2026

        $this->assertListagem('/projetos', ['Projeto Deste Ano', 'Orcamento Sem Data']);
        $this->assertListagem('/projetos?ano=2025', ['Projeto Ano Passado']);
        $this->assertListagem('/projetos?ano=todos', ['Projeto Deste Ano', 'Projeto Ano Passado', 'Orcamento Sem Data']);
    }

    public function test_listagem_filtra_por_mes_da_data_de_inicio(): void
    {
        $this->travelTo('2026-11-26 10:00:00');

        $this->projeto(['nome' => 'Projeto Marco', 'data_inicio' => '2026-03-31']);
        $this->projeto(['nome' => 'Projeto Abril', 'data_inicio' => '2026-04-01']);

        $this->assertListagem('/projetos?ano=2026&mes=3', ['Projeto Marco']);
        $this->assertListagem('/projetos?ano=2026&mes=4', ['Projeto Abril']);
    }

    private function assertListagem(string $url, array $esperados): void
    {
        $this->get($url)->assertOk()->assertViewHas('projetos', function ($projetos) use ($esperados) {
            $nomes = collect($projetos->items())->pluck('nome')->sort()->values()->all();
            sort($esperados);

            return $nomes === $esperados;
        });
    }

    public function test_validacoes_do_projeto(): void
    {
        $inativo = TipoProjeto::create(['nome' => 'Antigo', 'ativo' => false]);

        $this->post('/projetos', [
            'nome' => '', 'cliente_id' => 999, 'tipo_projeto_id' => $inativo->id, 'status' => 'xpto',
            'data_inicio' => '2026-06-10', 'data_conclusao' => '2026-06-01',
        ])->assertSessionHasErrors(['nome', 'cliente_id', 'tipo_projeto_id', 'status', 'data_conclusao']);

        // Na edição, pode manter o tipo que foi inativado depois
        $projeto = $this->projeto(['tipo_projeto_id' => $inativo->id]);
        $this->put("/projetos/{$projeto->id}", [
            'nome' => 'Novo nome', 'cliente_id' => $this->cliente->id, 'tipo_projeto_id' => $inativo->id,
            'status' => 'concluido', 'valor_final' => '10.000,00',
        ])->assertSessionHasNoErrors();
        $this->assertSame('Novo nome', $projeto->fresh()->nome);
    }

    public function test_valor_total_nao_pode_ficar_menor_que_o_recebido(): void
    {
        $projeto = $this->projeto();
        Recebimento::create(['projeto_id' => $projeto->id, 'valor' => 6000, 'forma_pagamento' => 'pix', 'data_recebimento' => '2026-05-02']);

        $this->put("/projetos/{$projeto->id}", [
            'nome' => 'X', 'cliente_id' => $this->cliente->id, 'tipo_projeto_id' => $this->tipo->id,
            'status' => 'em_producao', 'valor_final' => '5.000,00',
        ])->assertSessionHasErrors('valor_final');
    }

    public function test_listagem_filtros_e_detalhe(): void
    {
        $projeto = $this->projeto();
        $this->projeto(['nome' => 'Painel sala', 'status' => 'orcamento']);
        Recebimento::create(['projeto_id' => $projeto->id, 'valor' => 4000, 'forma_pagamento' => 'pix', 'data_recebimento' => '2026-05-02']);
        Despesa::create(['projeto_id' => $projeto->id, 'categoria' => 'material', 'valor' => 3000, 'data_despesa' => '2026-05-03', 'status' => 'pago']);

        $this->get('/projetos')->assertOk()->assertSee('Cozinha Maria')->assertSee('Painel sala');
        // (o nome também aparece no seletor do "+ Lançar"; por isso verifica o link do card)
        $this->get('/projetos?status=orcamento')->assertSee('Painel sala')->assertDontSee('href="'.route('projetos.show', $projeto).'"', false);
        $this->get('/projetos?q=maria+oli')->assertSee('Cozinha Maria');

        // 10.000 − 3.000 − 5% (500) = 6.500 ; a receber 6.000
        $this->get("/projetos/{$projeto->id}")
            ->assertOk()
            ->assertSee('R$ 6.500,00')
            ->assertSee('R$ 6.000,00')
            ->assertSee('40% recebido');
    }

    public function test_exclusao_de_projeto(): void
    {
        $comLancamento = $this->projeto();
        Recebimento::create(['projeto_id' => $comLancamento->id, 'valor' => 100, 'forma_pagamento' => 'pix', 'data_recebimento' => '2026-05-02']);
        $this->delete("/projetos/{$comLancamento->id}")->assertSessionHas('error');
        $this->assertModelExists($comLancamento);

        $vazio = $this->projeto(['nome' => 'Vazio']);
        $this->delete("/projetos/{$vazio->id}")->assertRedirect(route('projetos.index'));
        $this->assertModelMissing($vazio);
    }

    public function test_recebimentos(): void
    {
        $projeto = $this->projeto();
        $url = "/projetos/{$projeto->id}/recebimentos";

        $this->from("/projetos/{$projeto->id}")
            ->post($url, ['valor' => '7.000,00', 'forma_pagamento' => 'pix', 'data_recebimento' => '2026-05-02'])
            ->assertRedirect("/projetos/{$projeto->id}")
            ->assertSessionHas('success');
        $recebimento = Recebimento::firstOrFail();
        $this->assertSame('7000.00', $recebimento->valor);

        // Ultrapassa o saldo (3.000)
        $this->post($url, ['valor' => '3.000,01', 'forma_pagamento' => 'pix', 'data_recebimento' => '2026-05-03'])
            ->assertSessionHasErrors('valor');
        $this->post($url, ['valor' => '0,00', 'forma_pagamento' => 'boleto', 'data_recebimento' => '2026-05-03'])
            ->assertSessionHasErrors('valor');
        $this->post($url, ['valor' => '10,00', 'forma_pagamento' => 'cripto', 'data_recebimento' => ''])
            ->assertSessionHasErrors(['forma_pagamento', 'data_recebimento']);

        // Edição ignora o próprio valor no cálculo do saldo
        $this->put("{$url}/{$recebimento->id}", ['valor' => '10.000,00', 'forma_pagamento' => 'dinheiro', 'data_recebimento' => '2026-05-02'])
            ->assertSessionHasNoErrors();
        $this->assertSame('10000.00', $recebimento->fresh()->valor);

        // Rota com escopo: recebimento de outro projeto dá 404
        $outro = $this->projeto(['nome' => 'Outro']);
        $this->put("/projetos/{$outro->id}/recebimentos/{$recebimento->id}", ['valor' => '1,00', 'forma_pagamento' => 'pix', 'data_recebimento' => '2026-05-02'])
            ->assertNotFound();

        $this->delete("{$url}/{$recebimento->id}")->assertSessionHas('success');
        $this->assertModelMissing($recebimento);
    }

    public function test_despesas(): void
    {
        $projeto = $this->projeto();
        $url = "/projetos/{$projeto->id}/despesas";

        // Paga sem data de pagamento: assume a data da despesa
        $this->post($url, ['valor' => '1.250,50', 'categoria' => 'material', 'descricao' => ' MDF ', 'data_despesa' => '2026-05-04', 'status' => 'pago', 'forma_pagamento' => 'pix'])
            ->assertSessionHasNoErrors();
        $despesa = Despesa::firstOrFail();
        $this->assertSame('1250.50', $despesa->valor);
        $this->assertSame('MDF', $despesa->descricao);
        $this->assertSame('2026-05-04', $despesa->data_pagamento->toDateString());

        // Pendente limpa os dados de pagamento
        $this->put("{$url}/{$despesa->id}", ['valor' => '1.250,50', 'categoria' => 'frete', 'data_despesa' => '2026-05-04', 'status' => 'pendente', 'data_pagamento' => '2026-05-05', 'forma_pagamento' => 'pix'])
            ->assertSessionHasNoErrors();
        $despesa->refresh();
        $this->assertSame('pendente', $despesa->status);
        $this->assertNull($despesa->data_pagamento);
        $this->assertNull($despesa->forma_pagamento);

        // Imposto não é lançável (é calculado)
        $this->post($url, ['valor' => '10,00', 'categoria' => 'imposto', 'data_despesa' => '2026-05-04'])
            ->assertSessionHasErrors('categoria');

        $this->delete("{$url}/{$despesa->id}")->assertSessionHas('success');
        $this->assertModelMissing($despesa);
    }

    public function test_lancamento_rapido_aparece_e_volta_para_a_pagina_de_origem(): void
    {
        $projeto = $this->projeto();
        $this->projeto(['nome' => 'Cancelado', 'status' => 'cancelado']);

        $this->get('/home')
            ->assertOk()
            ->assertSee('Novo lançamento')
            ->assertSee('Cozinha Maria — Maria Oliveira')
            ->assertDontSee('Cancelado — Maria Oliveira');

        $this->from('/clientes')
            ->post("/projetos/{$projeto->id}/recebimentos", ['_modal' => 'lancar', '_tipo' => 'recebimento', 'valor' => '500,00', 'forma_pagamento' => 'pix', 'data_recebimento' => '2026-05-02'])
            ->assertRedirect('/clientes');
        $this->assertSame(1, $projeto->recebimentos()->count());
    }
}
