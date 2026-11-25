<?php

namespace App\Enums;

/** Como o cliente vai pagar o projeto. */
enum CondicaoPagamento: string
{
    case AVista = 'a_vista';
    case Parcelado = 'parcelado';

    public const MAX_PARCELAS = 24;

    public function label(): string
    {
        return match ($this) {
            self::AVista => 'À vista',
            self::Parcelado => 'Parcelado',
        };
    }

    public static function options(): array
    {
        return array_column(array_map(fn (self $c) => ['v' => $c->value, 'l' => $c->label()], self::cases()), 'l', 'v');
    }
}
