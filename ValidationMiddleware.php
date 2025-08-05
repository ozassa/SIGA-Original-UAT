<?php
/**
 * SIGA Validation Middleware
 * 
 * Middleware transparente que intercepta automaticamente $_GET, $_POST, $_REQUEST
 * e aplica validação baseada em contexto/módulo mantendo total backward compatibility.
 * 
 * @version 1.0
 * @author Claude Code - Security Hardening Mission
 */

require_once('InputValidationFramework.php');

class ValidationMiddleware {
    
    private static $initialized = false;
    private static $original_request = [];
    private static $original_get = [];
    private static $original_post = [];
    private static $current_module = null;
    private static $bypass_validation = false;
    
    /**
     * Inicializa o middleware de validação
     */
    public static function init($module = null) {
        if (self::$initialized) {
            return;
        }
        
        // Salvar dados originais
        self::$original_request = $_REQUEST;
        self::$original_get = $_GET;
        self::$original_post = $_POST;
        
        // Detectar módulo atual se não fornecido
        if (!$module) {
            $module = self::detectCurrentModule();
        }
        self::$current_module = $module;
        
        // Aplicar validação automática
        self::applyValidation();
        
        self::$initialized = true;
    }
    
    /**
     * Detecta o módulo atual baseado na URL/PATH
     */
    private static function detectCurrentModule() {
        $path = $_SERVER['REQUEST_URI'] ?? $_SERVER['PHP_SELF'] ?? '';
        
        if (strpos($path, '/credit/') !== false) {
            return 'credit';
        } elseif (strpos($path, '/client/') !== false) {
            return 'client';
        } elseif (strpos($path, '/dve/') !== false) {
            return 'dve';
        } elseif (strpos($path, '/executive/') !== false) {
            return 'executive';
        } elseif (strpos($path, '/inform/') !== false) {
            return 'inform';
        } elseif (strpos($path, '/policy/') !== false) {
            return 'policy';
        } elseif (strpos($path, '/user/') !== false) {
            return 'user';
        }
        
        return 'general';
    }
    
    /**
     * Aplica validação transparente aos superglobals
     */
    private static function applyValidation() {
        // Verificar rate limiting primeiro
        if (!InputValidator::checkRateLimit()) {
            self::blockRequest('Rate limit exceeded');
            return;
        }
        
        // Obter regras do módulo atual
        $rules = ValidationRules::getRulesForModule(self::$current_module);
        
        // Validar e sanitizar $_GET
        $_GET = self::validateArray($_GET, $rules, 'GET');
        
        // Validar e sanitizar $_POST  
        $_POST = self::validateArray($_POST, $rules, 'POST');
        
        // Reconstruir $_REQUEST com dados validados
        $_REQUEST = array_merge($_GET, $_POST);
        
        // Logging de tentativas suspeitas
        self::logSuspiciousActivity();
    }
    
    /**
     * Valida array de dados
     */
    private static function validateArray($data, $rules, $source = 'UNKNOWN') {
        $validated = [];
        
        foreach ($data as $key => $value) {
            // Verificar se existe regra específica para esta chave
            if (isset($rules[$key])) {
                $validated_value = InputValidator::validate($value, $rules[$key]);
                
                // Se validação falhou, usar valor padrão ou original (para backward compatibility)
                if ($validated_value === false || $validated_value === null) {
                    if (isset($rules[$key]['default'])) {
                        $validated[$key] = $rules[$key]['default'];
                    } else {
                        // Para backward compatibility, manter valor original sanitizado
                        $validated[$key] = InputValidator::sanitize($value, 'html');
                        self::logValidationFailure($key, $value, $source);
                    }
                } else {
                    $validated[$key] = $validated_value;
                }
            } else {
                // Sem regra específica - aplicar sanitização básica
                if (is_array($value)) {
                    $validated[$key] = self::validateArray($value, $rules, $source);
                } else {
                    $validated[$key] = InputValidator::sanitize($value, 'html');
                    
                    // Detectar possíveis ataques em campos não mapeados
                    if (self::isPotentialAttack($value)) {
                        self::logPotentialAttack($key, $value, $source);
                    }
                }
            }
        }
        
        return $validated;
    }
    
