<?php
/**
 * SIGA Enhanced Error Reporting and Debugging Tool
 * 
 * Provides comprehensive error reporting, logging, and debugging capabilities
 * to identify and resolve system issues. Creates detailed error trails.
 * 
 * SAFE FOR PRODUCTION - Only logs and reports, no system modifications
 */

// Enable comprehensive error reporting for debugging
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);

$debug_log = __DIR__ . '/logs/error_debug_' . date('Y-m-d_H-i-s') . '.log';
if (!is_dir(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0755, true);
}

// Enhanced logging function
function log_debug($message, $level = 'INFO', $context = []) {
    global $debug_log;
    $timestamp = date('Y-m-d H:i:s');
    $memory_usage = memory_get_usage(true);
    $memory_peak = memory_get_peak_usage(true);
    
    $log_entry = [
        'timestamp' => $timestamp,
        'level' => $level,
        'message' => $message,
        'memory_current' => $memory_usage,
        'memory_peak' => $memory_peak,
        'context' => $context
    ];
    
    $formatted_entry = "[$timestamp] [$level] $message";
    if (!empty($context)) {
        $formatted_entry .= " | Context: " . json_encode($context, JSON_UNESCAPED_SLASHES);
    }
    $formatted_entry .= " | Memory: " . format_bytes($memory_usage) . " (Peak: " . format_bytes($memory_peak) . ")" . PHP_EOL;
    
    file_put_contents($debug_log, $formatted_entry, FILE_APPEND | LOCK_EX);
    echo $formatted_entry;
}

function format_bytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

// Custom error handler for detailed error capture
function custom_error_handler($errno, $errstr, $errfile, $errline) {
    $error_types = [
        E_ERROR => 'ERROR',
        E_WARNING => 'WARNING', 
        E_PARSE => 'PARSE',
        E_NOTICE => 'NOTICE',
        E_CORE_ERROR => 'CORE_ERROR',
        E_CORE_WARNING => 'CORE_WARNING',
        E_COMPILE_ERROR => 'COMPILE_ERROR',
        E_COMPILE_WARNING => 'COMPILE_WARNING',
        E_USER_ERROR => 'USER_ERROR',
        E_USER_WARNING => 'USER_WARNING',
        E_USER_NOTICE => 'USER_NOTICE',
        E_STRICT => 'STRICT',
        E_RECOVERABLE_ERROR => 'RECOVERABLE_ERROR',
        E_DEPRECATED => 'DEPRECATED',
        E_USER_DEPRECATED => 'USER_DEPRECATED'
    ];
    
    $error_type = $error_types[$errno] ?? 'UNKNOWN';
    
    log_debug("PHP Error [$error_type]: $errstr", 'ERROR', [
        'file' => $errfile,
        'line' => $errline,
        'errno' => $errno,
        'backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5)
    ]);
    
    return false; // Let default handler run too
}

// Custom exception handler
function custom_exception_handler($exception) {
    log_debug("Uncaught Exception: " . $exception->getMessage(), 'CRITICAL', [
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'class' => get_class($exception),
        'trace' => $exception->getTraceAsString()
    ]);
}

// Set custom handlers
set_error_handler('custom_error_handler');
set_exception_handler('custom_exception_handler');

