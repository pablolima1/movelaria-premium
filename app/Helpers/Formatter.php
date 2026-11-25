<?php

namespace App\Helpers;

class Formatter
{
    public static function money(float|int|string|null $value): string
    {
        return 'R$ ' . number_format((float) $value, 2, ',', '.');
    }

    public static function moneyShort(float|int|string|null $value): string
    {
        $value = (float) $value;
        $abs = abs($value);
        $sign = $value < 0 ? '-' : '';

        if ($abs >= 1_000_000) {
            return $sign . 'R$ ' . number_format($abs / 1_000_000, 1, ',', '.') . ' mi';
        }

        if ($abs >= 10_000) {
            return $sign . 'R$ ' . number_format($abs / 1_000, 1, ',', '.') . ' mil';
        }

        return $sign . 'R$ ' . number_format($abs, 2, ',', '.');
    }

    public static function percent(float|int|string|null $value): string
    {
        return number_format((float) $value, 2, ',', '.') . '%';
    }
}
