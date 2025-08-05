<?php
/**
 * SIGA Production Security Functions
 * 
 * Essential security functions for XSS and CSRF protection
 * optimized for production environment.
 * 
 * @version 2.0 - Production Ready
 * @author Claude Code - Security Production Cleanup
 */

// Inicializar sessão se não existir (verificação mais robusta)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Gera token CSRF único para proteção de formulários
 * 
 * @return string Token CSRF único
 */
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_tokens'])) {
        $_SESSION['csrf_tokens'] = array();
    }
    
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_tokens'][$token] = time();
    
    // Limpar tokens antigos (mais de 1 hora)
    foreach ($_SESSION['csrf_tokens'] as $old_token => $timestamp) {
        if (time() - $timestamp > 3600) {
            unset($_SESSION['csrf_tokens'][$old_token]);
        }
    }
    
    return $token;
}

/**
 * Valida token CSRF enviado via POST
 * 
 * @param string $token Token a ser validado
 * @return bool True se válido, false caso contrário
 */
function validate_csrf_token($token) {
    if (!isset($_SESSION['csrf_tokens']) || empty($token)) {
        return false;
    }
    
    if (isset($_SESSION['csrf_tokens'][$token])) {
        // Token válido por 1 hora
        if (time() - $_SESSION['csrf_tokens'][$token] <= 3600) {
            // Remove token após uso (one-time use)
            unset($_SESSION['csrf_tokens'][$token]);
            return true;
        } else {
            // Remove token expirado
            unset($_SESSION['csrf_tokens'][$token]);
        }
    }
    
    return false;
}

/**
 * Gera campo hidden com token CSRF para formulários
 * 
 * @return string HTML do campo hidden
 */
function csrf_token_field() {
    $token = generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Verifica se a requisição atual possui token CSRF válido
 * Para uso em processamento de formulários POST
 * 
 * @return bool True se válido ou GET, false se POST inválido
 */
function verify_csrf_token() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return true; // GET requests não precisam de token
    }
    
    $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    return validate_csrf_token($token);
}

/**
 * Sanitização contextual para output seguro
 * 
 * @param mixed $data Dados a serem sanitizados
 * @param string $context Contexto: html, attr, js, css, url
 * @return string Dados sanitizados
 */
function safe_output($data, $context = 'html') {
    if (is_null($data)) {
        return '';
    }
    
    $data = (string) $data;
    
    switch ($context) {
        case 'html':
            return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            
        case 'attr':
            // Para atributos HTML
            return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            
        case 'js':
            // Para contexto JavaScript
            return json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
            
        case 'css':
            // Para contexto CSS - remove caracteres perigosos
            return preg_replace('/[^a-zA-Z0-9\-_#\.]/', '', $data);
            
        case 'url':
            // Para URLs
            return urlencode($data);
            
        case 'sql':
            // Para SQL - escape básico (usar prepared statements quando possível)
            return str_replace("'", "''", $data);
            
        default:
            return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}

/**
 * Valida e sanitiza dados de entrada
 * 
 * @param mixed $data Dados de entrada
 * @param string $type Tipo esperado: string, int, float, email, url
 * @param int $max_length Comprimento máximo
 * @return mixed Dados validados ou null se inválidos
 */
function validate_input($data, $type = 'string', $max_length = 255) {
    if (is_null($data)) {
        return null;
    }
    
    $data = trim((string) $data);
    
    // Verificar comprimento
    if (strlen($data) > $max_length) {
        return null;
    }
    
    switch ($type) {
        case 'int':
            return filter_var($data, FILTER_VALIDATE_INT);
            
        case 'float':
            return filter_var($data, FILTER_VALIDATE_FLOAT);
            
        case 'email':
            return filter_var($data, FILTER_VALIDATE_EMAIL);
            
        case 'url':
            return filter_var($data, FILTER_VALIDATE_URL);
            
        case 'string':
        default:
            // Remove caracteres de controle perigosos
            $data = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $data);
            return $data;
    }
}

