<?php

namespace Tests\Feature;

use App\Models\AliquotaImposto;
use App\Models\Cliente;
use App\Models\Parcela;
use App\Models\Projeto;
use App\Models\TipoProjeto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ParcelaTest extends TestCase
{
    use RefreshDatabase;

    private Cliente $cliente;

    private TipoProjeto $tipo;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-11-24');
        $this->actingAs(User::factory()->create());
        AliquotaImposto::create(['percentual' => 5.00, 'vigente_desde' => '2020-01-01']);
        $this->cliente = Cliente::create(['nome' => 'Maria Oliveira', 'telefone' => '(11) 98888-7777']);
        $this->tipo = TipoProjeto::create(['nome' => 'Cozinha', 'ativo' => true]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function dados(array $attrs = []): array
    {
        return $attrs + [
            'nome' => 'Cozinha Maria',
            'cliente_id' => $this->cliente->id,
            'tipo_projeto_id' => $this->tipo->id,
            'status' => 'agendado',
            'valor_final' => '10.000,00',
            'condicao_pagamento' => 'parcelado',
            'numero_parcelas' => 3,
            'primeiro_vencimento' => '2026-01-31',
        ];
    }

    private function criar(array $attrs = []): Projeto
    {
        $this->post('/projetos', $this->dados($attrs))->assertSessionHasNoErrors();

        return Projeto::latest('id')->firstOrFail();
    }

    public function test_gera_parcelas_mensais_com_centavos_na_ultima(): void
    {
        $projeto = $this->criar();

        $parcelas = $projeto->parcelas()->get();
        $this->assertSame([1, 2, 3], $parcelas->pluck('numero')->all());
        $this->assertSame(['3333.33', '3333.33', '3333.34'], $parcelas->pluck('valor')->all());
        // Fim de mês não "pula" fevereiro
        $this->assertSame(['2026-01-31', '2026-02-28', '2026-03-31'], $parcelas->map(fn ($p) => $p->vencimento->toDateString())->all());
    }

    public function test_a_vista_nao_gera_parcelas(): void
    {
        $projeto = $this->criar(['condicao_pagamento' => 'a_vista']);

        $this->assertSame(0, $projeto->parcelas()->count());
    }

    public function test_receber_parcela_lanca_recebimento_e_excluir_reabre(): void
    {
        $projeto = $this->criar();
        $parcela = $projeto->parcelas()->first();

        $this->from('/cobrancas')
            ->post("/parcelas/{$parcela->id}/receber", ['data_recebimento' => '2026-01-30', 'forma_pagamento' => 'pix'])
            ->assertRedirect('/cobrancas')
            ->assertSessionHas('success');

        $parcela->refresh();
        $this->assertTrue($parcela->isPaga());
        $this->assertSame('3333.33', $parcela->recebimento->valor);
        $this->assertSame('Parcela 1 de 3', $parcela->recebimento->observacoes);

        // Não recebe duas vezes
        $this->post("/parcelas/{$parcela->id}/receber", ['data_recebimento' => '2026-01-30', 'forma_pagamento' => 'pix'])
            ->assertSessionHasErrors('parcela');

        // Excluir o recebimento reabre a parcela
        $this->delete("/projetos/{$projeto->id}/recebimentos/{$parcela->recebimento_id}");
        $this->assertFalse($parcela->fresh()->isPaga());
    }

    public function test_alterar_plano_preserva_parcelas_pagas(): void
    {
        $projeto = $this->criar();
        $primeira = $projeto->parcelas()->first();
        $this->post("/parcelas/{$primeira->id}/receber", ['data_recebimento' => '2026-01-30', 'forma_pagamento' => 'pix']);

        // Restante (6.666,67) em 4 parcelas -> total 5
        $this->put("/projetos/{$projeto->id}", $this->dados(['numero_parcelas' => 5]))->assertSessionHasNoErrors();

        $parcelas = $projeto->parcelas()->get();
        $this->assertSame([1, 2, 3, 4, 5], $parcelas->pluck('numero')->all());
        $this->assertTrue($parcelas[0]->isPaga());
        $this->assertSame($primeira->id, $parcelas[0]->id);
        $this->assertSame(['1666.66', '1666.66', '1666.66', '1666.69'], $parcelas->slice(1)->pluck('valor')->values()->all());

        // Não pode ter menos parcelas que as já pagas com saldo restante
        $this->put("/projetos/{$projeto->id}", $this->dados(['numero_parcelas' => 1]))->assertSessionHasErrors('numero_parcelas');

        // Mudar para à vista remove só as em aberto
        $this->put("/projetos/{$projeto->id}", $this->dados(['condicao_pagamento' => 'a_vista']))->assertSessionHasNoErrors();
        $this->assertSame([$primeira->id], $projeto->parcelas()->pluck('id')->all());
    }

    public function test_editar_outros_campos_nao_refaz_parcelas_remarcadas(): void
    {
        $projeto = $this->criar();
        $segunda = $projeto->parcelas()->where('numero', 2)->first();

        $this->patch("/parcelas/{$segunda->id}", ['vencimento' => '2026-03-15'])->assertSessionHasNoErrors();
        $this->put("/projetos/{$projeto->id}", $this->dados(['nome' => 'Cozinha Maria (ajustada)']))->assertSessionHasNoErrors();

        $this->assertSame('2026-03-15', $segunda->fresh()->vencimento->toDateString());
    }

    public function test_tela_de_cobrancas(): void
    {
        // Jan/2026 vencida (atrasada), fev e mar também já passaram em 24/11/2026; cria outro futuro
        $atrasado = $this->criar(['nome' => 'Closet atrasado']);
        $futuro = $this->criar(['nome' => 'Painel futuro', 'primeiro_vencimento' => '2026-12-10']);
        $cancelado = $this->criar(['nome' => 'Cancelado X', 'primeiro_vencimento' => '2026-12-10']);
        $cancelado->update(['status' => 'cancelado']);

        $this->get('/cobrancas')
            ->assertOk()
            ->assertSee('Closet atrasado')
            ->assertSee('Painel futuro')
            ->assertDontSee('href="'.route('projetos.show', $cancelado).'"', false)
            ->assertSee('R$ 10.000,00') // em atraso
            ->assertSee('wa.me/5511988887777', false)
            ->assertSeeInOrder(['Closet atrasado', 'Painel futuro']);

        // (o nome também aparece no seletor do "+ Lançar"; por isso verifica o link)
        $this->get('/cobrancas?aba=atrasadas')
            ->assertSee('href="'.route('projetos.show', $atrasado).'"', false)
            ->assertDontSee('href="'.route('projetos.show', $futuro).'"', false);

        $parcela = $futuro->parcelas()->first();
        $this->post("/parcelas/{$parcela->id}/receber", ['data_recebimento' => '2026-11-24', 'forma_pagamento' => 'boleto']);
        $this->get('/cobrancas?aba=pagas')->assertSee('Painel futuro')->assertSee('Recebida em 24/11/2026');

        $this->get("/projetos/{$atrasado->id}")->assertOk()->assertSee('Parcelas')->assertSee('3 em atraso');
    }
}
