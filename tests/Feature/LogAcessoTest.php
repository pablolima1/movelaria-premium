<?php

namespace Tests\Feature;

use App\Enums\EventoLog;
use App\Models\Log;
use App\Models\User;
use App\Services\LogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogAcessoTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_bem_sucedido_e_logado(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        $this->withHeader('User-Agent', 'Mozilla/5.0 (iPhone)')
            ->post('/login', ['email' => $user->email, 'password' => 'password', 'remember' => true])
            ->assertRedirect('/home');

        $log = Log::doEvento(EventoLog::Login)->sole();
        $this->assertSame($user->id, $log->user_id);
        $this->assertSame(['lembrar' => true], $log->dados);
        $this->assertSame('127.0.0.1', $log->ip);
        $this->assertSame('Mozilla/5.0 (iPhone)', $log->user_agent);
        $this->assertStringContainsString($user->name, $log->descricao);
        $this->assertNotNull($log->created_at);
    }

    public function test_logout_e_logado(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/logout');

        $this->assertSame($user->id, Log::doEvento(EventoLog::Logout)->sole()->user_id);
    }

    public function test_login_falho_guarda_email_mas_nunca_a_senha(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        $this->post('/login', ['email' => $user->email, 'password' => 'senha-errada'])->assertSessionHasErrors();
        $this->post('/login', ['email' => 'ninguem@exemplo.com', 'password' => 'outra-senha']);

        $logs = Log::doEvento(EventoLog::LoginFalhou)->orderBy('id')->get();
        $this->assertCount(2, $logs);
        $this->assertSame($user->id, $logs[0]->user_id);
        $this->assertSame(['email' => $user->email, 'usuario_existe' => true], $logs[0]->dados);
        $this->assertNull($logs[1]->user_id);
        $this->assertFalse($logs[1]->dados['usuario_existe']);

        $this->assertDatabaseMissing('logs', ['dados' => null, 'evento' => EventoLog::LoginFalhou->value]);
        foreach (Log::all() as $log) {
            $this->assertStringNotContainsString('senha-errada', json_encode($log->toArray()));
        }

        $this->assertSame(0, Log::doEvento(EventoLog::Login)->count());
    }

    public function test_service_aceita_eventos_futuros_com_referencia(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $log = app(LogService::class)->registrar('projeto.excluido', referencia: $user, dados: ['nome' => 'X']);

        $this->assertSame($user->id, $log->user_id); // usa o usuário autenticado por padrão
        $this->assertTrue($log->referencia->is($user));
        $this->assertSame('projeto.excluido', $log->evento);
    }
}
