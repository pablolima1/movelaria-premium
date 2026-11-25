<?php

namespace App\Enums;

/**
 * Tipos de evento registrados na tabela `logs`.
 * Para logar algo novo, adicione um case aqui e chame LogService::registrar().
 */
enum EventoLog: string
{
    case Login = 'auth.login';
    case Logout = 'auth.logout';
    case LoginFalhou = 'auth.login_falhou';

    public function label(): string
    {
        return match ($this) {
            self::Login => 'Login',
            self::Logout => 'Logout',
            self::LoginFalhou => 'Tentativa de login falhou',
        };
    }

    public static function labelFor(?string $value): string
    {
        return $value === null ? '—' : (self::tryFrom($value)?->label() ?? $value);
    }
}
