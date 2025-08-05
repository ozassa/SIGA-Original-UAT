# Instruções de Configuração - Sistema de Segurança SIGA

## Pré-requisitos

Antes de utilizar as implementações de segurança, você deve:

### 1. Configurar Banco de Dados

**IMPORTANTE:** O arquivo `src/dbOpen.php` não existe. Você deve criá-lo baseado no exemplo:

```bash
cp src/dbOpen.php.example src/dbOpen.php
```

Depois edite `src/dbOpen.php` com suas configurações reais de banco:

```php
<?php
    header("Content-Type: text/html; charset=iso-8859-1",true);

    define('ENV', 'production'); // ou 'development'
    define('SALT', '$2a$07$SeuSaltUnicoAqui$'); // Manter para compatibilidade

    $conn = [
        'development' => [
            'DNS' => 'SUA_DNS_DEV',
            'user' => 'seu_usuario_dev',
            'password' => 'sua_senha_dev',
        ],
        'production' => [
            'DNS' => 'SUA_DNS_PROD',
            'user' => 'seu_usuario_prod',
            'password' => 'sua_senha_prod',
        ],
    ];

    $db = odbc_connect($conn[ENV]['DNS'], $conn[ENV]['user'], $conn[ENV]['password'], 1);
?>
```

### 2. Criar Diretório de Logs

```bash
mkdir -p logs/security
chmod 755 logs/security
```

### 3. Verificar Permissões

Certifique-se de que o diretório de logs tem permissões de escrita:

```bash
chown -R www-data:www-data logs/ # Linux/Apache
# ou
chown -R _www:_www logs/ # macOS
```

## Configuração do Sistema de Email

Para que a recuperação de senha funcione, você deve configurar o sistema de email.

### Opção 1: Usar Sistema Existente (MailSend.php)

Se já existe `src/role/MailSend.php`, a integração é automática.

### Opção 2: Configurar SMTP

Edite `secure_password_recovery.php` e configure as credenciais SMTP:

```php
function send_recovery_email($to, $subject, $body) {
    // Configure aqui suas credenciais SMTP
    $smtp_host = 'seu.servidor.smtp.com';
    $smtp_user = 'seu.email@dominio.com';
    $smtp_pass = 'sua.senha.smtp';
    
    // ... resto da implementação
}
```

## Testes de Funcionamento

### 1. Executar Testes Automatizados

**APENAS EM DESENVOLVIMENTO:**

```bash
php security_test.php
```

### 2. Testar Manualmente

1. **Login:** Teste com usuários existentes
2. **Brute Force:** Tente 6 logins incorretos e verifique bloqueio
3. **Recuperação:** Use `recover_password.php`
4. **Dashboard:** Acesse `security_dashboard.php` (apenas admin)

## Estrutura de Arquivos Criados

```
/
├── hybrid_auth.php                 # Sistema híbrido de autenticação
├── security_functions.php          # Funções de segurança XSS/CSRF
├── session_config.php              # Configuração segura de sessão
├── secure_password_recovery.php    # Sistema seguro de recuperação
├── reset_password.php              # Interface de redefinição
├── recover_password.php            # Interface de solicitação
├── security_dashboard.php          # Dashboard de monitoramento
├── security_test.php               # Script de teste (dev only)
├── SECURITY_IMPLEMENTATION.md      # Documentação técnica
├── SETUP_INSTRUCTIONS.md           # Este arquivo
└── logs/security/                  # Logs de segurança
    ├── login_attempts_YYYY-MM-DD.log
    ├── password_upgrades_YYYY-MM-DD.log
    ├── password_changes_YYYY-MM-DD.log
    ├── password_recoveries_YYYY-MM-DD.log
    ├── recovery_attempts_YYYY-MM-DD.log
    └── security_YYYY-MM-DD.log
```

## Arquivos Modificados

```
src/role/access/Access.php          # Integração híbrida
remember.php                        # Recuperação segura
session_config.php                  # Sessão avançada
index.php                          # Token CSRF (se aplicável)
```

## Verificação de Funcionamento

### 1. Autenticação Híbrida

- [ ] Usuários com senhas antigas podem fazer login
- [ ] Senhas são automaticamente atualizadas para BCRYPT
- [ ] Novas senhas usam hash seguro
- [ ] Log de upgrades é gerado

### 2. Proteção Brute Force

- [ ] IP bloqueado após 5 tentativas (5 min)
- [ ] Usuário bloqueado após 3 tentativas (15 min)
- [ ] Logs de tentativas são gerados
- [ ] Bloqueios são liberados após timeout

### 3. Segurança de Sessão

- [ ] Sessão expira após 30 min de inatividade
- [ ] ID regenerado a cada 5 minutos
- [ ] Cookies seguros configurados
- [ ] Detecção de hijacking funciona

### 4. Recuperação de Senha

- [ ] Email de recuperação é enviado
- [ ] Token expira em 15 minutos
- [ ] Rate limiting funciona
- [ ] Senha é alterada com hash seguro

## Monitoramento

### Dashboard de Segurança

Acesse `/security_dashboard.php` para visualizar:
- Estatísticas de login
- IPs bloqueados
- Upgrades de senha
- Logs recentes

### Logs de Segurança

Os logs são gerados automaticamente em `logs/security/`:
- Monitore tentativas de brute force
- Verifique upgrades de senha
- Analise padrões suspeitos

## Manutenção

### Rotação de Logs

Configure um cron job para limpar logs antigos:

```bash
# Adicione ao crontab
0 2 * * * find /caminho/para/siga/logs/security -name "*.log" -mtime +30 -delete
```

### Backup de Logs

```bash
# Script de backup semanal
tar -czf security_logs_$(date +%Y%m%d).tar.gz logs/security/
```

### Monitoramento de Performance

Monitore o uso de CPU durante picos de login. Se necessário:
- Ajuste o `cost` do BCRYPT em `hybrid_auth.php`
- Implemente cache para rate limiting
- Considere CDN para assets estáticos

## Solução de Problemas

### Problema: "Database connection failed"
**Solução:** Verifique `src/dbOpen.php` e configurações de banco

### Problema: "Permission denied on logs"
**Solução:** Configure permissões de escrita no diretório logs/security

### Problema: "Function password_hash not found"
**Solução:** Verifique se PHP >= 5.5.0 está instalado

### Problema: "Email not sending"
**Solução:** Configure SMTP em `secure_password_recovery.php`

### Problema: "Session timeout muito rápido"
**Solução:** Ajuste `SESSION_TIMEOUT` em `session_config.php`

## Suporte

1. **Logs:** Sempre verifique primeiro os logs em `logs/security/`
2. **Dashboard:** Use `/security_dashboard.php` para diagnóstico
3. **Testes:** Execute `security_test.php` em desenvolvimento
4. **Documentação:** Consulte `SECURITY_IMPLEMENTATION.md`

## Status da Implementação

✅ **Concluído:**
- Sistema híbrido de autenticação
- Proteção brute force
- Segurança de sessão avançada
- Recuperação segura de senha
- Proteção XSS/CSRF
- Logging de segurança
- Dashboard de monitoramento
- Documentação completa

🔧 **Pendente de Configuração:**
- Arquivo `src/dbOpen.php` (configurar banco)
- Sistema de email (SMTP)
- Permissões de diretório logs
- Testes em produção

---

**Versão:** 1.0  
**Data:** Janeiro 2025  
**Compatibilidade:** PHP 5.5+ | Sistema SIGA existente