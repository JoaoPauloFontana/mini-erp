<?php

namespace App\Enums;

enum StatusPedido: string
{
    case PENDENTE = 'pendente';
    case CONFIRMADO = 'confirmado';
    case ENVIADO = 'enviado';
    case ENTREGUE = 'entregue';
    case CANCELADO = 'cancelado';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::PENDENTE => 'Pendente',
            self::CONFIRMADO => 'Confirmado',
            self::ENVIADO => 'Enviado',
            self::ENTREGUE => 'Entregue',
            self::CANCELADO => 'Cancelado',
        };
    }

    public static function isValid(string $status): bool
    {
        return in_array($status, self::values());
    }
}
