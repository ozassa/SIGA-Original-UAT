<?php
/**
 * CSP Violation Handler
 * Processa e registra violações de Content Security Policy
 */

// Apenas aceita POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

// Verifica se há dados de violação
$input = file_get_contents('php://input');
if (empty($input)) {
    http_response_code(400);
    exit;
}

try {
    // Decodifica o JSON da violação
    $violationReport = json_decode($input, true);
    
    if (!$violationReport || !isset($violationReport['csp-report'])) {
        http_response_code(400);
        exit;
    }
    
    $violation = $violationReport['csp-report'];
    
    // Cria entrada de log estruturada
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        'referer' => $_SERVER['HTTP_REFERER'] ?? 'unknown',
        'blocked_uri' => $violation['blocked-uri'] ?? 'unknown',
        'document_uri' => $violation['document-uri'] ?? 'unknown',
        'violated_directive' => $violation['violated-directive'] ?? 'unknown',
        'original_policy' => $violation['original-policy'] ?? 'unknown',
        'source_file' => $violation['source-file'] ?? 'unknown',
        'line_number' => $violation['line-number'] ?? 'unknown',
        'column_number' => $violation['column-number'] ?? 'unknown',
        'script_sample' => $violation['script-sample'] ?? 'unknown'
    ];
    
    // Arquivo de log
    $logFile = 'logs/csp_violations.log';
    $logDir = dirname($logFile);
    
    // Cria diretório se não existir
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    // Escreve no log
    $logLine = json_encode($logEntry) . "\n";
    file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
    
    // Log adicional para violações críticas
    if (isCriticalViolation($violation)) {
        $criticalLogFile = 'logs/csp_critical_violations.log';
        file_put_contents($criticalLogFile, $logLine, FILE_APPEND | LOCK_EX);
        
        // Opcional: Enviar email para administradores em violações críticas
        // notifyAdministrators($logEntry);
    }
    
    // Resposta de sucesso
    http_response_code(204);
    
} catch (Exception $e) {
    // Log de erro interno
    error_log("CSP Violation Handler Error: " . $e->getMessage());
    http_response_code(500);
}

/**
 * Determina se uma violação é crítica
 */
function isCriticalViolation($violation) {
    $criticalPatterns = [
        'script-src',     // Violações de script são sempre críticas
        'object-src',     // Tentativas de executar objetos
        'base-uri',       // Manipulação de base URI
        'form-action'     // Tentativas de envio para URIs não autorizados
    ];
    
    $violatedDirective = $violation['violated-directive'] ?? '';
    
    foreach ($criticalPatterns as $pattern) {
        if (strpos($violatedDirective, $pattern) !== false) {
            return true;
        }
    }
    
    return false;
}

/**
 * Notifica administradores sobre violações críticas (opcional)
 */
function notifyAdministrators($logEntry) {
    // Implementar notificação por email se necessário
    // mail('admin@domain.com', 'CSP Critical Violation', json_encode($logEntry, JSON_PRETTY_PRINT));
}

?>