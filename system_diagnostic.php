<?php
/**
 * SIGA System Comprehensive Diagnostic Tool
 * 
 * This script performs a complete health check of the SIGA system
 * to identify the exact causes of system errors.
 * 
 * SAFE FOR PRODUCTION - No data modification, only testing and logging
 */

// Set error reporting for diagnostic purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Create diagnostic log
$diagnostic_log = __DIR__ . '/logs/system_diagnostic_' . date('Y-m-d_H-i-s') . '.log';
if (!is_dir(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0755, true);
}

function log_diagnostic($message, $level = 'INFO') {
    global $diagnostic_log;
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] [$level] $message" . PHP_EOL;
    file_put_contents($diagnostic_log, $log_entry, FILE_APPEND | LOCK_EX);
    echo $log_entry;
}

log_diagnostic("=== SIGA SYSTEM COMPREHENSIVE DIAGNOSTIC STARTED ===");
log_diagnostic("PHP Version: " . PHP_VERSION);
log_diagnostic("Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'));
log_diagnostic("Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'));
log_diagnostic("Script Path: " . __FILE__);

// 1. CRITICAL FILE EXISTENCE CHECK
log_diagnostic("1. CHECKING CRITICAL FILE EXISTENCE", "TEST");

$critical_files = [
    'config.php' => __DIR__ . '/config.php',
    'session_config.php' => __DIR__ . '/session_config.php',
    'security_functions.php' => __DIR__ . '/security_functions.php', 
    'hybrid_auth.php' => __DIR__ . '/hybrid_auth.php',
    'src/dbOpen.php' => __DIR__ . '/src/dbOpen.php',
    'src/role/rolePrefix.php' => __DIR__ . '/src/role/rolePrefix.php',
    'src/role/access/Access.php' => __DIR__ . '/src/role/access/Access.php',
    'remember.php' => __DIR__ . '/remember.php',
    'index.php' => __DIR__ . '/index.php'
];

$missing_files = [];
foreach ($critical_files as $name => $path) {
    if (file_exists($path)) {
        if (is_readable($path)) {
            log_diagnostic("   ✓ $name - EXISTS and READABLE");
        } else {
            log_diagnostic("   ✗ $name - EXISTS but NOT READABLE", "ERROR");
            $missing_files[] = $name;
        }
    } else {
        log_diagnostic("   ✗ $name - MISSING", "ERROR");
        $missing_files[] = $name;
    }
}

if (!empty($missing_files)) {
    log_diagnostic("CRITICAL: Missing files detected: " . implode(', ', $missing_files), "ERROR");
}

// 2. PHP SYNTAX VALIDATION
log_diagnostic("2. PERFORMING PHP SYNTAX VALIDATION", "TEST");

foreach ($critical_files as $name => $path) {
    if (file_exists($path)) {
        $output = [];
        $return_code = 0;
        exec("php -l \"$path\" 2>&1", $output, $return_code);
        
        if ($return_code === 0) {
            log_diagnostic("   ✓ $name - SYNTAX OK");
        } else {
            log_diagnostic("   ✗ $name - SYNTAX ERROR:", "ERROR");
            foreach ($output as $line) {
                log_diagnostic("     $line", "ERROR");
            }
        }
    }
}

// 3. PHP EXTENSION CHECK
log_diagnostic("3. CHECKING REQUIRED PHP EXTENSIONS", "TEST");

$required_extensions = [
    'odbc' => 'Database connectivity',
    'session' => 'Session management',
    'mbstring' => 'Multi-byte string functions',
    'json' => 'JSON handling',
    'openssl' => 'Cryptographic functions',
    'hash' => 'Hash functions'
];

foreach ($required_extensions as $ext => $description) {
    if (extension_loaded($ext)) {
        log_diagnostic("   ✓ $ext - LOADED ($description)");
    } else {
        log_diagnostic("   ✗ $ext - MISSING ($description)", "ERROR");
    }
}

// 4. CONFIGURATION VALIDATION
log_diagnostic("4. VALIDATING SYSTEM CONFIGURATION", "TEST");

// Test config.php loading
try {
    ob_start();
    include __DIR__ . '/config.php';
    $config_output = ob_get_clean();
    
    if (!empty($config_output)) {
        log_diagnostic("   ! config.php produces output (potential issue)", "WARNING");
        log_diagnostic("     Output: " . substr($config_output, 0, 200), "WARNING");
    } else {
        log_diagnostic("   ✓ config.php loads without output");
    }
    
    // Check if session is started
    if (session_status() === PHP_SESSION_ACTIVE) {
        log_diagnostic("   ✓ Session successfully started");
    } else {
        log_diagnostic("   ✗ Session not started", "ERROR");
    }
    
} catch (Exception $e) {
    log_diagnostic("   ✗ config.php loading error: " . $e->getMessage(), "ERROR");
} catch (ParseError $e) {
    log_diagnostic("   ✗ config.php parse error: " . $e->getMessage(), "ERROR");
}

// 5. DATABASE CONNECTIVITY TEST
log_diagnostic("5. TESTING DATABASE CONNECTIVITY", "TEST");

try {
    // Suppress output during database test
    ob_start();
    
    if (file_exists(__DIR__ . '/src/dbOpen.php')) {
        include_once __DIR__ . '/src/dbOpen.php';
        
        if (isset($db) && $db !== false) {
            log_diagnostic("   ✓ Database connection established");
            
            // Test database query
            if (function_exists('odbc_exec')) {
                $test_query = odbc_exec($db, "SELECT 1 as test_column");
                if ($test_query) {
                    log_diagnostic("   ✓ Database query test successful");
                    odbc_free_result($test_query);
                } else {
                    log_diagnostic("   ! Database query test failed: " . odbc_errormsg($db), "WARNING");
                }
            }
        } else {
            log_diagnostic("   ✗ Database connection failed", "ERROR");
            if (defined('ENV') && ENV == 'development') {
                log_diagnostic("     Check ODBC DSN configuration", "ERROR");
            }
        }
    } else {
        log_diagnostic("   ✗ Database configuration file missing", "ERROR");
    }
    
    $db_output = ob_get_clean();
    if (!empty($db_output)) {
        log_diagnostic("   ! Database connection produces output:", "WARNING");
        log_diagnostic("     " . substr($db_output, 0, 200), "WARNING");
    }
    
} catch (Exception $e) {
    ob_get_clean(); // Clean up output buffer
    log_diagnostic("   ✗ Database connection exception: " . $e->getMessage(), "ERROR");
}

// 6. SESSION FUNCTIONALITY TEST
log_diagnostic("6. TESTING SESSION FUNCTIONALITY", "TEST");

try {
    if (session_status() === PHP_SESSION_ACTIVE) {
        // Test session write
        $_SESSION['diagnostic_test'] = 'test_value_' . time();
        log_diagnostic("   ✓ Session write test successful");
        
        // Test session read
        if (isset($_SESSION['diagnostic_test'])) {
            log_diagnostic("   ✓ Session read test successful");
            unset($_SESSION['diagnostic_test']); // Clean up
        } else {
            log_diagnostic("   ✗ Session read test failed", "ERROR");
        }
    } else {
        log_diagnostic("   ✗ Session not active for testing", "ERROR");
    }
} catch (Exception $e) {
    log_diagnostic("   ✗ Session test exception: " . $e->getMessage(), "ERROR");
}

// 7. SECURITY FUNCTIONS TEST
log_diagnostic("7. TESTING SECURITY FUNCTIONS", "TEST");

try {
    if (file_exists(__DIR__ . '/security_functions.php')) {
        include_once __DIR__ . '/security_functions.php';
        
        // Test CSRF token generation
        if (function_exists('generate_csrf_token')) {
            $token = generate_csrf_token();
            if (!empty($token)) {
                log_diagnostic("   ✓ CSRF token generation working");
            } else {
                log_diagnostic("   ✗ CSRF token generation failed", "ERROR");
            }
        } else {
            log_diagnostic("   ✗ CSRF functions not available", "ERROR");
        }
        
        // Test safe_output function
        if (function_exists('safe_output')) {
            $test_output = safe_output('<script>alert("test")</script>');
            if (strpos($test_output, '<script>') === false) {
                log_diagnostic("   ✓ XSS protection working");
            } else {
                log_diagnostic("   ✗ XSS protection failed", "ERROR");
            }
        } else {
            log_diagnostic("   ✗ XSS protection functions not available", "ERROR");
        }
        
    } else {
        log_diagnostic("   ✗ Security functions file missing", "ERROR");
    }
} catch (Exception $e) {
    log_diagnostic("   ✗ Security functions test exception: " . $e->getMessage(), "ERROR");
}

// 8. FILE PERMISSIONS CHECK
log_diagnostic("8. CHECKING DIRECTORY PERMISSIONS", "TEST");

$directories_to_check = [
    'logs' => __DIR__ . '/logs',
    'src/download' => __DIR__ . '/src/download',
    'logs/security' => __DIR__ . '/logs/security'
];

foreach ($directories_to_check as $name => $path) {
    if (is_dir($path)) {
        if (is_writable($path)) {
            log_diagnostic("   ✓ $name - WRITABLE");
        } else {
            log_diagnostic("   ✗ $name - NOT WRITABLE", "ERROR");
        }
    } else {
        log_diagnostic("   ! $name - DOES NOT EXIST (will be created if needed)", "WARNING");
    }
}

// 9. MEMORY AND RESOURCE CHECK
log_diagnostic("9. CHECKING SYSTEM RESOURCES", "TEST");

$memory_limit = ini_get('memory_limit');
$max_execution_time = ini_get('max_execution_time');
$upload_max_filesize = ini_get('upload_max_filesize');

log_diagnostic("   Memory Limit: $memory_limit");
log_diagnostic("   Max Execution Time: $max_execution_time seconds");
log_diagnostic("   Upload Max Filesize: $upload_max_filesize");

// 10. ERROR LOG ANALYSIS
log_diagnostic("10. CHECKING ERROR LOGS", "TEST");

$error_log_paths = [
    ini_get('error_log'),
    __DIR__ . '/logs/php_errors.log',
    '/var/log/apache2/error.log',
    '/var/log/nginx/error.log'
];

foreach ($error_log_paths as $log_path) {
    if ($log_path && file_exists($log_path) && is_readable($log_path)) {
        $recent_errors = tail_file($log_path, 10);
        if (!empty($recent_errors)) {
            log_diagnostic("   Error log found at: $log_path", "WARNING");
            log_diagnostic("   Recent entries:", "WARNING");
            foreach ($recent_errors as $error_line) {
                log_diagnostic("     " . trim($error_line), "WARNING");
            }
        }
    }
}

// Helper function to read last N lines of a file
function tail_file($filename, $lines = 10) {
    if (!file_exists($filename) || !is_readable($filename)) {
        return [];
    }
    
    $handle = fopen($filename, "r");
    if (!$handle) {
        return [];
    }
    
    $result = [];
    $buffer = 4096;
    $pos = -1;
    $line_count = 0;
    
    fseek($handle, $pos, SEEK_END);
    
    while ($line_count < $lines) {
        $char = fgetc($handle);
        if ($char === "\n") {
            $line_count++;
        }
        
        $pos--;
        if (fseek($handle, $pos, SEEK_END) === -1) {
            break;
        }
    }
    
    while (($line = fgets($handle)) !== false) {
        $result[] = $line;
    }
    
    fclose($handle);
    return array_slice($result, -$lines);
}

// FINAL DIAGNOSTIC SUMMARY
log_diagnostic("=== DIAGNOSTIC SUMMARY ===");

// Count issues
$log_content = file_get_contents($diagnostic_log);
$error_count = substr_count($log_content, '[ERROR]');
$warning_count = substr_count($log_content, '[WARNING]');

log_diagnostic("Errors Found: $error_count");
log_diagnostic("Warnings Found: $warning_count");

if ($error_count > 0) {
    log_diagnostic("SYSTEM STATUS: CRITICAL ISSUES DETECTED", "ERROR");
    log_diagnostic("Action Required: Review errors above and fix critical issues", "ERROR");
} elseif ($warning_count > 0) {
    log_diagnostic("SYSTEM STATUS: MINOR ISSUES DETECTED", "WARNING");
    log_diagnostic("Action Recommended: Review warnings above", "WARNING");
} else {
    log_diagnostic("SYSTEM STATUS: HEALTHY");
    log_diagnostic("All critical systems appear to be functioning correctly");
}

log_diagnostic("=== DIAGNOSTIC COMPLETE ===");
log_diagnostic("Full diagnostic log saved to: $diagnostic_log");

// Provide next steps
log_diagnostic("");
log_diagnostic("NEXT STEPS:");
log_diagnostic("1. If errors were found, run specific diagnostic scripts:");
log_diagnostic("   - database_test.php for database issues");
log_diagnostic("   - syntax_checker.php for code issues");
log_diagnostic("   - session_test.php for session issues");
log_diagnostic("2. Check the detailed log file: $diagnostic_log");
log_diagnostic("3. If errors persist, contact system administrator");

?>