/**
 * Obtém valor de REQUEST de forma segura
 * 
 * @param string $key Chave do array $_REQUEST
 * @param string $type Tipo esperado
 * @param mixed $default Valor padrão
 * @param int $max_length Comprimento máximo
 * @return mixed Valor sanitizado ou padrão
 */
function safe_request($key, $type = 'string', $default = '', $max_length = 255) {
    if (!isset($_REQUEST[$key])) {
        return $default;
    }
    
    $value = validate_input($_REQUEST[$key], $type, $max_length);
    return ($value !== null) ? $value : $default;
}

/**
 * Production optimized security headers
 */
function set_security_headers() {
    if (headers_sent()) {
        return false;
    }
    
    // Prevent clickjacking
    header('X-Frame-Options: SAMEORIGIN');
    
    // XSS Protection
    header('X-XSS-Protection: 1; mode=block');
    
    // Content type sniffing protection
    header('X-Content-Type-Options: nosniff');
    
    // Referrer policy
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    // Basic CSP for legacy compatibility - use advanced_security_system.php for full CSP
    header("Content-Security-Policy: default-src 'self' 'unsafe-inline' 'unsafe-eval'; img-src 'self' data: https:; object-src 'none'; frame-ancestors 'self';");
    
    return true;
}

/**
 * Production optimized security incident logging
 * 
 * @param string $type Attack type
 * @param string $data Attack data
 * @param string $ip Attacker IP
 */
function log_security_incident($type, $data, $ip = null) {
    if (!$ip) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
    
    $log_entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'type' => $type,
        'ip' => $ip,
        'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 255),
        'data' => substr($data, 0, 500), // Limit log size for performance
        'url' => substr($_SERVER['REQUEST_URI'] ?? 'unknown', 0, 255)
    ];
    
    // Production log directory
    $log_dir = defined('SECURITY_LOG_DIR') ? SECURITY_LOG_DIR : dirname(__FILE__) . '/logs/security';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    $log_file = $log_dir . '/security_incidents_' . date('Y-m-d') . '.log';
    
    // Use error_log for better production logging
    error_log(json_encode($log_entry), 3, $log_file);
}

/**
 * Production CSRF middleware for form processing
 */
function csrf_middleware() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verify_csrf_token()) {
            log_security_incident('CSRF_VIOLATION', 'Invalid or missing token', $_SERVER['REMOTE_ADDR'] ?? 'unknown');
            
            // Production-safe redirect
            http_response_code(403);
            $current_page = $_SERVER['PHP_SELF'] ?? '/index.php';
            header("Location: $current_page?erro=security");
            exit();
        }
    }
}

/**
 * Verifica se string contém possível payload XSS
 * 
 * @param string $data Dados a verificar
 * @return bool True se suspeito de XSS
 */
function is_xss_attempt($data) {
    $xss_patterns = [
        '/<script/i',
        '/javascript:/i',
        '/on\w+\s*=/i',
        '/<iframe/i',
        '/<object/i',
        '/<embed/i',
        '/vbscript:/i',
        '/expression\s*\(/i'
    ];
    
    foreach ($xss_patterns as $pattern) {
        if (preg_match($pattern, $data)) {
            return true;
        }
    }
    
    return false;
}

/**
 * Production XSS detection middleware
 */
function xss_middleware() {
    $inputs_to_check = array_merge($_GET, $_POST);
    
    foreach ($inputs_to_check as $key => $value) {
        if (is_string($value) && is_xss_attempt($value)) {
            log_security_incident('XSS_ATTEMPT', "$key: " . substr($value, 0, 100), $_SERVER['REMOTE_ADDR'] ?? 'unknown');
            
            // Block suspicious requests in production
            http_response_code(400);
            exit('Request blocked for security reasons.');
        }
    }
}

/**
 * Inicialização automática de segurança
 * Chamada automática quando o arquivo é incluído
 */
function init_security() {
    // Definir headers de segurança
    set_security_headers();
    
    // Executar middleware de detecção XSS
    xss_middleware();
    
    // Middleware CSRF será chamado manualmente onde necessário
}

// Executar inicialização automática
init_security();

?>