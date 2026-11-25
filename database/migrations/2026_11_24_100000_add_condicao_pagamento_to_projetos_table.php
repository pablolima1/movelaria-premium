<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projetos', function (Blueprint $table) {
            $table->string('condicao_pagamento', 20)->default('a_vista')->after('valor_final');
            $table->unsignedSmallInteger('numero_parcelas')->nullable()->after('condicao_pagamento');
        });
    }

    public function down(): void
    {
        Schema::table('projetos', function (Blueprint $table) {
            $table->dropColumn(['condicao_pagamento', 'numero_parcelas']);
        });
    }
};