    /**
     * Detecta potenciais ataques em inputs
     */
    private static function isPotentialAttack($value) {
        $attack_patterns = [
            // XSS patterns
            '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi',
            '/javascript\s*:/i',
            '/on\w+\s*=/i',
            '/<iframe/i',
            '/<object/i',
            '/<embed/i',
            '/vbscript\s*:/i',
            '/expression\s*\(/i',
            
            // SQL Injection patterns
            '/(\bunion\b.*\bselect\b)|(\bselect\b.*\bunion\b)/i',
            '/\b(select|insert|update|delete|drop|create|alter)\b.*\b(from|into|table|database)\b/i',
            '/(\bor\b|\band\b)\s+\d+\s*=\s*\d+/i',
            '/\'\s*(or|and)\s*\'/i',
            
            // Command injection patterns
            '/[;&|`$(){}[\]]/i',
            '/\b(wget|curl|nc|netcat|php|python|perl|ruby|bash|sh|cmd|powershell)\b/i',
            
            // Path traversal patterns
            '/\.\.[\/\\]/i',
            '/\/(etc|usr|var|tmp|home|root)\//i',
            
            // File inclusion patterns
            '/\b(include|require)(_once)?\s*\(/i',
            '/file_get_contents\s*\(/i',
            '/fopen\s*\(/i'
        ];
        
        foreach ($attack_patterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Log de falhas de validação
     */
    private static function logValidationFailure($key, $value, $source) {
        $log_entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => 'VALIDATION_FAILURE',
            'module' => self::$current_module,
            'source' => $source,
            'key' => $key,
            'value' => substr($value, 0, 200), // Limitar tamanho
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'url' => $_SERVER['REQUEST_URI'] ?? 'unknown',
            'user_id' => $_SESSION['userID'] ?? 'anonymous'
        ];
        
        self::writeLog($log_entry, 'validation_failures');
    }
    
    /**
     * Log de potenciais ataques
     */
    private static function logPotentialAttack($key, $value, $source) {
        $log_entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => 'POTENTIAL_ATTACK',
            'module' => self::$current_module,
            'source' => $source,
            'key' => $key,
            'value' => substr($value, 0, 500), // Mais detalhes para análise
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'url' => $_SERVER['REQUEST_URI'] ?? 'unknown',
            'user_id' => $_SESSION['userID'] ?? 'anonymous',
            'referer' => $_SERVER['HTTP_REFERER'] ?? 'unknown'
        ];
        
        self::writeLog($log_entry, 'potential_attacks');
        
        // Alertas em tempo real para ataques críticos
        if (self::isCriticalAttack($value)) {
            self::sendSecurityAlert($log_entry);
        }
    }
    
