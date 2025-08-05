<?php
/**
 * Configuração Segura de Sessão SIGA
 * 
 * Implementa configurações de segurança avançadas para sessões:
 * - Timeout de inatividade (30 minutos)
 * - Regeneração automática de ID
 * - Cookies seguros
 * - Detecção de hijacking
 */

// Configurações de segurança de sessão
define('SESSION_TIMEOUT', 1800); // 30 minutos
define('SESSION_REGENERATE_INTERVAL', 300); // 5 minutos

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;

// Configurações de cookie seguro
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', $isHttps ? 1 : 0);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.cookie_lifetime', 0); // Expirar quando browser fechar
ini_set('session.gc_maxlifetime', SESSION_TIMEOUT);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica timeout da sessão
 */
function check_session_timeout() {
    if (isset($_SESSION['last_activity'])) {
        if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
            // Sessão expirou
            session_destroy();
            
            // Redirecionar para login com mensagem
            $redirect_url = '/index.php?erro=2'; // Erro de sessão expirada
            header("Location: $redirect_url");
            exit();
        }
    }
    
    // Atualizar timestamp de atividade
    $_SESSION['last_activity'] = time();
}

/**
 * Força regeneração de ID da sessão periodicamente
 */
function check_session_regeneration() {
    if (!isset($_SESSION['session_regenerated'])) {
        $_SESSION['session_regenerated'] = time();
        session_regenerate_id(true);
    } else {
        if (time() - $_SESSION['session_regenerated'] > SESSION_REGENERATE_INTERVAL) {
            $_SESSION['session_regenerated'] = time();
            session_regenerate_id(true);
        }
    }
}

/**
 * Detecta possível hijacking de sessão
 */
function detect_session_hijacking() {
    $current_fingerprint = create_session_fingerprint();
    
    if (isset($_SESSION['session_fingerprint'])) {
        if ($_SESSION['session_fingerprint'] !== $current_fingerprint) {
            // Possível hijacking detectado
            session_destroy();
            
            // Log do incidente
            error_log("Possível session hijacking detectado - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
            
            $redirect_url = '/index.php?erro=2';
            header("Location: $redirect_url");
            exit();
        }
    } else {
        $_SESSION['session_fingerprint'] = $current_fingerprint;
    }
}

/**
 * Cria fingerprint da sessão baseado em características do usuário
 */
function create_session_fingerprint() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $accept_language = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
    $accept_encoding = $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '';
    
    return hash('sha256', $user_agent . $accept_language . $accept_encoding);
}

/**
 * Inicializa sessão segura
 */
function secure_session_start() {
    // Verificar timeout
    check_session_timeout();
    
    // Verificar regeneração
    check_session_regeneration();
    
    // Detectar hijacking
    detect_session_hijacking();
}

/**
 * Destrói sessão de forma segura
 */
function secure_session_destroy() {
    $_SESSION = array();
    
    // Destruir cookie de sessão
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    session_destroy();
}

/**
 * Valida se usuário está logado e sessão é válida
 */
function validate_user_session() {
    if (!isset($_SESSION['userID']) || empty($_SESSION['userID'])) {
        return false;
    }
    
    return true;
}

// Executar verificações automáticas se usuário logado
if (isset($_SESSION['userID']) && !empty($_SESSION['userID'])) {
    secure_session_start();
}

?>
