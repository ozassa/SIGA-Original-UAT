<?php
/**
 * SIGA Input Validation Framework
 * 
 * Framework abrangente de validação de entrada para fortalecer a segurança do SIGA
 * em todos os pontos de entrada de dados mantendo total backward compatibility.
 * 
 * @version 1.0
 * @author Claude Code - Security Hardening Mission
 */

class InputValidator {
    
    // Rate limiting para inputs suspeitos
    private static $rate_limit = [];
    private static $max_attempts = 10;
    private static $time_window = 300; // 5 minutos
    
    // Cache de regras de validação
    private static $validation_rules = [];
    
    /**
     * Valida entrada baseada em regras específicas
     * 
     * @param mixed $input Dados de entrada
     * @param array $rules Array de regras ['type' => 'string', 'max_length' => 255, etc]
     * @return mixed Dados validados ou valor padrão
     */
    public static function validate($input, $rules = []) {
        // Configurações padrão
        $defaults = [
            'type' => 'string',
            'max_length' => 255,
            'min_length' => 0,
            'required' => false,
            'default' => null,
            'sanitize' => true,
            'whitelist' => null,
            'blacklist' => null
        ];
        
        $rules = array_merge($defaults, $rules);
        
        // Se entrada está vazia
        if (empty($input) || is_null($input)) {
            if ($rules['required']) {
                self::logSecurityIncident('VALIDATION_FAILED', 'Required field missing');
                return false;
            }
            return $rules['default'];
        }
        
        // Converter para string para validação
        $input = (string) $input;
        
        // Verificar comprimento
        if (strlen($input) > $rules['max_length']) {
            self::logSecurityIncident('VALIDATION_FAILED', "Input too long: " . substr($input, 0, 100));
            return $rules['default'];
        }
        
        if (strlen($input) < $rules['min_length']) {
            self::logSecurityIncident('VALIDATION_FAILED', "Input too short");
            return $rules['default'];
        }
        
        // Verificar whitelist
        if ($rules['whitelist'] && !in_array($input, $rules['whitelist'])) {
            self::logSecurityIncident('VALIDATION_FAILED', "Input not in whitelist: " . substr($input, 0, 100));
            return $rules['default'];
        }
        
        // Verificar blacklist
        if ($rules['blacklist'] && in_array($input, $rules['blacklist'])) {
            self::logSecurityIncident('VALIDATION_FAILED', "Input in blacklist: " . substr($input, 0, 100));
            return $rules['default'];
        }
        
        // Validação por tipo
        $validated = self::validateByType($input, $rules['type']);
        
        if ($validated === false) {
            self::logSecurityIncident('VALIDATION_FAILED', "Type validation failed for: " . substr($input, 0, 100));
            return $rules['default'];
        }
        
        // Sanitização se habilitada
        if ($rules['sanitize']) {
            $validated = self::sanitize($validated, $rules['type']);
        }
        
        return $validated;
    }
    
    /**
     * Validação específica por tipo
     */
    private static function validateByType($input, $type) {
        switch ($type) {
            case 'int':
            case 'integer':
                return filter_var($input, FILTER_VALIDATE_INT);
                
            case 'float':
            case 'decimal':
                return filter_var($input, FILTER_VALIDATE_FLOAT);
                
            case 'email':
                $email = filter_var($input, FILTER_VALIDATE_EMAIL);
                // Verificação adicional contra caracteres perigosos
                if ($email && !preg_match('/[<>"\']/', $email)) {
                    return $email;
                }
                return false;
                
            case 'url':
                return filter_var($input, FILTER_VALIDATE_URL);
                
            case 'ip':
                return filter_var($input, FILTER_VALIDATE_IP);
                
            case 'cnpj':
                return self::validateCNPJ($input);
                
            case 'cpf':
                return self::validateCPF($input);
                
            case 'date':
                return self::validateDate($input);
                
            case 'datetime':
                return self::validateDateTime($input);
                
            case 'currency':
                return self::validateCurrency($input);
                
            case 'filename':
                return self::validateFilename($input);
                
            case 'path':
                return self::validatePath($input);
                
            case 'alphanumeric':
                return preg_match('/^[a-zA-Z0-9]+$/', $input) ? $input : false;
                
            case 'alpha':
                return preg_match('/^[a-zA-Z]+$/', $input) ? $input : false;
                
            case 'numeric':
                return is_numeric($input) ? $input : false;
                
            case 'boolean':
                return filter_var($input, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                
            case 'string':
            default:
                // Remove caracteres de controle perigosos
                return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $input);
        }
    }
    
