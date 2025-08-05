<?php
/**
 * Sistema de Segurança SIGA - Proteção contra XSS e CSRF
 * 
 * Este arquivo contém funções essenciais de segurança para proteger
 * o sistema SIGA contra vulnerabilidades XSS e CSRF mantendo total
 * compatibilidade com o frontend existente.
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
 * Define headers básicos de segurança
 */
function set_security_headers() {
    // Prevent clickjacking
    header('X-Frame-Options: SAMEORIGIN');
    
    // XSS Protection
    header('X-XSS-Protection: 1; mode=block');
    
    // Content type sniffing protection
    header('X-Content-Type-Options: nosniff');
    
    // Referrer policy
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    // Content Security Policy básico (ajustar conforme necessário)
    header("Content-Security-Policy: default-src 'self' 'unsafe-inline' 'unsafe-eval'; img-src 'self' data:; object-src 'none';");
}

/**
 * Log de tentativas de ataques (para monitoramento)
 * 
 * @param string $type Tipo do ataque
 * @param string $data Dados do ataque
 * @param string $ip IP do atacante
 */
function log_security_incident($type, $data, $ip = null) {
    if (!$ip) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
    
    $log_entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'type' => $type,
        'ip' => $ip,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        'data' => substr($data, 0, 1000), // Limitar tamanho do log
        'url' => $_SERVER['REQUEST_URI'] ?? 'unknown'
    ];
    
    // Log em arquivo (criar diretório se não existir)
    $log_dir = dirname(__FILE__) . '/logs/security';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    $log_file = $log_dir . '/security_' . date('Y-m-d') . '.log';
    file_put_contents($log_file, json_encode($log_entry) . PHP_EOL, FILE_APPEND | LOCK_EX);
}

/**
 * Middleware para verificação automática de CSRF em POST
 * Chamar no início de páginas que processam formulários
 */
function csrf_middleware() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verify_csrf_token()) {
            log_security_incident('CSRF', 'Token inválido ou ausente', $_SERVER['REMOTE_ADDR'] ?? 'unknown');
            
            // Redirecionar com erro em vez de mostrar erro técnico
            $current_page = $_SERVER['PHP_SELF'] ?? 'index.php';
            header("Location: $current_page?erro=csrf");
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
 * Middleware para detecção de XSS
 * Verifica dados de entrada para possíveis ataques XSS
 */
function xss_middleware() {
    $inputs_to_check = array_merge($_GET, $_POST, $_COOKIE);
    
    foreach ($inputs_to_check as $key => $value) {
        if (is_string($value) && is_xss_attempt($value)) {
            log_security_incident('XSS_ATTEMPT', "$key: $value", $_SERVER['REMOTE_ADDR'] ?? 'unknown');
            
            // Opcional: bloquear requisição
            // header('HTTP/1.0 400 Bad Request');
            // exit('Requisição bloqueada por questões de segurança.');
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