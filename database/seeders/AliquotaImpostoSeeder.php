<?php

namespace Database\Seeders;

use App\Models\AliquotaImposto;
use Illuminate\Database\Seeder;

class AliquotaImpostoSeeder extends Seeder
{
    public function run(): void
    {
        if (AliquotaImposto::query()->exists()) {
            return;
        }

        AliquotaImposto::query()->create([
            'percentual' => 4.50,
            'vigente_desde' => now()->toDateString(),
        ]);
    }
}
