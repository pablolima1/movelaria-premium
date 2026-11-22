<?php

namespace Database\Seeders;

use App\Models\TipoProjeto;
use Illuminate\Database\Seeder;

class TiposProjetoSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Guarda-roupa', 'Painel TV', 'Closet', 'Armário de cozinha'] as $nome) {
            TipoProjeto::query()->firstOrCreate(
                ['nome' => $nome],
                ['ativo' => true],
            );
        }
    }
}
