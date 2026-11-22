<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projetos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->foreignId('tipo_projeto_id')->constrained('tipos_projeto')->restrictOnDelete();
            $table->string('nome');
            $table->string('status')->default('orcamento');
            $table->decimal('valor_final', 12, 2)->nullable();
            $table->date('data_inicio')->nullable();
            $table->date('data_conclusao')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projetos');
    }
};
