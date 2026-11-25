<?php

namespace App\Enums;

enum StatusDespesa: string
{
    case Pendente = 'pendente';
    case Pago = 'pago';

    public function label(): string
    {
        return match ($this) {
            self::Pendente => 'Pendente',
            self::Pago => 'Pago',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pendente => 'bg-warning-50 text-warning-700 dark:bg-warning-500/15 dark:text-orange-400',
            self::Pago => 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-500',
        };
    }

    public static function options(): array
    {
        return array_column(array_map(fn (self $c) => ['v' => $c->value, 'l' => $c->label()], self::cases()), 'l', 'v');
    }
}
