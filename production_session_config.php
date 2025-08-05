<?php
/**
 * SIGA Production Session Configuration
 * 
 * Optimized session configuration for production environment.
 * Consolidates security settings from multiple previous config files.
 * 
 * @version 1.0
 * @author Claude Code - Security Production Cleanup
 */

// Prevent direct access
if (!defined('SIGA_SYSTEM')) {
    define('SIGA_SYSTEM', true);
}

// Exit early for CLI environments
if (php_sapi_name() === 'cli') {
    return;
}

// Production security constants
define('SESSION_TIMEOUT', 1800); // 30 minutes
define('SESSION_REGENERATE_INTERVAL', 300); // 5 minutes
define('SECURITY_LOG_DIR', dirname(__FILE__) . '/logs/security');

// Detect HTTPS
$server_port = $_SERVER['SERVER_PORT'] ?? 80;
$https_check = $_SERVER['HTTPS'] ?? 'off';
$is_https = (!empty($https_check) && $https_check !== 'off') || $server_port == 443;

/**
 * Configure secure session parameters
 */
function configure_secure_session($is_https) {
    // Set secure session configuration
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', $is_https ? 1 : 0);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.cookie_lifetime', 0); // Expire when browser closes
    ini_set('session.gc_maxlifetime', SESSION_TIMEOUT);
    ini_set('session.gc_probability', 1);
    ini_set('session.gc_divisor', 100);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.sid_length', 48);
    ini_set('session.sid_bits_per_character', 6);
    
    // Additional security settings
    ini_set('session.hash_function', 'sha256');
    ini_set('session.hash_bits_per_character', 6);
    ini_set('session.entropy_length', 32);
}

/**
 * Start secure session
 */
function start_secure_session() {
    global $is_https;
    
    if (session_status() === PHP_SESSION_NONE) {
        configure_secure_session($is_https);
        session_start();
    }
    
    // Initialize security checks if user is logged in
    if (isset($_SESSION['userID']) && !empty($_SESSION['userID'])) {
        perform_session_security_checks();
    }
}

/**
 * Perform session security checks
 */
function perform_session_security_checks() {
    check_session_timeout();
    check_session_regeneration();
    detect_session_hijacking();
}

/**
 * Check for session timeout
 */
function check_session_timeout() {
    if (isset($_SESSION['last_activity'])) {
        if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
            log_security_event('session_timeout', 'Session expired for user: ' . ($_SESSION['userID'] ?? 'unknown'));
            destroy_secure_session();
            redirect_to_login('Session expired');
            return;
        }
    }
    
    $_SESSION['last_activity'] = time();
}

/**
 * Check if session ID needs regeneration
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
 * Detect potential session hijacking
 */
function detect_session_hijacking() {
    $current_fingerprint = create_session_fingerprint();
    
    if (isset($_SESSION['session_fingerprint'])) {
        if ($_SESSION['session_fingerprint'] !== $current_fingerprint) {
            log_security_event('session_hijacking', 'Potential hijacking detected for user: ' . ($_SESSION['userID'] ?? 'unknown'));
            destroy_secure_session();
            redirect_to_login('Security violation detected');
            return;
        }
    } else {
        $_SESSION['session_fingerprint'] = $current_fingerprint;
    }
}

/**
 * Create session fingerprint
 */
function create_session_fingerprint() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $accept_language = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
    $accept_encoding = $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '';
    
    return hash('sha256', $user_agent . $accept_language . $accept_encoding);
}

/**
 * Securely destroy session
 */
function destroy_secure_session() {
    $_SESSION = array();
    
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    session_destroy();
}

/**
 * Validate user session
 */
function validate_user_session() {
    if (!isset($_SESSION['userID']) || empty($_SESSION['userID'])) {
        return false;
    }
    
    return true;
}

/**
 * Log security events
 */
function log_security_event($type, $message) {
    if (!is_dir(SECURITY_LOG_DIR)) {
        mkdir(SECURITY_LOG_DIR, 0755, true);
    }
    
    $log_entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'type' => $type,
        'message' => $message,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ];
    
    $log_file = SECURITY_LOG_DIR . '/session_security_' . date('Y-m-d') . '.log';
    file_put_contents($log_file, json_encode($log_entry) . PHP_EOL, FILE_APPEND | LOCK_EX);
}

/**
 * Redirect to login page
 */
function redirect_to_login($message = '') {
    $redirect_url = '/index.php';
    if (!empty($message)) {
        $redirect_url .= '?erro=' . urlencode($message);
    }
    
    header("Location: $redirect_url");
    exit();
}

// Auto-start secure session
start_secure_session();

?>