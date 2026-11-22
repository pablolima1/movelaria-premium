<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('despesas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projeto_id')->constrained('projetos')->restrictOnDelete();
            $table->string('categoria', 30);
            $table->string('descricao')->nullable();
            $table->decimal('valor', 12, 2);
            $table->decimal('aliquota_aplicada', 5, 2)->nullable();
            $table->date('data_despesa');
            $table->string('status', 20)->default('pendente');
            $table->date('data_pagamento')->nullable();
            $table->string('forma_pagamento', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('despesas');
    }
};
