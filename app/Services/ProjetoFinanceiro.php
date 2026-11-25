<?php

namespace App\Services;

use App\Models\Projeto;

/**
 * Números financeiros de um projeto. Espera os agregados `recebido_total`
 * e `material_total` (withSum) já carregados; se não houver, calcula pelas relações.
 * Valores somados em centavos para evitar erro de arredondamento.
 */
class ProjetoFinanceiro
{
    public const CATEGORIA_IMPOSTO = 'imposto';

    public function __construct(private readonly AliquotaResolver $aliquotas)
    {
    }

    public function resumo(Projeto $projeto): array
    {
        $aliquota = $this->aliquotas->paraProjeto($projeto);

        $total = self::cents($projeto->valor_final);
        $attrs = $projeto->getAttributes();
        $recebido = self::cents(array_key_exists('recebido_total', $attrs)
            ? $attrs['recebido_total']
            : $projeto->recebimentos()->sum('valor'));
        $material = self::cents(array_key_exists('material_total', $attrs)
            ? $attrs['material_total']
            : $projeto->despesas()->where('categoria', '!=', self::CATEGORIA_IMPOSTO)->sum('valor'));
        $imposto = (int) round($total * ($aliquota ?? 0) / 100);

        return [
            'aliquota' => $aliquota,
            'valor_total' => $total / 100,
            'recebido_total' => $recebido / 100,
            'a_receber' => max(0, $total - $recebido) / 100,
            'material' => $material / 100,
            'imposto' => $imposto / 100,
            'lucro_liquido' => ($total - $material - $imposto) / 100,
            'progresso' => $total > 0 ? min(100, (int) round($recebido / $total * 100)) : null,
        ];
    }

    public static function cents(mixed $valor): int
    {
        return (int) round(((float) ($valor ?? 0)) * 100);
    }
}
