# Configuração de E-mail - Sistema ERP

## Configurações Necessárias no .env

Para que o sistema de e-mail funcione corretamente, adicione as seguintes configurações no arquivo `.env`:

### Para Gmail (recomendado para desenvolvimento)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu-email@gmail.com
MAIL_PASSWORD=sua-senha-de-app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=seu-email@gmail.com
MAIL_FROM_NAME="Sistema ERP"
```

**Importante para Gmail:**
1. Ative a verificação em duas etapas na sua conta Google
2. Gere uma "Senha de app" específica para o Laravel
3. Use a senha de app no campo `MAIL_PASSWORD`, não sua senha normal

### Para Mailtrap (recomendado para testes)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=seu-username-mailtrap
MAIL_PASSWORD=sua-senha-mailtrap
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@sistema-erp.com
MAIL_FROM_NAME="Sistema ERP"
```

### Para SendGrid

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=sua-api-key-sendgrid
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@seudominio.com
MAIL_FROM_NAME="Sistema ERP"
```

### Para Mailgun

```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=seudominio.mailgun.org
MAILGUN_SECRET=sua-chave-secreta
MAIL_FROM_ADDRESS=noreply@seudominio.com
MAIL_FROM_NAME="Sistema ERP"
```

## Testando a Configuração

### 1. Comando de Teste

Execute o comando para testar o envio de e-mail:

```bash
# Testar com o pedido mais recente
php artisan email:testar

# Testar com um pedido específico
php artisan email:testar 123

# Testar enviando para um e-mail específico
php artisan email:testar --email=teste@exemplo.com

# Testar pedido específico para e-mail específico
php artisan email:testar 123 --email=teste@exemplo.com
```

### 2. Verificar Logs

Os logs de e-mail são salvos em `storage/logs/laravel.log`. Procure por:
- `E-mail de confirmação enviado com sucesso`
- `Falha ao enviar e-mail de confirmação`

### 3. Configurar Queue (Opcional)

Para melhor performance, configure o sistema de filas:

```env
QUEUE_CONNECTION=database
```

Execute as migrações de queue:
```bash
php artisan queue:table
php artisan migrate
```

Inicie o worker:
```bash
php artisan queue:work
```

## Estrutura do E-mail

O e-mail de confirmação inclui:

- **Cabeçalho**: Logo e título do sistema
- **Informações do Pedido**: Número, data, status
- **Dados do Cliente**: Nome, e-mail, telefone
- **Endereço de Entrega**: CEP e endereço completo
- **Itens do Pedido**: Tabela com produtos, quantidades e preços
- **Totais**: Subtotal, desconto, frete e total final
- **Rodapé**: Informações de contato

## Personalização

### Alterar Template

O template do e-mail está em: `resources/views/emails/pedido-confirmado.blade.php`

### Alterar Assunto

Modifique o método `envelope()` em: `app/Mail/PedidoConfirmado.php`

### Adicionar Anexos

Use o método `attachments()` em: `app/Mail/PedidoConfirmado.php`

## Troubleshooting

### Erro: "Connection could not be established"
- Verifique host, porta e credenciais
- Confirme se o firewall permite conexões SMTP
- Teste com telnet: `telnet smtp.gmail.com 587`

### Erro: "Authentication failed"
- Para Gmail: use senha de app, não senha normal
- Verifique username e password
- Confirme se a conta não tem 2FA sem senha de app

### E-mail não chega
- Verifique pasta de spam/lixo eletrônico
- Confirme se o e-mail de destino está correto
- Verifique logs do Laravel para erros

### Performance lenta
- Configure sistema de filas (queue)
- Use serviços de e-mail dedicados (SendGrid, Mailgun)
- Considere envio assíncrono

## Monitoramento

### Logs Importantes

```bash
# Ver logs em tempo real
tail -f storage/logs/laravel.log | grep -i mail

# Buscar erros de e-mail
grep -i "mail\|email" storage/logs/laravel.log
```

### Métricas

O sistema registra:
- Tentativas de envio
- Sucessos e falhas
- Tempo de processamento
- Erros detalhados
