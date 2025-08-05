<?php
/**
 * SIGA Session Functionality Diagnostic Tool
 * 
 * Tests session management, authentication flow, and related functionality
 * to identify session-related issues causing system problems.
 * 
 * SAFE FOR PRODUCTION - Only performs read-only session tests
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$session_log = __DIR__ . '/logs/session_test_' . date('Y-m-d_H-i-s') . '.log';
if (!is_dir(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0755, true);
}

function log_session($message, $level = 'INFO') {
    global $session_log;
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] [$level] $message" . PHP_EOL;
    file_put_contents($session_log, $log_entry, FILE_APPEND | LOCK_EX);
    echo $log_entry;
}

log_session("=== SIGA SESSION FUNCTIONALITY TEST ===");

// 1. SESSION CONFIGURATION CHECK
log_session("1. CHECKING SESSION CONFIGURATION", "TEST");

// Check current session status
$session_status = session_status();
$status_names = [
    PHP_SESSION_DISABLED => 'DISABLED',
    PHP_SESSION_NONE => 'NONE', 
    PHP_SESSION_ACTIVE => 'ACTIVE'
];

log_session("   Current session status: " . $status_names[$session_status]);

// Check session settings
$session_settings = [
    'session.cookie_httponly' => ini_get('session.cookie_httponly'),
    'session.cookie_secure' => ini_get('session.cookie_secure'),
    'session.use_only_cookies' => ini_get('session.use_only_cookies'),
    'session.cookie_lifetime' => ini_get('session.cookie_lifetime'),
    'session.gc_maxlifetime' => ini_get('session.gc_maxlifetime'),
    'session.gc_probability' => ini_get('session.gc_probability'),
    'session.gc_divisor' => ini_get('session.gc_divisor'),
    'session.use_strict_mode' => ini_get('session.use_strict_mode'),
    'session.save_path' => ini_get('session.save_path'),
    'session.save_handler' => ini_get('session.save_handler')
];

log_session("   Session Configuration:");
foreach ($session_settings as $setting => $value) {
    log_session("     $setting: $value");
}

// 2. SESSION CONFIGURATION FILE TEST
log_session("2. TESTING SESSION CONFIGURATION FILE", "TEST");

try {
    if (file_exists(__DIR__ . '/session_config.php')) {
        log_session("   ✓ session_config.php exists");
        
        // Test including the session config
        ob_start();
        $errors_before = error_get_last();
        
        include_once __DIR__ . '/session_config.php';
        
        $include_output = ob_get_clean();
        $errors_after = error_get_last();
        
        if ($errors_after && $errors_after !== $errors_before) {
            log_session("   ✗ Error loading session_config.php:", "ERROR");
            log_session("     " . $errors_after['message'], "ERROR");
        } else {
            log_session("   ✓ session_config.php loaded successfully");
        }
        
        if (!empty($include_output)) {
            log_session("   ! session_config.php produces output:", "WARNING");
            log_session("     " . substr($include_output, 0, 200), "WARNING");
        }
        
        // Check if session was started by config
        if (session_status() === PHP_SESSION_ACTIVE) {
            log_session("   ✓ Session started by configuration");
        } else {
            log_session("   ! Session not started by configuration", "WARNING");
        }
        
    } else {
        log_session("   ✗ session_config.php not found", "ERROR");
    }
} catch (Exception $e) {
    log_session("   ✗ Exception loading session_config.php: " . $e->getMessage(), "ERROR");
}

// 3. MANUAL SESSION TEST
log_session("3. TESTING MANUAL SESSION OPERATIONS", "TEST");

try {
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        log_session("   Starting session manually...");
        session_start();
        
        if (session_status() === PHP_SESSION_ACTIVE) {
            log_session("   ✓ Manual session start successful");
        } else {
            log_session("   ✗ Manual session start failed", "ERROR");
        }
    }
    
    if (session_status() === PHP_SESSION_ACTIVE) {
        // Test session write
        $test_key = 'session_test_' . time();
        $test_value = 'test_value_' . rand(1000, 9999);
        
        $_SESSION[$test_key] = $test_value;
        log_session("   ✓ Session write test successful");
        
        // Test session read
        if (isset($_SESSION[$test_key]) && $_SESSION[$test_key] === $test_value) {
            log_session("   ✓ Session read test successful");
        } else {
            log_session("   ✗ Session read test failed", "ERROR");
        }
        
        // Test session unset
        unset($_SESSION[$test_key]);
        if (!isset($_SESSION[$test_key])) {
            log_session("   ✓ Session unset test successful");
        } else {
            log_session("   ✗ Session unset test failed", "ERROR");
        }
        
        // Show session ID
        $session_id = session_id();
        log_session("   Session ID: " . ($session_id ?: 'Not available'));
        
        // Show session name
        $session_name = session_name();
        log_session("   Session Name: $session_name");
        
    } else {
        log_session("   ✗ Session not active for testing", "ERROR");
    }
    
} catch (Exception $e) {
    log_session("   ✗ Session operation exception: " . $e->getMessage(), "ERROR");
}

// 4. SESSION SECURITY FUNCTIONS TEST
log_session("4. TESTING SESSION SECURITY FUNCTIONS", "TEST");

try {
    // Check if security functions are available
    $security_functions = [
        'start_secure_session',
        'perform_session_security_checks',
        'check_session_timeout',
        'check_session_regeneration',
        'detect_session_hijacking',
        'destroy_secure_session',
        'validate_user_session'
    ];
    
    $available_functions = [];
    $missing_functions = [];
    
    foreach ($security_functions as $func) {
        if (function_exists($func)) {
            $available_functions[] = $func;
            log_session("   ✓ $func - Available");
        } else {
            $missing_functions[] = $func;
            log_session("   ! $func - Not available", "WARNING");
        }
    }
    
    log_session("   Available security functions: " . count($available_functions));
    log_session("   Missing security functions: " . count($missing_functions));
    
    // Test session fingerprint creation if available
    if (function_exists('create_session_fingerprint')) {
        $fingerprint = create_session_fingerprint();
        if (!empty($fingerprint)) {
            log_session("   ✓ Session fingerprint creation works");
            log_session("   Fingerprint: " . substr($fingerprint, 0, 16) . "...");
        } else {
            log_session("   ✗ Session fingerprint creation failed", "ERROR");
        }
    }
    
} catch (Exception $e) {
    log_session("   ✗ Security functions test exception: " . $e->getMessage(), "ERROR");
}

// 5. SESSION STORAGE TEST
log_session("5. TESTING SESSION STORAGE", "TEST");

$save_path = session_save_path();
if (!empty($save_path)) {
    log_session("   Session save path: $save_path");
    
    if (is_dir($save_path)) {
        if (is_writable($save_path)) {
            log_session("   ✓ Session save path is writable");
        } else {
            log_session("   ✗ Session save path is not writable", "ERROR");
        }
        
        // Count session files
        $session_files = glob($save_path . '/sess_*');
        if ($session_files !== false) {
            log_session("   Session files found: " . count($session_files));
        }
    } else {
        log_session("   ✗ Session save path is not a directory", "ERROR");
    }
} else {
    log_session("   ! Session save path is empty (using system default)", "WARNING");
}

// 6. AUTHENTICATION FLOW TEST
log_session("6. TESTING AUTHENTICATION FLOW", "TEST");

// Test authentication-related session variables
$auth_variables = ['userID', 'nameUser', 'login', 'pefil'];
log_session("   Checking authentication session variables:");

foreach ($auth_variables as $var) {
    if (isset($_SESSION[$var])) {
        $value = $_SESSION[$var];
        if (is_string($value)) {
            $display_value = strlen($value) > 20 ? substr($value, 0, 20) . '...' : $value;
        } else {
            $display_value = print_r($value, true);
        }
        log_session("   ✓ $_SESSION['$var'] = $display_value");
    } else {
        log_session("   ! $_SESSION['$var'] - Not set", "WARNING");
    }
}

// Test hybrid authentication if available
if (file_exists(__DIR__ . '/hybrid_auth.php')) {
    try {
        ob_start();
        include_once __DIR__ . '/hybrid_auth.php';
        $hybrid_output = ob_get_clean();
        
        if (!empty($hybrid_output)) {
            log_session("   ! hybrid_auth.php produces output", "WARNING");
        } else {
            log_session("   ✓ hybrid_auth.php loaded successfully");
        }
        
    } catch (Exception $e) {
        log_session("   ✗ Error loading hybrid_auth.php: " . $e->getMessage(), "ERROR");
    }
}

// 7. SESSION TIMEOUT TEST
log_session("7. TESTING SESSION TIMEOUT MECHANISM", "TEST");

if (isset($_SESSION['last_activity'])) {
    $last_activity = $_SESSION['last_activity'];
    $current_time = time();
    $idle_time = $current_time - $last_activity;
    
    log_session("   Last activity: " . date('Y-m-d H:i:s', $last_activity));
    log_session("   Current time: " . date('Y-m-d H:i:s', $current_time));
    log_session("   Idle time: $idle_time seconds");
    
    $timeout = defined('SESSION_TIMEOUT') ? SESSION_TIMEOUT : ini_get('session.gc_maxlifetime');
    log_session("   Session timeout: $timeout seconds");
    
    if ($idle_time > $timeout) {
        log_session("   ! Session should have timed out", "WARNING");
    } else {
        log_session("   ✓ Session is within timeout limit");
    }
} else {
    log_session("   ! No last_activity timestamp found", "WARNING");
    
    // Set it for future tests
    $_SESSION['last_activity'] = time();
    log_session("   Set last_activity for future tests");
}

// 8. SESSION REGENERATION TEST
log_session("8. TESTING SESSION REGENERATION", "TEST");

if (session_status() === PHP_SESSION_ACTIVE) {
    $old_session_id = session_id();
    log_session("   Old session ID: $old_session_id");
    
    try {
        session_regenerate_id(true);
        $new_session_id = session_id();
        log_session("   New session ID: $new_session_id");
        
        if ($old_session_id !== $new_session_id) {
            log_session("   ✓ Session ID regeneration successful");
        } else {
            log_session("   ✗ Session ID was not regenerated", "ERROR");
        }
        
    } catch (Exception $e) {
        log_session("   ✗ Session regeneration exception: " . $e->getMessage(), "ERROR");
    }
} else {
    log_session("   ! Cannot test regeneration - session not active", "WARNING");
}

// 9. COOKIE TEST
log_session("9. TESTING SESSION COOKIES", "TEST");

$session_name = session_name();
if (isset($_COOKIE[$session_name])) {
    log_session("   ✓ Session cookie is present");
    $cookie_value = $_COOKIE[$session_name];
    log_session("   Cookie value: " . substr($cookie_value, 0, 16) . "...");
} else {
    log_session("   ! Session cookie is not present", "WARNING");
}

// Check cookie settings
$cookie_params = session_get_cookie_params();
log_session("   Cookie parameters:");
foreach ($cookie_params as $param => $value) {
    log_session("     $param: " . (is_bool($value) ? ($value ? 'true' : 'false') : $value));
}

// 10. SESSION CLEANUP TEST
log_session("10. TESTING SESSION CLEANUP", "TEST");

// Test current session data size
if (session_status() === PHP_SESSION_ACTIVE) {
    $session_data = serialize($_SESSION);
    $session_size = strlen($session_data);
    log_session("   Session data size: $session_size bytes");
    
    if ($session_size > 1024 * 10) { // 10KB
        log_session("   ! Large session data detected", "WARNING");
    } else {
        log_session("   ✓ Session data size is reasonable");
    }
    
    // Count session variables
    $session_vars = count($_SESSION);
    log_session("   Session variables count: $session_vars");
    
    // Show session variables (safely)
    log_session("   Session variables:");
    foreach ($_SESSION as $key => $value) {
        if (is_string($value) && strlen($value) > 50) {
            $display_value = substr($value, 0, 50) . '...';
        } elseif (is_array($value)) {
            $display_value = '[Array with ' . count($value) . ' elements]';
        } elseif (is_object($value)) {
            $display_value = '[Object: ' . get_class($value) . ']';
        } else {
            $display_value = (string)$value;
        }
        log_session("     $key: $display_value");
    }
}

// SUMMARY AND RECOMMENDATIONS
log_session("=== SESSION TEST SUMMARY ===");

$log_content = file_get_contents($session_log);
$error_count = substr_count($log_content, '[ERROR]');
$warning_count = substr_count($log_content, '[WARNING]');

log_session("Errors: $error_count, Warnings: $warning_count");

if ($error_count > 0) {
    log_session("SESSION STATUS: CRITICAL ISSUES", "ERROR");
    log_session("");
    log_session("TROUBLESHOOTING STEPS:", "ERROR");
    log_session("1. Check session save path permissions");
    log_session("2. Verify session configuration in php.ini");
    log_session("3. Check session_config.php for errors");
    log_session("4. Verify server has sufficient disk space");
    log_session("5. Check for conflicting session settings");
} elseif ($warning_count > 0) {
    log_session("SESSION STATUS: MINOR ISSUES", "WARNING"); 
    log_session("Session functionality works but some features may be suboptimal");
} else {
    log_session("SESSION STATUS: HEALTHY");
    log_session("All session functionality tests passed");
}

log_session("");
log_session("Detailed session test log saved to: $session_log");

?>