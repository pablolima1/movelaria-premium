<?php

namespace App\Http\Requests\Concerns;

trait NormalizesMoney
{
    /**
     * Converte valores da máscara pt-BR em decimal: "1.234,56" -> "1234.56" e
     * "20.000" (sem centavos) -> "20000". Valores no formato americano
     * ("1234.56") passam direto.
     */
    protected function normalizeMoney(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = str_replace(['R$', ' ', "\u{A0}"], '', trim((string) $value));

        if (str_contains($value, ',')) {
            return str_replace(['.', ','], ['', '.'], $value);
        }

        // Só pontos de milhar ("20.000", "1.250.000"): nunca é decimal.
        if (preg_match('/^-?\d{1,3}(\.\d{3})+$/', $value)) {
            return str_replace('.', '', $value);
        }

        return $value;
    }

    protected function emptyToNull(array $fields): array
    {
        $data = [];
        foreach ($fields as $field) {
            $value = $this->input($field);
            $data[$field] = is_string($value) && trim($value) === '' ? null : $value;
        }

        return $data;
    }
}
