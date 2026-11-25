<?php

namespace App\Listeners;

use App\Enums\EventoLog;
use App\Services\LogService;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

/**
 * Registra acessos ao sistema na tabela `logs`.
 * Descoberto automaticamente pelo Laravel (app/Listeners).
 */
class RegistrarAcessoNoLog
{
    public function __construct(private readonly LogService $log)
    {
    }

    public function handleLogin(Login $event): void
    {
        $this->log->registrar(
            EventoLog::Login,
            $event->user,
            dados: ['lembrar' => $event->remember],
            descricao: "{$event->user->name} entrou no sistema",
        );
    }

    public function handleLogout(Logout $event): void
    {
        if (! $event->user) {
            return;
        }

        $this->log->registrar(EventoLog::Logout, $event->user, descricao: "{$event->user->name} saiu do sistema");
    }

    public function handleFailed(Failed $event): void
    {
        // Nunca guarda a senha: só o e-mail tentado.
        $email = (string) ($event->credentials['email'] ?? '');

        $this->log->registrar(
            EventoLog::LoginFalhou,
            $event->user,
            dados: ['email' => $email, 'usuario_existe' => $event->user !== null],
            descricao: "Tentativa de login falhou para {$email}",
        );
    }
}