    /**
     * Sanitização contextual avançada
     */
    public static function sanitize($input, $context = 'html') {
        if (is_null($input)) {
            return '';
        }
        
        $input = (string) $input;
        
        switch ($context) {
            case 'html':
                return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                
            case 'html_safe':
                // HTML com whitelist de tags seguras
                $allowed_tags = '<p><br><strong><em><u><span>';
                return strip_tags($input, $allowed_tags);
                
            case 'sql':
                // Escape básico SQL (usar prepared statements quando possível)
                return str_replace(["'", '"', "\\", "\x00", "\n", "\r", "\x1a"], ["''", '""', "\\\\", "\\0", "\\n", "\\r", "\\Z"], $input);
                
            case 'url':
                return urlencode($input);
                
            case 'filename':
                // Remove caracteres perigosos para nomes de arquivo
                return preg_replace('/[^a-zA-Z0-9._-]/', '_', $input);
                
            case 'path':
                // Previne path traversal
                $input = str_replace(['../', '.\\', '../', '..\\'], '', $input);
                return preg_replace('/[^a-zA-Z0-9\/\\_.-]/', '', $input);
                
            case 'command':
                // Previne command injection
                return escapeshellarg($input);
                
            case 'ldap':
                // Previne LDAP injection
                $metaChars = ['\\', '*', '(', ')', "\x00"];
                $quotedMetaChars = [];
                foreach ($metaChars as $metaChar) {
                    $quotedMetaChars[] = '\\' . dechex(ord($metaChar));
                }
                return str_replace($metaChars, $quotedMetaChars, $input);
                
            case 'json':
                return json_encode($input, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                
            case 'regex':
                return preg_quote($input, '/');
                
            default:
                return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
    }
    
    /**
     * Verifica se dados são válidos baseado no tipo
     */
    public static function isValid($input, $type, $rules = []) {
        $validation_result = self::validate($input, array_merge(['type' => $type], $rules));
        return $validation_result !== false && $validation_result !== null;
    }
    
    /**
     * Validação de CNPJ
     */
    public static function validateCNPJ($cnpj) {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        
        if (strlen($cnpj) != 14) {
            return false;
        }
        
        // Verificar se todos os dígitos são iguais
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }
        
        // Calcular dígitos verificadores
        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        
        $resto = $soma % 11;
        
        if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto)) {
            return false;
        }
        
        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        
        $resto = $soma % 11;
        
