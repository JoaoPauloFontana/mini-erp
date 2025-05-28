# Mini ERP - Sistema de Controle de Pedidos, Produtos, Cupons e Estoque

Um sistema completo de ERP desenvolvido em **Laravel** com **Bootstrap** para gerenciamento de produtos, estoque, pedidos e cupons de desconto.

## 🚀 Características

- **Gestão de Produtos**: Cadastro completo com variações e controle de estoque
- **Carrinho de Compras**: Sistema de carrinho em sessão com cálculo automático de frete
- **Sistema de Cupons**: Cupons com validação de período, valor mínimo e limite de uso
- **Controle de Estoque**: Gestão automática com redução/devolução de estoque
- **Cálculo de Frete**: Regras automáticas baseadas no valor do pedido
- **Verificação de CEP**: Integração com API ViaCEP
- **Webhook**: API para atualização de status de pedidos
- **Interface Responsiva**: Design moderno com Bootstrap 5

## 🛠️ Tecnologias Utilizadas

- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend**: Bootstrap 5, jQuery
- **Banco de Dados**: MySQL 8.0
- **Containerização**: Docker + Docker Compose
- **Servidor Web**: Nginx
- **Arquitetura**: MVC (Model-View-Controller)

## 📋 Pré-requisitos

- Docker
- Docker Compose
- Git

## 🔧 Instalação e Configuração

### 1. Clone o repositório

```bash
git clone https://github.com/JoaoPauloFontana/mini-erp.git
cd mini-erp
```

### 2. Inicie os containers Docker

```bash
docker-compose up -d
```

### 3. Execute as migrations e seeders

```bash
docker exec mini_erp_app php artisan migrate
docker exec mini_erp_app php artisan db:seed
```

### 4. Acesse a aplicação

- **Aplicação**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081
  - Usuário: `root`
  - Senha: `root123`

## 🗄️ Estrutura do Banco de Dados

O sistema utiliza 6 tabelas principais:

### Produtos
- `id`, `nome`, `preco`, `descricao`, `ativo`
- Suporte a variações com valores adicionais

### Produto Variações
- Variações dos produtos (tamanhos, cores, etc.)
- Valores adicionais ao preço base

### Estoque
- Controle por produto e variação
- Quantidade mínima configurável
- Redução/devolução automática

### Cupons
- Tipos: percentual ou valor fixo
- Validação por período e valor mínimo
- Controle de limite de uso

### Pedidos
- Status: pendente, confirmado, enviado, entregue, cancelado
- Integração com estoque e cupons
- Cálculo automático de frete

### Pedido Itens
- Itens individuais de cada pedido
- Histórico de preços e quantidades

## 🎯 Funcionalidades Principais

### Gestão de Produtos
- ✅ Cadastro, edição e exclusão de produtos
- ✅ Sistema de variações (tamanhos, cores, etc.)
- ✅ Controle de estoque por produto/variação
- ✅ Preços com valores adicionais por variação

### Carrinho de Compras
- ✅ Adicionar/remover produtos
- ✅ Atualização de quantidades em tempo real
- ✅ Aplicação de cupons de desconto
- ✅ Cálculo automático de frete:
  - Grátis para pedidos acima de R$ 200,00
  - R$ 15,00 para pedidos entre R$ 52,00 e R$ 166,59
  - R$ 20,00 para outros valores

### Sistema de Cupons
- ✅ Criação e gestão de cupons
- ✅ Validação automática (período, valor mínimo, limite de uso)
- ✅ Tipos: percentual ou valor fixo
- ✅ Aplicação no carrinho com feedback visual

### Verificação de CEP
- ✅ Integração com API ViaCEP
- ✅ Preenchimento automático do endereço
- ✅ Validação de CEP no checkout

### Webhook para Status de Pedidos
- ✅ Endpoint: `POST /webhook/status`
- ✅ Atualização automática de status
- ✅ Cancelamento com devolução de estoque e remoção do pedido
- ✅ Logs de auditoria

## 🔌 API Webhook

### Endpoint de Status
```
POST /webhook/status
Content-Type: application/json

{
    "pedido_id": 123,
    "status": "enviado"
}
```

### Status Aceitos
- `pendente`: Pedido criado
- `confirmado`: Pedido confirmado
- `enviado`: Pedido enviado
- `entregue`: Pedido entregue
- `cancelado`: Pedido cancelado (devolve estoque e remove pedido)

### Teste do Webhook
Acesse `/webhook/teste` para testar o webhook manualmente.

## 📊 Regras de Negócio

### Frete
- **Grátis**: Pedidos ≥ R$ 200,00
- **Promocional**: R$ 15,00 para pedidos entre R$ 52,00 e R$ 166,59
- **Normal**: R$ 20,00 para outros valores

### Estoque
- Verificação automática de disponibilidade
- Redução automática ao confirmar pedido
- Devolução automática ao cancelar pedido
- Controle por produto e variação

### Cupons
- Validação de período de validade
- Verificação de valor mínimo do carrinho
- Controle de limite de uso
- Aplicação única por pedido

## 🎨 Interface

- **Design Responsivo**: Bootstrap 5 com tema customizado
- **Navegação Intuitiva**: Menu principal com todas as funcionalidades
- **Feedback Visual**: Alertas, modais e animações
- **AJAX**: Atualizações em tempo real sem reload da página

## 🔒 Segurança

- Validação de dados no frontend e backend
- Proteção CSRF do Laravel
- Sanitização automática de dados
- Transações de banco para operações críticas

## 📱 Responsividade

O sistema é totalmente responsivo e funciona em:
- Desktop (1200px+)
- Tablet (768px - 1199px)
- Mobile (< 768px)

## 🧪 Dados de Teste

O sistema vem com dados de exemplo:

### Produtos
- Camiseta Básica (com variações P, M, G)
- Calça Jeans
- Tênis Esportivo

### Cupons
- `DESCONTO10`: 10% de desconto (mín. R$ 50,00)
- `FRETE15`: R$ 15,00 de desconto (mín. R$ 100,00)

## 🚀 Como Testar

1. **Produtos**: Acesse a página inicial e veja os produtos
2. **Carrinho**: Adicione produtos ao carrinho
3. **Cupons**: Use os cupons pré-cadastrados
4. **Checkout**: Finalize um pedido com CEP válido
5. **Webhook**: Teste em `/webhook/teste`

---

**Mini ERP** - Sistema completo de gestão empresarial
