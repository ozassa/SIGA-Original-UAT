<?php
/**
 * SIGA Advanced Security System
 * Implementa CSP avançado, security headers modernos e sistema de proteção completo
 * 
 * Features:
 * - CSP inteligente com nonces dinâmicos
 * - Suporte completo ao TinyMCE e jQuery
 * - Modo Report-Only para testes
 * - Logging de violações CSP
 * - Fallbacks para navegadores antigos
 * - Headers de segurança modernos
 */

class AdvancedSecuritySystem {
    
    private $nonce;
    private $reportOnly = false;
    private $logViolations = true;
    private $logFile = 'logs/csp_violations.log';
    private $allowUnsafeInline = true; // Para TinyMCE e scripts legados
    private $enableTrustedTypes = false; // Para navegadores que suportam
    private $config = [];
    
    public function __construct($reportOnly = null) {
        $this->loadConfig();
        $this->reportOnly = $reportOnly !== null ? $reportOnly : $this->config['report_only'];
        $this->logViolations = $this->config['log_violations'];
        $this->allowUnsafeInline = $this->config['allow_unsafe_inline'];
        $this->enableTrustedTypes = $this->config['enable_trusted_types'];
        $this->nonce = $this->generateSecureNonce();
        $this->ensureLogDirectory();
    }
    
    /**
     * Gera nonce criptograficamente seguro
     */
    private function generateSecureNonce() {
        return base64_encode(random_bytes(16));
    }
    
    /**
     * Carrega configuração do arquivo JSON
     */
    private function loadConfig() {
        $configFile = 'csp_config.json';
        $defaultConfig = [
            'report_only' => true,
            'log_violations' => true,
            'allow_unsafe_inline' => true,
            'allow_unsafe_eval' => true,
            'enable_trusted_types' => false,
            'custom_sources' => [],
            'notifications' => ['email_alerts' => false],
            'page_specific_policies' => []
        ];
        
        if (file_exists($configFile)) {
            $config = json_decode(file_get_contents($configFile), true);
            $this->config = array_merge($defaultConfig, $config ?: []);
        } else {
            $this->config = $defaultConfig;
        }
    }
    
    /**
     * Garante que o diretório de logs existe
     */
    private function ensureLogDirectory() {
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }
    
    /**
     * Detecta recursos necessários baseado na página atual
     */
    private function detectPageRequirements() {
        $currentPage = $_SERVER['REQUEST_URI'] ?? '';
        $requirements = [
            'tinymce' => false,
            'jquery' => true,
            'calendar' => false,
            'upload' => false,
            'pdf' => false,
            'maps' => false
        ];
        
        // Detecta TinyMCE
        if (strpos($currentPage, 'caixa_texto') !== false || 
            strpos($currentPage, 'tinymce') !== false) {
            $requirements['tinymce'] = true;
        }
        
        // Detecta funcionalidades de calendário
        if (strpos($currentPage, 'calendar') !== false || 
            strpos($currentPage, 'data') !== false) {
            $requirements['calendar'] = true;
        }
        
        // Detecta upload de arquivos
        if (strpos($currentPage, 'upload') !== false || 
            strpos($currentPage, 'arquivo') !== false) {
            $requirements['upload'] = true;
        }
        
        // Detecta geração de PDF
        if (strpos($currentPage, 'pdf') !== false || 
            strpos($currentPage, 'relatorio') !== false) {
            $requirements['pdf'] = true;
        }
        
        return $requirements;
    }
    
