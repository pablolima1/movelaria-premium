<?php

namespace App\Enums;

enum StatusParcela: string
{
    case AVencer = 'a_vencer';
    case Atrasada = 'atrasada';
    case Paga = 'paga';

    public function label(): string
    {
        return match ($this) {
            self::AVencer => 'A vencer',
            self::Atrasada => 'Atrasada',
            self::Paga => 'Paga',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::AVencer => 'bg-blue-light-50 text-blue-light-500 dark:bg-blue-light-500/15 dark:text-blue-light-500',
            self::Atrasada => 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500',
            self::Paga => 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-500',
        };
    }
}
