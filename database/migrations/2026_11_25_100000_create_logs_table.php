<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Log genérico do sistema (acessos, alterações, etc.).
     * Registros são imutáveis: só created_at.
     */
    public function up(): void
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->string('evento', 60)->index();          // ex.: auth.login, projeto.excluido
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->nullableMorphs('referencia');            // registro afetado (opcional)
            $table->string('descricao')->nullable();
            $table->json('dados')->nullable();               // contexto extra do evento
            $table->string('ip', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
