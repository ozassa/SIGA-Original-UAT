<?php
// Arquivo de configuração de logging de segurança
// Criado para rastrear tentativas de SQL Injection e atividades suspeitas

function logSecurityEvent($event_type, $message, $user_id = null, $ip = null) {
    $timestamp = date('Y-m-d H:i:s');
    $user_id = $user_id ?: (isset($_SESSION['userID']) ? $_SESSION['userID'] : 'unknown');
    $ip = $ip ?: $_SERVER['REMOTE_ADDR'];
    
    $log_entry = "[{$timestamp}] [{$event_type}] IP: {$ip} | User: {$user_id} | {$message}" . PHP_EOL;
    
    // Log para arquivo
    $log_file = __DIR__ . '/logs/security_' . date('Y-m-d') . '.log';
    $log_dir = dirname($log_file);
    
    if (!file_exists($log_dir)) {
        mkdir($log_dir, 0750, true);
    }
    
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
    
    // Log crítico também para error_log do sistema
    if (in_array($event_type, ['BLOCKED_INJECTION', 'INVALID_LOGIN', 'DANGEROUS_QUERY'])) {
        error_log("SECURITY ALERT: " . $log_entry);
    }
}

function validateUserSession() {
    if (!isset($_SESSION['userID']) || !is_numeric($_SESSION['userID'])) {
        logSecurityEvent('INVALID_SESSION', 'Tentativa de acesso com sessão inválida');
        return false;
    }
    return true;
}

function sanitizeInput($input, $type = 'string') {
    if (is_null($input)) {
        return null;
    }
    
    switch ($type) {
        case 'int':
            return filter_var($input, FILTER_VALIDATE_INT);
        case 'email':
            return filter_var($input, FILTER_VALIDATE_EMAIL);
        case 'string':
        default:
            // Remove caracteres perigosos mas mantém funcionalidade
            return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}
?>