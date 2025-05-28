-- Mini ERP - Estrutura do Banco de Dados
-- Sistema de Controle de Pedidos, Produtos, Cupons e Estoque
-- Desenvolvido em Laravel com MySQL

CREATE DATABASE IF NOT EXISTS mini_erp;
USE mini_erp;

-- Tabela de Produtos
CREATE TABLE produtos (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    descricao TEXT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    INDEX idx_nome_ativo (nome, ativo)
);

-- Tabela de Variações de Produtos
CREATE TABLE produto_variacoes (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    produto_id BIGINT UNSIGNED NOT NULL,
    nome VARCHAR(255) NOT NULL,
    valor_adicional DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    INDEX idx_produto_ativo (produto_id, ativo),
    CONSTRAINT fk_produto_variacoes_produto FOREIGN KEY (produto_id) REFERENCES produtos (id) ON DELETE CASCADE
);

-- Tabela de Estoque
CREATE TABLE estoque (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    produto_id BIGINT UNSIGNED NOT NULL,
    variacao_id BIGINT UNSIGNED NULL,
    quantidade INT NOT NULL DEFAULT 0,
    quantidade_minima INT NOT NULL DEFAULT 5,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uk_produto_variacao (produto_id, variacao_id),
    INDEX idx_produto_quantidade (produto_id, quantidade),
    CONSTRAINT fk_estoque_produto FOREIGN KEY (produto_id) REFERENCES produtos (id) ON DELETE CASCADE,
    CONSTRAINT fk_estoque_variacao FOREIGN KEY (variacao_id) REFERENCES produto_variacoes (id) ON DELETE CASCADE
);

-- Tabela de Cupons
CREATE TABLE cupons (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    codigo VARCHAR(50) NOT NULL,
    tipo ENUM('percentual', 'valor_fixo') NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    valor_minimo DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    data_inicio DATE NOT NULL,
    data_fim DATE NOT NULL,
    limite_uso INT NULL,
    usado INT NOT NULL DEFAULT 0,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uk_codigo (codigo),
    INDEX idx_codigo_ativo (codigo, ativo),
    INDEX idx_data_validade (data_inicio, data_fim)
);

-- Tabela de Pedidos
CREATE TABLE pedidos (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    numero_pedido VARCHAR(20) NOT NULL,
    cliente_nome VARCHAR(255) NOT NULL,
    cliente_email VARCHAR(255) NOT NULL,
    cliente_telefone VARCHAR(20) NULL,
    cep VARCHAR(10) NOT NULL,
    endereco TEXT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    desconto DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    frete DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    cupom_id BIGINT UNSIGNED NULL,
    status ENUM('pendente', 'confirmado', 'enviado', 'entregue', 'cancelado') NOT NULL DEFAULT 'pendente',
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uk_numero_pedido (numero_pedido),
    INDEX idx_numero_status (numero_pedido, status),
    INDEX idx_email_status (cliente_email, status),
    CONSTRAINT fk_pedidos_cupom FOREIGN KEY (cupom_id) REFERENCES cupons (id)
);

-- Tabela de Itens do Pedido
CREATE TABLE pedido_itens (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    pedido_id BIGINT UNSIGNED NOT NULL,
    produto_id BIGINT UNSIGNED NOT NULL,
    variacao_id BIGINT UNSIGNED NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    INDEX idx_pedido (pedido_id),
    CONSTRAINT fk_pedido_itens_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos (id) ON DELETE CASCADE,
    CONSTRAINT fk_pedido_itens_produto FOREIGN KEY (produto_id) REFERENCES produtos (id),
    CONSTRAINT fk_pedido_itens_variacao FOREIGN KEY (variacao_id) REFERENCES produto_variacoes (id)
);

-- Inserir dados de exemplo
INSERT INTO produtos (nome, preco, descricao) VALUES
('Camiseta Básica', 29.90, 'Camiseta 100% algodão'),
('Calça Jeans', 89.90, 'Calça jeans tradicional'),
('Tênis Esportivo', 159.90, 'Tênis para corrida e caminhada');

-- Inserir variações para a camiseta
INSERT INTO produto_variacoes (produto_id, nome, valor_adicional) VALUES
(1, 'P', 0.00),
(1, 'M', 0.00),
(1, 'G', 5.00);

-- Inserir estoque
INSERT INTO estoque (produto_id, variacao_id, quantidade) VALUES
(1, 1, 50),  -- Camiseta P
(1, 2, 75),  -- Camiseta M
(1, 3, 30),  -- Camiseta G
(2, NULL, 25), -- Calça Jeans
(3, NULL, 15); -- Tênis Esportivo

-- Inserir cupons de exemplo
INSERT INTO cupons (codigo, tipo, valor, valor_minimo, data_inicio, data_fim, limite_uso) VALUES
('DESCONTO10', 'percentual', 10.00, 50.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 YEAR), 100),
('FRETE15', 'valor_fixo', 15.00, 100.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 YEAR), 50);

-- Comentários sobre as regras de negócio:
-- 
-- FRETE:
-- - Grátis para pedidos >= R$ 200,00
-- - R$ 15,00 para pedidos entre R$ 52,00 e R$ 166,59
-- - R$ 20,00 para outros valores
--
-- CUPONS:
-- - Validação por período (data_inicio e data_fim)
-- - Valor mínimo do carrinho
-- - Limite de uso (opcional)
-- - Tipos: percentual ou valor_fixo
--
-- ESTOQUE:
-- - Controle por produto e variação
-- - Redução automática ao confirmar pedido
-- - Devolução automática ao cancelar pedido
--
-- WEBHOOK:
-- - Endpoint: POST /webhook/status
-- - Parâmetros: pedido_id, status
-- - Status cancelado: remove pedido e devolve estoque
-- - Outros status: apenas atualiza o status