log_debug("=== SIGA ENHANCED ERROR DEBUGGING SESSION ===");
log_debug("PHP Version: " . PHP_VERSION);
log_debug("Server API: " . php_sapi_name());
log_debug("Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'));

// 1. SYSTEM INFORMATION GATHERING
log_debug("1. GATHERING SYSTEM INFORMATION", "TEST");

$system_info = [
    'php_version' => PHP_VERSION,
    'server_api' => php_sapi_name(),
    'operating_system' => PHP_OS,
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
    'script_filename' => $_SERVER['SCRIPT_FILENAME'] ?? 'Unknown',
    'request_uri' => $_SERVER['REQUEST_URI'] ?? 'Unknown',
    'http_host' => $_SERVER['HTTP_HOST'] ?? 'Unknown',
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
];

foreach ($system_info as $key => $value) {
    log_debug("   $key: $value");
}

// 2. PHP CONFIGURATION ANALYSIS
log_debug("2. ANALYZING PHP CONFIGURATION", "TEST");

$critical_settings = [
    'memory_limit' => ini_get('memory_limit'),
    'max_execution_time' => ini_get('max_execution_time'),
    'max_input_time' => ini_get('max_input_time'),
    'post_max_size' => ini_get('post_max_size'),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'display_errors' => ini_get('display_errors'),
    'log_errors' => ini_get('log_errors'),
    'error_log' => ini_get('error_log'),
    'include_path' => ini_get('include_path'),
    'auto_prepend_file' => ini_get('auto_prepend_file'),
    'auto_append_file' => ini_get('auto_append_file')
];

log_debug("   Critical PHP Settings:");
foreach ($critical_settings as $setting => $value) {
    log_debug("     $setting: " . ($value ?: 'Not set'));
}

// 3. LOADED EXTENSIONS CHECK
log_debug("3. CHECKING LOADED EXTENSIONS", "TEST");

$loaded_extensions = get_loaded_extensions();
log_debug("   Total loaded extensions: " . count($loaded_extensions));

$critical_extensions = ['odbc', 'session', 'mbstring', 'json', 'openssl', 'hash', 'curl', 'gd'];
foreach ($critical_extensions as $ext) {
    if (in_array($ext, $loaded_extensions)) {
        log_debug("   ✓ $ext - Loaded");
    } else {
        log_debug("   ✗ $ext - NOT LOADED", "ERROR");
    }
}

// 4. FILE INCLUSION TESTING
log_debug("4. TESTING CRITICAL FILE INCLUSIONS", "TEST");

$include_files = [
    'config.php',
    'session_config.php', 
    'security_functions.php',
    'src/dbOpen.php',
    'src/role/rolePrefix.php'
];

foreach ($include_files as $file) {
    $full_path = __DIR__ . '/' . $file;
    log_debug("   Testing inclusion of: $file");
    
    if (!file_exists($full_path)) {
        log_debug("     ✗ File does not exist", "ERROR");
        continue;
    }
    
    if (!is_readable($full_path)) {
        log_debug("     ✗ File is not readable", "ERROR");
        continue;
    }
    
    try {
        $before_vars = get_defined_vars();
        $before_functions = get_defined_functions()['user'];
        $before_classes = get_declared_classes();
        
        ob_start();
        $include_result = include_once $full_path;
        $output = ob_get_clean();
        
        $after_vars = get_defined_vars();
        $after_functions = get_defined_functions()['user'];
        $after_classes = get_declared_classes();
        
        log_debug("     ✓ File included successfully");
        
        if (!empty($output)) {
            log_debug("     ! File produces output: " . substr($output, 0, 100), "WARNING");
        }
        
        $new_functions = array_diff($after_functions, $before_functions);
        if (!empty($new_functions)) {
            log_debug("     Functions defined: " . implode(', ', array_slice($new_functions, 0, 5)));
        }
        
        $new_classes = array_diff($after_classes, $before_classes);
        if (!empty($new_classes)) {
            log_debug("     Classes defined: " . implode(', ', $new_classes));
        }
        
    } catch (Exception $e) {
        log_debug("     ✗ Exception during inclusion: " . $e->getMessage(), "ERROR");
    } catch (Error $e) {
        log_debug("     ✗ Fatal error during inclusion: " . $e->getMessage(), "ERROR");
    }
}

// 5. ERROR LOG ANALYSIS
log_debug("5. ANALYZING EXISTING ERROR LOGS", "TEST");

$log_locations = [
    'System error log' => ini_get('error_log'),
    'Local PHP errors' => __DIR__ . '/logs/php_errors.log',
    'Apache error log' => '/var/log/apache2/error.log',
    'Nginx error log' => '/var/log/nginx/error.log',
    'System log' => '/var/log/syslog'
];

foreach ($log_locations as $log_name => $log_path) {
    if (empty($log_path)) {
        log_debug("   $log_name: Not configured");
        continue;
    }
    
    if (!file_exists($log_path)) {
        log_debug("   $log_name: File does not exist ($log_path)");
        continue;
    }
    
    if (!is_readable($log_path)) {
        log_debug("   $log_name: File is not readable ($log_path)", "WARNING");
        continue;
    }
    
    $file_size = filesize($log_path);
    $mod_time = filemtime($log_path);
    
    log_debug("   $log_name: Found");
    log_debug("     Path: $log_path");
    log_debug("     Size: " . format_bytes($file_size));
    log_debug("     Modified: " . date('Y-m-d H:i:s', $mod_time));
    
    // Read recent entries
    if ($file_size > 0 && $file_size < 10 * 1024 * 1024) { // Less than 10MB
        $recent_lines = tail_file($log_path, 20);
        if (!empty($recent_lines)) {
            log_debug("     Recent entries:", "INFO");
            foreach (array_slice($recent_lines, -5) as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    log_debug("       " . substr($line, 0, 150));
                }
            }
        }
    }
}

