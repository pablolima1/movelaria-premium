<?php

namespace App\Enums;

enum StatusProjeto: string
{
    case Orcamento = 'orcamento';
    case Agendado = 'agendado'; // aprovado e com produção agendada
    case EmProducao = 'em_producao';
    case Concluido = 'concluido';
    case Cancelado = 'cancelado';

    public function label(): string
    {
        return match ($this) {
            self::Orcamento => 'Orçamento',
            self::Agendado => 'Agendado',
            self::EmProducao => 'Em produção',
            self::Concluido => 'Concluído',
            self::Cancelado => 'Cancelado',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Orcamento => 'bg-blue-light-50 text-blue-light-500 dark:bg-blue-light-500/15 dark:text-blue-light-500',
            self::Agendado => 'bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400',
            self::EmProducao => 'bg-warning-50 text-warning-700 dark:bg-warning-500/15 dark:text-orange-400',
            self::Concluido => 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-500',
            self::Cancelado => 'bg-gray-100 text-gray-700 dark:bg-white/5 dark:text-white/80',
        };
    }

    public static function options(): array
    {
        return array_column(array_map(fn (self $c) => ['v' => $c->value, 'l' => $c->label()], self::cases()), 'l', 'v');
    }
}
