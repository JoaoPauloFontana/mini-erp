<?php

namespace App\Constants;

class SystemConstants
{
    public const HTTP_OK = 200;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_UNPROCESSABLE_ENTITY = 422;
    public const HTTP_INTERNAL_SERVER_ERROR = 500;

    public const MAX_STRING_LENGTH = 255;
    public const MIN_NUMERIC_VALUE = 0;
    public const MIN_QUANTITY = 0;
    public const ZERO_QUANTITY = 0;

    public const FRETE_GRATIS_LIMITE = 200.00;
    public const FRETE_PROMOCIONAL_MIN = 52.00;
    public const FRETE_PROMOCIONAL_MAX = 166.59;
    public const FRETE_PROMOCIONAL_VALOR = 15.00;
    public const FRETE_NORMAL_VALOR = 20.00;
    public const FRETE_GRATIS_VALOR = 0.00;

    public const PEDIDOS_RECENTES_LIMITE = 10;

    public const SESSION_CARRINHO = 'carrinho';
    public const SESSION_CUPOM_APLICADO = 'cupom_aplicado';
    public const SESSION_DESCONTO = 'desconto';
    public const SESSION_PEDIDO_CRIADO = 'pedido_criado';

    public const CARRINHO_SEM_VARIACAO = 'sem_variacao';
    public const CARRINHO_SEPARADOR = '_';

    public const VIACEP_URL = 'https://viacep.com.br/ws';

    public const VALOR_PADRAO_ZERO = 0;
    public const VALOR_PADRAO_NULO = null;
    public const PRODUTO_ATIVO_PADRAO = true;
    public const PRODUTO_INATIVO = false;

    public const MSG_PRODUTO_CRIADO = 'Produto criado com sucesso!';
    public const MSG_PRODUTO_ATUALIZADO = 'Produto atualizado com sucesso!';
    public const MSG_PRODUTO_REMOVIDO = 'Produto removido com sucesso!';
    public const MSG_VARIACAO_ADICIONADA = 'Variação adicionada com sucesso!';
    public const MSG_PRODUTO_ADICIONADO_CARRINHO = 'Produto adicionado ao carrinho!';
    public const MSG_CARRINHO_ATUALIZADO = 'Carrinho atualizado!';
    public const MSG_ITEM_REMOVIDO_CARRINHO = 'Item removido do carrinho!';
    public const MSG_CUPOM_APLICADO = 'Cupom aplicado com sucesso!';
    public const MSG_CUPOM_REMOVIDO = 'Cupom removido';
    public const MSG_CEP_ENCONTRADO = 'CEP encontrado';
    public const MSG_PEDIDO_REALIZADO = 'Pedido realizado com sucesso! Você receberá um email de confirmação.';
    public const MSG_STATUS_ATUALIZADO = 'Status atualizado com sucesso';
    public const MSG_PEDIDO_CANCELADO = 'Pedido cancelado e removido';

    public const MSG_ESTOQUE_INSUFICIENTE = 'Estoque insuficiente';
    public const MSG_ITEM_NAO_ENCONTRADO = 'Item não encontrado no carrinho';
    public const MSG_CEP_NAO_ENCONTRADO = 'CEP não encontrado';
    public const MSG_ERRO_CONSULTAR_CEP = 'Erro ao consultar CEP';
    public const MSG_CUPOM_INVALIDO = 'Cupom não encontrado ou inválido';
    public const MSG_CARRINHO_VAZIO = 'Carrinho vazio';
    public const MSG_PEDIDO_NAO_ENCONTRADO = 'Pedido não encontrado';
    public const MSG_ERRO_INTERNO = 'Erro interno do servidor';
    public const MSG_ERRO_PROCESSAR_PEDIDO = 'Erro ao processar pedido: ';

    public const WEBHOOK_ACAO_STATUS_ATUALIZADO = 'status_atualizado';
    public const WEBHOOK_ACAO_PEDIDO_REMOVIDO = 'pedido_removido_estoque_devolvido';
}