// 6. DATABASE CONNECTION ERROR TESTING
log_debug("6. TESTING DATABASE CONNECTION WITH ERROR CAPTURE", "TEST");

try {
    if (file_exists(__DIR__ . '/src/dbOpen.php')) {
        ob_start();
        
        // Capture all errors during database connection
        $connection_errors = [];
        set_error_handler(function($errno, $errstr, $errfile, $errline) use (&$connection_errors) {
            $connection_errors[] = "[$errno] $errstr in $errfile:$errline";
            return false;
        });
        
        include_once __DIR__ . '/src/dbOpen.php';
        
        restore_error_handler();
        $db_output = ob_get_clean();
        
        if (!empty($connection_errors)) {
            log_debug("   Database connection errors:", "ERROR");
            foreach ($connection_errors as $error) {
                log_debug("     $error", "ERROR");
            }
        }
        
        if (!empty($db_output)) {
            log_debug("   Database connection output:", "WARNING");
            log_debug("     " . substr($db_output, 0, 300), "WARNING");
        }
        
        if (isset($db) && $db !== false) {
            log_debug("   ✓ Database connection variable is set");
            
            // Test a simple query with error capture
            try {
                $test_result = @odbc_exec($db, "SELECT 1");
                if ($test_result) {
                    log_debug("   ✓ Database query test successful");
                    @odbc_free_result($test_result);
                } else {
                    $db_error = @odbc_errormsg($db);
                    log_debug("   ✗ Database query failed: $db_error", "ERROR");
                }
            } catch (Exception $e) {
                log_debug("   ✗ Database query exception: " . $e->getMessage(), "ERROR");
            }
        } else {
            log_debug("   ✗ Database connection variable is not set or false", "ERROR");
        }
        
    } else {
        log_debug("   ✗ Database configuration file not found", "ERROR");
    }
} catch (Exception $e) {
    log_debug("   ✗ Database connection exception: " . $e->getMessage(), "ERROR");
}

// 7. ACCESS CONTROL TESTING
log_debug("7. TESTING ACCESS CONTROL WITH ERROR CAPTURE", "TEST");

