<?php

namespace App\Enums;

enum StatusPedido: string
{
    case PENDENTE = 'pendente';
    case CONFIRMADO = 'confirmado';
    case ENVIADO = 'enviado';
    case ENTREGUE = 'entregue';
    case CANCELADO = 'cancelado';

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
            self::PENDENTE => 'Pendente',
            self::CONFIRMADO => 'Confirmado',
            self::ENVIADO => 'Enviado',
            self::ENTREGUE => 'Entregue',
            self::CANCELADO => 'Cancelado',
        };
    }

    /**
     * Verificar se é um status válido
     */
    public static function isValid(string $status): bool
    {
        return in_array($status, self::values());
    }
}
