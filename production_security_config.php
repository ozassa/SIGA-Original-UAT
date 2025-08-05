<?php
/**
 * SIGA Production Security Configuration
 * 
 * Master security configuration file that consolidates all security settings
 * for production environment. This file should be included early in the application.
 * 
 * @version 1.0
 * @author Claude Code - Security Production Cleanup
 */

// Prevent direct access
if (!defined('SIGA_SYSTEM')) {
    define('SIGA_SYSTEM', true);
}

// Production environment flag
if (!defined('SIGA_PRODUCTION')) {
    define('SIGA_PRODUCTION', true);
}

// Security constants
define('SECURITY_VERSION', '2.0');
define('SECURITY_LOG_DIR', dirname(__FILE__) . '/logs/security');
define('SESSION_TIMEOUT', 1800); // 30 minutes
define('SESSION_REGENERATE_INTERVAL', 300); // 5 minutes
define('CSRF_TOKEN_LIFETIME', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

/**
 * Initialize production security system
 */
function init_production_security() {
    // Load required security components
    require_once __DIR__ . '/production_session_config.php';
    require_once __DIR__ . '/security_functions.php';
    
    // Set production error handling
    configure_production_error_handling();
    
    // Apply security headers
    apply_production_security_headers();
    
    // Initialize logging
    init_security_logging();
    
    // Set security policies
    set_production_security_policies();
}

/**
 * Configure production error handling
 */
function configure_production_error_handling() {
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', SECURITY_LOG_DIR . '/php_errors.log');
    
    // Set custom error handlers
    set_error_handler('production_error_handler');
    set_exception_handler('production_exception_handler');
}

/**
 * Production error handler
 */
function production_error_handler($errno, $errstr, $errfile, $errline) {
    $log_message = "Error [$errno]: $errstr in $errfile on line $errline";
    error_log($log_message);
    return true;
}

/**
 * Production exception handler
 */
function production_exception_handler($exception) {
    $log_message = "Uncaught exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine();
    error_log($log_message);
    error_log($exception->getTraceAsString());
    
    http_response_code(500);
    exit('Sistema temporariamente indisponível. Tente novamente em alguns minutos.');
}

/**
 * Apply production security headers
 */
function apply_production_security_headers() {
    if (headers_sent()) {
        return false;
    }
    
    $is_https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    
    // Basic security headers
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('X-Permitted-Cross-Domain-Policies: none');
    
    // HSTS for HTTPS
    if ($is_https) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
    
    // Basic CSP for compatibility
    $csp = "default-src 'self' 'unsafe-inline' 'unsafe-eval'; " .
           "img-src 'self' data: https:; " .
           "font-src 'self' https://fonts.gstatic.com; " .
           "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
           "frame-ancestors 'self'; " .
           "object-src 'none'; " .
           "base-uri 'self'";
    
    header("Content-Security-Policy: $csp");
    
    // Cache control for sensitive pages
    if (is_sensitive_page()) {
        header('Cache-Control: no-cache, no-store, must-revalidate, private');
        header('Pragma: no-cache');
        header('Expires: 0');
    }
    
    return true;
}

/**
 * Check if current page is sensitive
 */
function is_sensitive_page() {
    $sensitive_paths = ['/access/', '/login', '/admin', '/password', '/credit', '/dve'];
    $current_path = $_SERVER['REQUEST_URI'] ?? '';
    
    foreach ($sensitive_paths as $path) {
        if (strpos($current_path, $path) !== false) {
            return true;
        }
    }
    return false;
}

/**
 * Initialize security logging
 */
function init_security_logging() {
    if (!is_dir(SECURITY_LOG_DIR)) {
        mkdir(SECURITY_LOG_DIR, 0755, true);
    }
    
    // Create .htaccess to protect log directory
    $htaccess_file = SECURITY_LOG_DIR . '/.htaccess';
    if (!file_exists($htaccess_file)) {
        file_put_contents($htaccess_file, "Order Deny,Allow\nDeny from all\n");
    }
}

/**
 * Set production security policies
 */
function set_production_security_policies() {
    // PHP security settings
    ini_set('expose_php', 0);
    ini_set('allow_url_fopen', 0);
    ini_set('allow_url_include', 0);
    ini_set('enable_dl', 0);
    ini_set('file_uploads', 1);
    ini_set('upload_max_filesize', '10M');
    ini_set('post_max_size', '10M');
    ini_set('max_execution_time', 30);
    ini_set('max_input_time', 30);
    ini_set('memory_limit', '128M');
    
    // Session security
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Strict');
}

/**
 * Security health check
 */
function security_health_check() {
    $checks = [
        'logs_directory' => is_dir(SECURITY_LOG_DIR) && is_writable(SECURITY_LOG_DIR),
        'session_started' => session_status() === PHP_SESSION_ACTIVE,
        'https_available' => isset($_SERVER['HTTPS']),
        'security_headers' => !headers_sent(),
        'error_logging' => ini_get('log_errors') == 1,
        'display_errors_off' => ini_get('display_errors') == 0
    ];
    
    return $checks;
}

/**
 * Get security configuration status
 */
function get_security_status() {
    return [
        'version' => SECURITY_VERSION,
        'production_mode' => SIGA_PRODUCTION,
        'session_timeout' => SESSION_TIMEOUT,
        'csrf_enabled' => function_exists('generate_csrf_token'),
        'xss_protection' => function_exists('safe_output'),
        'logging_enabled' => is_dir(SECURITY_LOG_DIR),
        'health_check' => security_health_check()
    ];
}

/**
 * Log security configuration initialization
 */
function log_security_init() {
    $init_data = [
        'timestamp' => date('Y-m-d H:i:s'),
        'event' => 'security_init',
        'version' => SECURITY_VERSION,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 255)
    ];
    
    $log_file = SECURITY_LOG_DIR . '/security_init_' . date('Y-m-d') . '.log';
    error_log(json_encode($init_data), 3, $log_file);
}

// Auto-initialize if not in CLI mode
if (php_sapi_name() !== 'cli') {
    init_production_security();
    
    // Log initialization (only once per request)
    if (!defined('SECURITY_INITIALIZED')) {
        define('SECURITY_INITIALIZED', true);
        log_security_init();
    }
}

?>