$access_file = __DIR__ . '/src/role/access/Access.php';
if (file_exists($access_file)) {
    try {
        // Read file content to analyze structure
        $access_content = file_get_contents($access_file);
        $access_lines = file($access_file);
        
        log_debug("   Access.php file size: " . strlen($access_content) . " bytes");
        log_debug("   Access.php line count: " . count($access_lines));
        
        // Look for potential issues in Access.php
        if (strpos($access_content, '<?php') === false) {
            log_debug("   ! No PHP opening tag found", "WARNING");
        }
        
        // Check for syntax without executing
        $syntax_check = exec("php -l " . escapeshellarg($access_file) . " 2>&1", $syntax_output, $syntax_return);
        if ($syntax_return === 0) {
            log_debug("   ✓ Access.php syntax is valid");
        } else {
            log_debug("   ✗ Access.php syntax errors:", "ERROR");
            foreach ($syntax_output as $error_line) {
                log_debug("     $error_line", "ERROR");
            }
        }
        
        // Test include without executing main logic
        ob_start();
        $access_errors = [];
        set_error_handler(function($errno, $errstr, $errfile, $errline) use (&$access_errors) {
            $access_errors[] = "[$errno] $errstr in $errfile:$errline";
            return false;
        });
        
        // Temporarily modify $_GET to avoid execution
        $old_get = $_GET;
        $_GET = [];
        
        try {
            include_once $access_file;
        } catch (Exception $e) {
            log_debug("   Exception including Access.php: " . $e->getMessage(), "ERROR");
        }
        
        $_GET = $old_get;
        restore_error_handler();
        $access_output = ob_get_clean();
        
        if (!empty($access_errors)) {
            log_debug("   Access.php include errors:", "ERROR");
            foreach ($access_errors as $error) {
                log_debug("     $error", "ERROR");
            }
        }
        
        if (!empty($access_output)) {
            log_debug("   Access.php produces output during include:", "WARNING");
            log_debug("     " . substr($access_output, 0, 200), "WARNING");
        }
        
    } catch (Exception $e) {
        log_debug("   ✗ Error analyzing Access.php: " . $e->getMessage(), "ERROR");
    }
} else {
    log_debug("   ✗ Access.php file not found", "ERROR");
}

// 8. SECURITY FUNCTIONS ERROR TESTING
log_debug("8. TESTING SECURITY FUNCTIONS WITH ERROR CAPTURE", "TEST");

if (file_exists(__DIR__ . '/security_functions.php')) {
    try {
        ob_start();
        $security_errors = [];
        set_error_handler(function($errno, $errstr, $errfile, $errline) use (&$security_errors) {
            $security_errors[] = "[$errno] $errstr in $errfile:$errline";
            return false;
        });
        
        include_once __DIR__ . '/security_functions.php';
        
        restore_error_handler();
        $security_output = ob_get_clean();
        
        if (!empty($security_errors)) {
            log_debug("   Security functions errors:", "ERROR");
            foreach ($security_errors as $error) {
                log_debug("     $error", "ERROR");
            }
        } else {
            log_debug("   ✓ Security functions loaded without errors");
        }
        
        if (!empty($security_output)) {
            log_debug("   ! Security functions produce output:", "WARNING");
            log_debug("     " . substr($security_output, 0, 200), "WARNING");
        }
        
        // Test critical security functions
        $security_functions_to_test = [
            'generate_csrf_token',
            'safe_output',
            'validate_input',
            'set_security_headers'
        ];
        
        foreach ($security_functions_to_test as $func) {
            if (function_exists($func)) {
                log_debug("   ✓ $func is available");
                
                // Test function execution
                try {
                    switch ($func) {
                        case 'generate_csrf_token':
                            $token = generate_csrf_token();
                            log_debug("     Token generated: " . (empty($token) ? 'FAILED' : 'SUCCESS'));
                            break;
                        case 'safe_output':
                            $safe = safe_output('<script>test</script>');
                            log_debug("     XSS protection: " . (strpos($safe, '<script>') === false ? 'SUCCESS' : 'FAILED'));
                            break;
                    }
                } catch (Exception $e) {
                    log_debug("     Function test failed: " . $e->getMessage(), "ERROR");
                }
            } else {
                log_debug("   ✗ $func is not available", "ERROR");
            }
        }
        
    } catch (Exception $e) {
        log_debug("   ✗ Security functions test exception: " . $e->getMessage(), "ERROR");
    }
}

