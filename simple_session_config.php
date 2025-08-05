<?php
/**
 * Configuração SIMPLES de Sessão - Para resolver problemas imediatos
 */

// Verificar se está rodando via web server (não CLI)
if (php_sapi_name() === 'cli') {
    // Em CLI, não fazer nada com sessões
    return;
}

// Verificações básicas de $_SERVER
$server_port = $_SERVER['SERVER_PORT'] ?? 80;
$https_check = $_SERVER['HTTPS'] ?? 'off';
$is_https = (!empty($https_check) && $https_check !== 'off') || $server_port == 443;

// Configurações básicas de sessão
if (session_status() === PHP_SESSION_NONE) {
    // Configurações simples
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', $is_https ? 1 : 0);
    ini_set('session.use_only_cookies', 1);
    
    session_start();
}
?>