        return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto) ? $cnpj : false;
    }
    
    /**
     * Validação de CPF
     */
    public static function validateCPF($cpf) {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        if (strlen($cpf) != 11) {
            return false;
        }
        
        // Verificar se todos os dígitos são iguais
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
        
        // Calcular primeiro dígito verificador
        for ($i = 0, $j = 10, $soma = 0; $i < 9; $i++) {
            $soma += $cpf[$i] * $j;
            $j--;
        }
        
        $resto = $soma % 11;
        
        if ($cpf[9] != ($resto < 2 ? 0 : 11 - $resto)) {
            return false;
        }
        
        // Calcular segundo dígito verificador
        for ($i = 0, $j = 11, $soma = 0; $i < 10; $i++) {
            $soma += $cpf[$i] * $j;
            $j--;
        }
        
        $resto = $soma % 11;
        
        return $cpf[10] == ($resto < 2 ? 0 : 11 - $resto) ? $cpf : false;
    }
    
    /**
     * Validação de data
     */
    public static function validateDate($date) {
        // Formatos aceitos: dd/mm/yyyy, yyyy-mm-dd, dd-mm-yyyy
        $patterns = [
            '/^(\d{2})\/(\d{2})\/(\d{4})$/',  // dd/mm/yyyy
            '/^(\d{4})-(\d{2})-(\d{2})$/',   // yyyy-mm-dd
            '/^(\d{2})-(\d{2})-(\d{4})$/'    // dd-mm-yyyy
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $date, $matches)) {
                if (count($matches) == 4) {
                    // Ajustar ordem baseada no padrão
                    if ($pattern === $patterns[0] || $pattern === $patterns[2]) {
                        // dd/mm/yyyy ou dd-mm-yyyy
                        $day = $matches[1];
                        $month = $matches[2];
                        $year = $matches[3];
                    } else {
                        // yyyy-mm-dd
                        $year = $matches[1];
                        $month = $matches[2];
                        $day = $matches[3];
                    }
                    
                    if (checkdate($month, $day, $year)) {
                        return $date;
                    }
                }
            }
        }
        
        return false;
    }
    
    /**
     * Validação de datetime
     */
    public static function validateDateTime($datetime) {
        $timestamp = strtotime($datetime);
        return $timestamp !== false ? $datetime : false;
    }
    
    /**
     * Validação de valores monetários
     */
    public static function validateCurrency($value) {
        // Aceita formatos: 1234.56, 1,234.56, R$ 1.234,56, etc.
        $clean = preg_replace('/[^\d.,]/', '', $value);
        
        // Normalizar formato decimal
        if (preg_match('/^\d+([.,]\d{2})?$/', $clean)) {
            return str_replace(',', '.', $clean);
        }
        
        // Formato brasileiro com separadores de milhares
        if (preg_match('/^\d{1,3}(\.\d{3})*,\d{2}$/', $clean)) {
            return str_replace(['.', ','], ['', '.'], $clean);
        }
        
        return false;
    }
    
    /**
     * Validação de nome de arquivo
     */
    public static function validateFilename($filename) {
        // Remove path information
        $filename = basename($filename);
        
        // Verificar caracteres perigosos
        if (preg_match('/[^a-zA-Z0-9._-]/', $filename)) {
            return false;
        }
        
        // Verificar extensões perigosas
        $dangerous_extensions = ['php', 'exe', 'bat', 'cmd', 'com', 'scr', 'vbs', 'js', 'jar'];
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($extension, $dangerous_extensions)) {
            return false;
        }
        
        return $filename;
    }
    
    /**
     * Validação de path
     */
    public static function validatePath($path) {
        // Prevenir path traversal
        if (strpos($path, '../') !== false || strpos($path, '..\\') !== false) {
            return false;
        }
        
        // Verificar caracteres perigosos
        if (preg_match('/[<>"|*?]/', $path)) {
            return false;
        }
        
        return $path;
    }
    
    /**
     * Rate limiting para prevenir ataques
     */
    public static function checkRateLimit($identifier = null) {
        if (!$identifier) {
            $identifier = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        }
        
        $current_time = time();
        
        // Limpar entradas antigas
        foreach (self::$rate_limit as $key => $data) {
            if ($current_time - $data['first_attempt'] > self::$time_window) {
                unset(self::$rate_limit[$key]);
            }
        }
        
        // Verificar tentativas do identificador atual
        if (!isset(self::$rate_limit[$identifier])) {
            self::$rate_limit[$identifier] = [
                'attempts' => 1,
                'first_attempt' => $current_time
            ];
            return true;
        }
        
        self::$rate_limit[$identifier]['attempts']++;
        
        if (self::$rate_limit[$identifier]['attempts'] > self::$max_attempts) {
            self::logSecurityIncident('RATE_LIMIT_EXCEEDED', "Too many attempts from: $identifier");
            return false;
        }
        
        return true;
    }
    
    /**
     * Logging de incidentes de segurança
     */
    private static function logSecurityIncident($type, $message) {
        $log_entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => $type,
            'message' => $message,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'url' => $_SERVER['REQUEST_URI'] ?? 'unknown',
            'user_id' => $_SESSION['userID'] ?? 'anonymous'
        ];
        
        // Criar diretório de logs se não existir
        $log_dir = dirname(__FILE__) . '/logs/validation';
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        $log_file = $log_dir . '/validation_' . date('Y-m-d') . '.log';
        file_put_contents($log_file, json_encode($log_entry) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Obtém valor sanitizado de $_REQUEST
     */
    public static function getRequest($key, $rules = []) {
        if (!isset($_REQUEST[$key])) {
            return isset($rules['default']) ? $rules['default'] : null;
        }
        
        return self::validate($_REQUEST[$key], $rules);
    }
    
    /**
     * Obtém valor sanitizado de $_GET
     */
    public static function getGet($key, $rules = []) {
        if (!isset($_GET[$key])) {
            return isset($rules['default']) ? $rules['default'] : null;
        }
        
        return self::validate($_GET[$key], $rules);
    }
    
    /**
     * Obtém valor sanitizado de $_POST
     */
    public static function getPost($key, $rules = []) {
        if (!isset($_POST[$key])) {
            return isset($rules['default']) ? $rules['default'] : null;
        }
        
        return self::validate($_POST[$key], $rules);
    }
}