    /**
     * Detecta ataques críticos que precisam de alerta imediato
     */
    private static function isCriticalAttack($value) {
        $critical_patterns = [
            '/\beval\s*\(/i',
            '/\bexec\s*\(/i',
            '/\bsystem\s*\(/i',
            '/\bpassthru\s*\(/i',
            '/\bshell_exec\s*\(/i',
            '/<script.*>.*<\/script>/i',
            '/\bunion\b.*\bselect\b/i'
        ];
        
        foreach ($critical_patterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Log de atividade suspeita geral
     */
    private static function logSuspiciousActivity() {
        $suspicious_indicators = [];
        
        // Verificar múltiplos parâmetros com valores suspeitos
        $suspicious_count = 0;
        foreach ($_REQUEST as $key => $value) {
            if (self::isPotentialAttack($value)) {
                $suspicious_count++;
            }
        }
        
        if ($suspicious_count > 2) {
            $suspicious_indicators[] = "Multiple suspicious parameters: $suspicious_count";
        }
        
        // Verificar User Agent suspeito
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if (preg_match('/(sqlmap|nikto|nmap|masscan|burp|owasp|zap)/i', $user_agent)) {
            $suspicious_indicators[] = "Suspicious user agent: $user_agent";
        }
        
        // Verificar requests muito frequentes do mesmo IP
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        if (self::isHighFrequencyIP($ip)) {
            $suspicious_indicators[] = "High frequency requests from IP: $ip";
        }
        
        if (!empty($suspicious_indicators)) {
            $log_entry = [
                'timestamp' => date('Y-m-d H:i:s'),
                'type' => 'SUSPICIOUS_ACTIVITY',
                'module' => self::$current_module,
                'indicators' => $suspicious_indicators,
                'ip' => $ip,
                'user_agent' => $user_agent,
                'url' => $_SERVER['REQUEST_URI'] ?? 'unknown',
                'user_id' => $_SESSION['userID'] ?? 'anonymous'
            ];
            
            self::writeLog($log_entry, 'suspicious_activity');
        }
    }
    
    /**
     * Verifica se IP está fazendo requests em alta frequência
     */
    private static function isHighFrequencyIP($ip) {
        static $ip_tracker = [];
        $current_time = time();
        $window = 60; // 1 minuto
        $threshold = 30; // 30 requests por minuto
        
        if (!isset($ip_tracker[$ip])) {
            $ip_tracker[$ip] = [];
        }
        
        // Adicionar timestamp atual
        $ip_tracker[$ip][] = $current_time;
        
        // Remover timestamps antigos
        $ip_tracker[$ip] = array_filter($ip_tracker[$ip], function($timestamp) use ($current_time, $window) {
            return ($current_time - $timestamp) <= $window;
        });
        
        return count($ip_tracker[$ip]) > $threshold;
    }
    
    /**
     * Escreve log em arquivo
     */
    private static function writeLog($log_entry, $log_type) {
        $log_dir = dirname(__FILE__) . '/logs/middleware';
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        $log_file = $log_dir . '/' . $log_type . '_' . date('Y-m-d') . '.log';
        file_put_contents($log_file, json_encode($log_entry) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Envia alerta de segurança para administradores
     */
    private static function sendSecurityAlert($log_entry) {
        // Implementar notificação (email, webhook, etc.)
        // Por enquanto, apenas log especial
        self::writeLog($log_entry, 'critical_alerts');
        
        // Opcional: Implementar bloqueio temporário do IP
        // self::blockIP($log_entry['ip']);
    }
    
    /**
     * Bloqueia requisição suspeita
     */
    private static function blockRequest($reason) {
        $log_entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => 'REQUEST_BLOCKED',
            'reason' => $reason,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'url' => $_SERVER['REQUEST_URI'] ?? 'unknown'
        ];
        
        self::writeLog($log_entry, 'blocked_requests');
        
        // Headers de segurança
        http_response_code(429);
        header('Content-Type: text/plain');
        
        // Resposta genérica para não dar informações ao atacante
        die('Service temporarily unavailable. Please try again later.');
    }
    
    /**
     * Permite bypass da validação para casos específicos
     */
    public static function bypassValidation($bypass = true) {
        self::$bypass_validation = $bypass;
    }
    
    /**
     * Obtém dados originais se necessário
     */
    public static function getOriginalData($type = 'REQUEST') {
        switch (strtoupper($type)) {
            case 'GET':
                return self::$original_get;
            case 'POST':
                return self::$original_post;
            case 'REQUEST':
            default:
                return self::$original_request;
        }
    }
    
    /**
     * Reinicia o middleware (para testes)
     */
    public static function reset() {
        self::$initialized = false;
        self::$original_request = [];
        self::$original_get = [];
        self::$original_post = [];
        self::$current_module = null;
        self::$bypass_validation = false;
    }
    
    /**
     * Obtém estatísticas do middleware
     */
    public static function getStats() {
        return [
            'initialized' => self::$initialized,
            'current_module' => self::$current_module,
            'bypass_validation' => self::$bypass_validation,
            'original_data_count' => [
                'GET' => count(self::$original_get),
                'POST' => count(self::$original_post),
                'REQUEST' => count(self::$original_request)
            ]
        ];
    }
}

/**
 * Função helper para inicialização automática
 */
function init_validation_middleware($module = null) {
    ValidationMiddleware::init($module);
}

/**
 * Função helper para obter dados validados
 */
function safe_input($key, $source = 'REQUEST', $rules = []) {
    switch (strtoupper($source)) {
        case 'GET':
            return InputValidator::getGet($key, $rules);
        case 'POST':
            return InputValidator::getPost($key, $rules);
        case 'REQUEST':
        default:
            return InputValidator::getRequest($key, $rules);
    }
}

// Auto-inicialização se não estiver em modo de teste
if (!defined('VALIDATION_MIDDLEWARE_TEST_MODE')) {
    // Aguardar sessão estar disponível
    if (session_status() === PHP_SESSION_ACTIVE || isset($_SESSION)) {
        ValidationMiddleware::init();
    }
}