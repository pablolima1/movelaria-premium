<?php

namespace App\Enums;

/**
 * Categorias lançáveis pelo usuário. O imposto não é uma delas:
 * ele é calculado automaticamente a partir da alíquota vigente.
 */
enum CategoriaDespesa: string
{
    case Material = 'material';
    case MaoDeObra = 'mao_de_obra';
    case Frete = 'frete';
    case Instalacao = 'instalacao';
    case Outros = 'outros';

    public function label(): string
    {
        return match ($this) {
            self::Material => 'Material',
            self::MaoDeObra => 'Mão de obra',
            self::Frete => 'Frete',
            self::Instalacao => 'Instalação',
            self::Outros => 'Outros',
        };
    }

    public static function labelFor(?string $value): string
    {
        return $value === null ? '—' : (self::tryFrom($value)?->label() ?? ucfirst(str_replace('_', ' ', $value)));
    }

    public static function options(): array
    {
        return array_column(array_map(fn (self $c) => ['v' => $c->value, 'l' => $c->label()], self::cases()), 'l', 'v');
    }
}
