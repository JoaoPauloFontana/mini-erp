<?php

namespace App\Enums;

enum TipoCupom: string
{
    case PERCENTUAL = 'percentual';
    case VALOR_FIXO = 'valor_fixo';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::PERCENTUAL => 'Percentual',
            self::VALOR_FIXO => 'Valor Fixo',
        };
    }

    public static function isValid(string $tipo): bool
    {
        return in_array($tipo, self::values());
    }
}