    /**
     * Gera CSP inteligente baseado nos requisitos da página
     */
    public function generateIntelligentCSP() {
        $requirements = $this->detectPageRequirements();
        
        // Base CSP policies
        $policies = [
            'default-src' => ["'self'"],
            'base-uri' => ["'self'"],
            'form-action' => ["'self'"],
            'frame-ancestors' => ["'none'"],
            'upgrade-insecure-requests' => []
        ];
        
        // Script sources - crítico para funcionalidade
        $scriptSrc = ["'self'"];
        
        // Check page-specific policies
        $pagePolicy = $this->getPageSpecificPolicy();
        
        if ($this->allowUnsafeInline || $requirements['tinymce'] || ($pagePolicy && $pagePolicy['allow_unsafe_inline'])) {
            $scriptSrc[] = "'unsafe-inline'"; // Necessário para TinyMCE e scripts legados
        }
        if ($requirements['tinymce'] || $this->config['allow_unsafe_eval'] || ($pagePolicy && $pagePolicy['allow_unsafe_eval'])) {
            $scriptSrc[] = "'unsafe-eval'"; // TinyMCE precisa de eval
        }
        
        // Adiciona nonce para scripts específicos
        $scriptSrc[] = "'nonce-{$this->nonce}'";
        
        // Custom sources from config
        if (!empty($this->config['custom_sources']['script_src'])) {
            $scriptSrc = array_merge($scriptSrc, $this->config['custom_sources']['script_src']);
        }
        
        // Page-specific additional sources
        if ($pagePolicy && !empty($pagePolicy['additional_sources']['script_src'])) {
            $scriptSrc = array_merge($scriptSrc, $pagePolicy['additional_sources']['script_src']);
        }
        
        $policies['script-src'] = $scriptSrc;
        
        // Style sources
        $styleSrc = ["'self'", "'unsafe-inline'"]; // unsafe-inline necessário para estilos dinâmicos
        if ($requirements['tinymce']) {
            $styleSrc[] = 'https://fonts.googleapis.com';
        }
        $policies['style-src'] = $styleSrc;
        
        // Image sources
        $imgSrc = ["'self'", 'data:', 'https:', 'http:'];
        if ($requirements['upload']) {
            $imgSrc[] = 'blob:';
        }
        $policies['img-src'] = $imgSrc;
        
        // Font sources
        $policies['font-src'] = ["'self'", 'https://fonts.gstatic.com', 'data:'];
        
        // Connect sources - APIs externas
        $connectSrc = ["'self'"];
        
        // Custom connect sources from config
        if (!empty($this->config['custom_sources']['connect_src'])) {
            $connectSrc = array_merge($connectSrc, $this->config['custom_sources']['connect_src']);
        }
        
        $policies['connect-src'] = $connectSrc;
        
        // Frame/Object sources
        $policies['frame-src'] = $requirements['pdf'] ? ["'self'", 'blob:'] : ["'none'"];
        $policies['object-src'] = $requirements['pdf'] ? ["'self'", 'data:'] : ["'none'"];
        
        // Worker sources
        if ($requirements['upload'] || $requirements['pdf']) {
            $policies['worker-src'] = ["'self'", 'blob:'];
        }
        
        // Child sources para compatibilidade
        if ($requirements['pdf']) {
            $policies['child-src'] = ["'self'", 'blob:'];
        }
        
        // Media sources
        $policies['media-src'] = ["'self'", 'data:', 'blob:'];
        
        return $this->formatCSPHeader($policies);
    }
    
    /**
     * Get page-specific policy if applicable
     */
    private function getPageSpecificPolicy() {
        $currentPage = $_SERVER['REQUEST_URI'] ?? '';
        
        foreach ($this->config['page_specific_policies'] as $policyName => $policy) {
            if (isset($policy['pattern']) && strpos($currentPage, $policy['pattern']) !== false) {
                return $policy;
            }
        }
        
        return null;
    }
    
    /**
     * Formata o header CSP
     */
    private function formatCSPHeader($policies) {
        $cspParts = [];
        foreach ($policies as $directive => $sources) {
            if (empty($sources)) {
                $cspParts[] = $directive;
            } else {
                $cspParts[] = $directive . ' ' . implode(' ', $sources);
            }
        }
        return implode('; ', $cspParts);
    }
    
    /**
     * Aplica todos os security headers avançados
     */
    public function applyAdvancedSecurityHeaders() {
        if (headers_sent()) {
            return false;
        }
        
        // Content Security Policy
        $csp = $this->generateIntelligentCSP();
        $cspHeader = $this->reportOnly ? 'Content-Security-Policy-Report-Only' : 'Content-Security-Policy';
        
        if ($this->logViolations) {
            $csp .= '; report-uri /csp-violation-handler.php';
        }
        
        header("{$cspHeader}: {$csp}");
        
        // X-Frame-Options - Compatibilidade com CSP
        header("X-Frame-Options: DENY");
        
        // X-Content-Type-Options
        header("X-Content-Type-Options: nosniff");
        
        // X-XSS-Protection com modo block
        header("X-XSS-Protection: 1; mode=block");
        
        // Referrer Policy otimizada
        header("Referrer-Policy: strict-origin-when-cross-origin");
        
        // Permissions Policy moderna
        $permissionsPolicy = implode(', ', [
            'camera=()',
            'microphone=()',
            'geolocation=()',
            'payment=()',
            'usb=()',
            'accelerometer=()',
            'ambient-light-sensor=()',
            'autoplay=()',
            'battery=()',
            'display-capture=()',
            'document-domain=()',
            'encrypted-media=()',
            'fullscreen=(self)',
            'gyroscope=()',
            'magnetometer=()',
            'midi=()',
            'picture-in-picture=()',
            'publickey-credentials-get=()',
            'screen-wake-lock=()',
            'sync-xhr=(self)',
            'web-share=()'
        ]);
        header("Permissions-Policy: {$permissionsPolicy}");
        
        // HSTS - apenas se usando HTTPS
        if ($this->isHTTPS()) {
            header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
        }
        
        // Cross-Origin Policies para isolamento avançado
        header("Cross-Origin-Embedder-Policy: credentialless"); // Menos restritivo que require-corp
        header("Cross-Origin-Opener-Policy: same-origin-allow-popups"); // Permite popups necessários
        header("Cross-Origin-Resource-Policy: cross-origin"); // Permite recursos de outros origins se necessário
        
        // Cache Control para páginas sensíveis
        if ($this->isSensitivePage()) {
            header("Cache-Control: no-cache, no-store, must-revalidate, private");
            header("Pragma: no-cache");
            header("Expires: 0");
        }
        
        // Headers adicionais de segurança
        header("X-Permitted-Cross-Domain-Policies: none");
        
        // Expect-CT para Certificate Transparency
        if ($this->isHTTPS()) {
            header("Expect-CT: max-age=86400, enforce");
        }
        
        // Trusted Types (se suportado)
        if ($this->enableTrustedTypes) {
            header("Require-Trusted-Types-For: 'script'");
            header("Trusted-Types: default 'unsafe-inline'");
        }
        
        return true;
    }
    
