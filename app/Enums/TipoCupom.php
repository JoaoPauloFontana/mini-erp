<?php

namespace App\Enums;

enum TipoCupom: string
{
    case PERCENTUAL = 'percentual';
    case VALOR_FIXO = 'valor_fixo';

    /**
     * Obter todos os valores como array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Obter labels amigáveis
     */
    public function label(): string
    {
        return match($this) {
            self::PERCENTUAL => 'Percentual',
            self::VALOR_FIXO => 'Valor Fixo',
        };
    }

    /**
     * Verificar se é um tipo válido
     */
    public static function isValid(string $tipo): bool
    {
        return in_array($tipo, self::values());
    }
}