// Helper function to read last N lines of a file
function tail_file($filename, $lines = 10) {
    if (!file_exists($filename) || !is_readable($filename)) {
        return [];
    }
    
    $file = fopen($filename, 'r');
    if (!$file) {
        return [];
    }
    
    $result = [];
    $pos = -2;
    $line_count = 0;
    
    fseek($file, $pos, SEEK_END);
    
    while ($line_count < $lines) {
        $char = fgetc($file);
        if ($char === "\n") {
            $line_count++;
        }
        $pos--;
        if (fseek($file, $pos, SEEK_END) === -1) {
            break;
        }
    }
    
    while (!feof($file)) {
        $result[] = fgets($file);
    }
    
    fclose($file);
    return array_slice($result, -$lines);
}

// 9. MEMORY USAGE ANALYSIS
log_debug("9. ANALYZING MEMORY USAGE", "TEST");

$memory_info = [
    'current_usage' => memory_get_usage(true),
    'peak_usage' => memory_get_peak_usage(true),
    'limit' => ini_get('memory_limit'),
    'available' => null
];

// Convert memory limit to bytes
$limit_str = $memory_info['limit'];
$limit_bytes = 0;
if (preg_match('/^(\d+)(.)$/', $limit_str, $matches)) {
    $number = (int)$matches[1];
    $unit = strtoupper($matches[2]);
    switch ($unit) {
        case 'G': $limit_bytes = $number * 1024 * 1024 * 1024; break;
        case 'M': $limit_bytes = $number * 1024 * 1024; break;
        case 'K': $limit_bytes = $number * 1024; break;
        default: $limit_bytes = $number;
    }
}

$memory_info['available'] = $limit_bytes - $memory_info['current_usage'];

log_debug("   Memory Analysis:");
log_debug("     Current Usage: " . format_bytes($memory_info['current_usage']));
log_debug("     Peak Usage: " . format_bytes($memory_info['peak_usage']));
log_debug("     Memory Limit: $limit_str");
log_debug("     Available: " . format_bytes($memory_info['available']));

$usage_percentage = ($memory_info['current_usage'] / $limit_bytes) * 100;
log_debug("     Usage Percentage: " . round($usage_percentage, 2) . "%");

if ($usage_percentage > 80) {
    log_debug("   ! High memory usage detected", "WARNING");
} else {
    log_debug("   ✓ Memory usage is within acceptable limits");
}

// FINAL ERROR SUMMARY AND RECOMMENDATIONS
log_debug("=== ERROR DEBUGGING SUMMARY ===");

$log_content = file_get_contents($debug_log);
$error_count = substr_count($log_content, '[ERROR]');
$warning_count = substr_count($log_content, '[WARNING]');
$critical_count = substr_count($log_content, '[CRITICAL]');

log_debug("Critical Issues: $critical_count");
log_debug("Errors Found: $error_count");
log_debug("Warnings Found: $warning_count");

if ($critical_count > 0 || $error_count > 0) {
    log_debug("SYSTEM STATUS: CRITICAL ERRORS DETECTED", "ERROR");
    log_debug("");
    log_debug("IMMEDIATE ACTION REQUIRED:", "ERROR");
    log_debug("1. Review the detailed error log: $debug_log");
    log_debug("2. Fix syntax errors in PHP files");
    log_debug("3. Resolve database connectivity issues");
    log_debug("4. Check file permissions and paths");
    log_debug("5. Verify required PHP extensions are installed");
    log_debug("6. Contact system administrator if needed");
} elseif ($warning_count > 0) {
    log_debug("SYSTEM STATUS: WARNINGS DETECTED", "WARNING");
    log_debug("System may function but performance/security could be improved");
} else {
    log_debug("SYSTEM STATUS: CLEAN");
    log_debug("No critical errors detected in debugging session");
}

log_debug("");
log_debug("Complete debugging log saved to: $debug_log");
log_debug("Log file size: " . format_bytes(filesize($debug_log)));

// Restore default error handlers
restore_error_handler();
restore_exception_handler();

?>