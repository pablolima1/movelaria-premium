<?php

namespace App\Enums;

enum FormaPagamento: string
{
    case Pix = 'pix';
    case Dinheiro = 'dinheiro';
    case CartaoCredito = 'cartao_credito';
    case CartaoDebito = 'cartao_debito';
    case Boleto = 'boleto';
    case Transferencia = 'transferencia';
    case Cheque = 'cheque';

    public function label(): string
    {
        return match ($this) {
            self::Pix => 'Pix',
            self::Dinheiro => 'Dinheiro',
            self::CartaoCredito => 'Cartão de crédito',
            self::CartaoDebito => 'Cartão de débito',
            self::Boleto => 'Boleto',
            self::Transferencia => 'Transferência',
            self::Cheque => 'Cheque',
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
