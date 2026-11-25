<?php

namespace App\Services;

use App\Enums\EventoLog;
use App\Models\Log;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Throwable;

class LogService
{
    public function __construct(private readonly Request $request)
    {
    }

    /**
     * Registra um evento no log. Nunca interrompe o fluxo da aplicação:
     * se a gravação falhar, o erro é apenas reportado.
     *
     * @param  Authenticatable|null  $user  padrão: usuário autenticado
     * @param  Model|null  $referencia  registro afetado (projeto, cliente...)
     */
    public function registrar(
        EventoLog|string $evento,
        ?Authenticatable $user = null,
        ?Model $referencia = null,
        array $dados = [],
        ?string $descricao = null,
    ): ?Log {
        try {
            $user ??= Auth::user();

            return Log::create([
                'evento' => $evento instanceof EventoLog ? $evento->value : $evento,
                'user_id' => $user?->getAuthIdentifier(),
                'referencia_type' => $referencia?->getMorphClass(),
                'referencia_id' => $referencia?->getKey(),
                'descricao' => $descricao !== null ? Str::limit($descricao, 252) : null,
                'dados' => $dados ?: null,
                'ip' => $this->request->ip(),
                'user_agent' => Str::limit((string) $this->request->userAgent(), 252) ?: null,
            ]);
        } catch (Throwable $e) {
            report($e);

            return null;
        }
    }
}