/**
 * Classe para regras de validação específicas por módulo
 */
class ValidationRules {
    
    /**
     * Regras para módulo Credit
     */
    public static function getCreditRules() {
        return [
            'comm' => [
                'type' => 'string',
                'max_length' => 50,
                'whitelist' => [
                    'notif', 'return', 'accept', 'reject', 'view', 'edit', 
                    'done', 'list', 'search', 'import', 'export'
                ]
            ],
            'id' => [
                'type' => 'int',
                'min_length' => 1,
                'required' => true
            ],
            'limit_value' => [
                'type' => 'currency',
                'max_length' => 15
            ],
            'buyer_name' => [
                'type' => 'string',
                'max_length' => 255,
                'sanitize' => true
            ],
            'expiry_date' => [
                'type' => 'date',
                'required' => false
            ]
        ];
    }
    
    /**
     * Regras para módulo Client
     */
    public static function getClientRules() {
        return [
            'comm' => [
                'type' => 'string',
                'max_length' => 50,
                'whitelist' => [
                    'view', 'edit', 'update', 'list', 'search', 'register',
                    'password', 'profile', 'address', 'contact'
                ]
            ],
            'idInform' => [
                'type' => 'int',
                'required' => true
            ],
            'origem' => [
                'type' => 'string',
                'max_length' => 100,
                'sanitize' => true
            ],
            'cnpj' => [
                'type' => 'cnpj',
                'required' => false
            ],
            'cpf' => [
                'type' => 'cpf',
                'required' => false
            ],
            'email' => [
                'type' => 'email',
                'max_length' => 255
            ],
            'company_name' => [
                'type' => 'string',
                'max_length' => 255,
                'sanitize' => true
            ]
        ];
    }
    
    /**
     * Regras para módulo DVE
     */
    public static function getDveRules() {
        return [
            'comm' => [
                'type' => 'string',
                'max_length' => 50,
                'whitelist' => [
                    'view', 'open', 'done', 'entregavenciada', 'comite',
                    'include', 'exclude', 'modalidade', 'salvatotal', 'send',
                    'editImporter', 'consultadve', 'libera_dve'
                ]
            ],
            'export_value' => [
                'type' => 'currency',
                'max_length' => 20,
                'required' => true
            ],
            'currency' => [
                'type' => 'string',
                'max_length' => 3,
                'whitelist' => ['USD', 'EUR', 'BRL', 'GBP', 'JPY']
            ],
            'country_destination' => [
                'type' => 'string',
                'max_length' => 100,
                'sanitize' => true
            ],
            'dve_number' => [
                'type' => 'alphanumeric',
                'max_length' => 20
            ]
        ];
    }
    
    /**
     * Regras para módulo Executive
     */
    public static function getExecutiveRules() {
        return [
            'comm' => [
                'type' => 'string',
                'max_length' => 50,
                'whitelist' => [
                    'view', 'edit', 'create', 'approve', 'reject',
                    'report', 'export', 'analysis'
                ]
            ],
            'proposal_id' => [
                'type' => 'int',
                'required' => true
            ],
            'proposal_value' => [
                'type' => 'currency',
                'max_length' => 20
            ],
            'risk_rating' => [
                'type' => 'string',
                'max_length' => 10,
                'whitelist' => ['A', 'B', 'C', 'D', 'E']
            ]
        ];
    }
    
    /**
     * Obtém regras baseadas no módulo atual
     */
    public static function getRulesForModule($module) {
        switch (strtolower($module)) {
            case 'credit':
                return self::getCreditRules();
            case 'client':
                return self::getClientRules();
            case 'dve':
                return self::getDveRules();
            case 'executive':
                return self::getExecutiveRules();
            default:
                return [
                    'comm' => [
                        'type' => 'string',
                        'max_length' => 50,
                        'sanitize' => true
                    ]
                ];
        }
    }
}