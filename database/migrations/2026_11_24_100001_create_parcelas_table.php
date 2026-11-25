<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projetos', function (Blueprint $table) {
            $table->date('primeiro_vencimento')->nullable()->after('numero_parcelas');
        });

        Schema::create('parcelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projeto_id')->constrained('projetos')->cascadeOnDelete();
            $table->unsignedSmallInteger('numero');
            $table->decimal('valor', 12, 2);
            $table->date('vencimento');
            // Preenchido quando a parcela é recebida; excluir o recebimento reabre a parcela.
            $table->foreignId('recebimento_id')->nullable()->unique()->constrained('recebimentos')->nullOnDelete();
            $table->timestamps();

            $table->unique(['projeto_id', 'numero']);
            $table->index(['recebimento_id', 'vencimento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parcelas');

        Schema::table('projetos', function (Blueprint $table) {
            $table->dropColumn('primeiro_vencimento');
        });
    }
};