    /**
     * Verifica se está usando HTTPS
     */
    private function isHTTPS() {
        return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    }
    
    /**
     * Verifica se é uma página sensível
     */
    private function isSensitivePage() {
        $sensitivePaths = ['/access/', '/login', '/admin', '/password', '/credit'];
        $currentPath = $_SERVER['REQUEST_URI'] ?? '';
        
        foreach ($sensitivePaths as $path) {
            if (strpos($currentPath, $path) !== false) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Gera JavaScript com nonce para scripts inline críticos
     */
    public function generateNonceScript($script) {
        return "<script nonce=\"{$this->nonce}\">{$script}</script>";
    }
    
    /**
     * Retorna o nonce atual para uso em templates
     */
    public function getNonce() {
        return $this->nonce;
    }
    
    /**
     * Log de violações CSP
     */
    public function logViolation($violationData) {
        if (!$this->logViolations) {
            return;
        }
        
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'violation' => $violationData
        ];
        
        $logLine = json_encode($logEntry) . "\n";
        file_put_contents($this->logFile, $logLine, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Configura o modo Report-Only
     */
    public function setReportOnly($enabled = true) {
        $this->reportOnly = $enabled;
    }
    
    /**
     * Habilita/desabilita Trusted Types
     */
    public function enableTrustedTypes($enabled = true) {
        $this->enableTrustedTypes = $enabled;
    }
    
    /**
     * Configura se deve permitir unsafe-inline
     */
    public function allowUnsafeInline($allow = true) {
        $this->allowUnsafeInline = $allow;
    }
    
    /**
     * Método estático para uso rápido
     */
    public static function quickSetup($reportOnly = false) {
        $security = new self($reportOnly);
        $security->applyAdvancedSecurityHeaders();
        return $security;
    }
}

/**
 * Funções de conveniência para usar no sistema
 */

/**
 * Inicializa o sistema de segurança avançado
 */
function init_advanced_security($reportOnly = false) {
    global $advanced_security;
    $advanced_security = new AdvancedSecuritySystem($reportOnly);
    $advanced_security->applyAdvancedSecurityHeaders();
    return $advanced_security;
}

/**
 * Gera script com nonce
 */
function nonce_script($script) {
    global $advanced_security;
    if (!isset($advanced_security)) {
        return "<script>{$script}</script>";
    }
    return $advanced_security->generateNonceScript($script);
}

/**
 * Obtém o nonce atual
 */
function get_security_nonce() {
    global $advanced_security;
    return isset($advanced_security) ? $advanced_security->getNonce() : '';
}

/**
 * Verifica se o navegador suporta CSP
 */
function browser_supports_csp() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    // Lista de navegadores muito antigos que não suportam CSP
    $unsupportedPatterns = [
        '/MSIE [1-9]\./',           // IE 9 e anterior
        '/Chrome\/[1-9]\./',        // Chrome muito antigo
        '/Chrome\/1[0-9]\./',       // Chrome 10-19
        '/Firefox\/[1-3]\./',       // Firefox muito antigo
        '/Safari\/[1-4]\./'         // Safari muito antigo
    ];
    
    foreach ($unsupportedPatterns as $pattern) {
        if (preg_match($pattern, $userAgent)) {
            return false;
        }
    }
    
    return true;
}

/**
 * Aplica fallback de segurança para navegadores antigos
 */
function apply_legacy_security_fallback() {
    if (headers_sent()) {
        return;
    }
    
    // Headers básicos que funcionam em navegadores antigos
    header("X-Frame-Options: DENY");
    header("X-Content-Type-Options: nosniff");
    header("X-XSS-Protection: 1; mode=block");
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
}

?>