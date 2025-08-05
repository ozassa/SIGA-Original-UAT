<?php
/**
 * SIGA Master Diagnostic Runner
 * 
 * Executes all diagnostic scripts in sequence and provides a comprehensive
 * system health report. This is the main entry point for diagnosing SIGA issues.
 * 
 * SAFE FOR PRODUCTION - Only performs diagnostic tests
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$master_log = __DIR__ . '/logs/master_diagnostic_' . date('Y-m-d_H-i-s') . '.log';
if (!is_dir(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0755, true);
}

function log_master($message, $level = 'INFO') {
    global $master_log;
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] [$level] $message" . PHP_EOL;
    file_put_contents($master_log, $log_entry, FILE_APPEND | LOCK_EX);
    echo $log_entry;
}

function run_diagnostic_script($script_name, $description) {
    log_master("=== RUNNING: $description ===", "TEST");
    log_master("Script: $script_name");
    
    $script_path = __DIR__ . '/' . $script_name;
    
    if (!file_exists($script_path)) {
        log_master("ERROR: Script file not found: $script_path", "ERROR");
        return false;
    }
    
    $start_time = microtime(true);
    $start_memory = memory_get_usage(true);
    
    try {
        // Capture output from diagnostic script
        ob_start();
        $result = include $script_path;
        $output = ob_get_clean();
        
        $end_time = microtime(true);
        $end_memory = memory_get_usage(true);
        
        $execution_time = round($end_time - $start_time, 3);
        $memory_used = $end_memory - $start_memory;
        
        log_master("✓ Script completed successfully");
        log_master("Execution time: {$execution_time} seconds");
        log_master("Memory used: " . format_bytes($memory_used));
        
        // Count errors and warnings in output
        $error_count = substr_count($output, '[ERROR]');
        $warning_count = substr_count($output, '[WARNING]');
        
        log_master("Errors found: $error_count");
        log_master("Warnings found: $warning_count");
        
        if ($error_count > 0) {
            log_master("STATUS: ERRORS DETECTED", "ERROR");
        } elseif ($warning_count > 0) {
            log_master("STATUS: WARNINGS DETECTED", "WARNING");
        } else {
            log_master("STATUS: CLEAN");
        }
        
        return [
            'success' => true,
            'execution_time' => $execution_time,
            'memory_used' => $memory_used,
            'errors' => $error_count,
            'warnings' => $warning_count,
            'output' => $output
        ];
        
    } catch (Exception $e) {
        log_master("ERROR: Exception during script execution: " . $e->getMessage(), "ERROR");
        ob_end_clean();
        return false;
    } catch (Error $e) {
        log_master("ERROR: Fatal error during script execution: " . $e->getMessage(), "ERROR");
        ob_end_clean();
        return false;
    }
}

function format_bytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

// Start master diagnostic session
log_master("================================");
log_master("SIGA COMPREHENSIVE DIAGNOSTIC SESSION");
log_master("Started: " . date('Y-m-d H:i:s'));
log_master("PHP Version: " . PHP_VERSION);
log_master("Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'));
log_master("================================");
log_master("");

// Define diagnostic scripts to run
$diagnostic_scripts = [
    'system_diagnostic.php' => 'System Health Check',
    'syntax_checker.php' => 'PHP Syntax Validation',
    'database_test.php' => 'Database Connectivity Test',
    'session_test.php' => 'Session Functionality Test',
    'error_debug.php' => 'Enhanced Error Debugging'
];

$results = [];
$total_start_time = microtime(true);

// Run each diagnostic script
foreach ($diagnostic_scripts as $script => $description) {
    log_master("");
    $result = run_diagnostic_script($script, $description);
    $results[$script] = $result;
    log_master("");
    
    // Add delay between scripts to prevent resource conflicts
    usleep(500000); // 0.5 second delay
}

$total_end_time = microtime(true);
$total_execution_time = round($total_end_time - $total_start_time, 3);

// Generate comprehensive summary
log_master("================================");
log_master("COMPREHENSIVE DIAGNOSTIC SUMMARY");
log_master("================================");

$summary_stats = [
    'total_scripts' => count($diagnostic_scripts),
    'successful_scripts' => 0,
    'failed_scripts' => 0,
    'total_errors' => 0,
    'total_warnings' => 0,
    'total_execution_time' => $total_execution_time
];

log_master("Total diagnostic scripts: " . $summary_stats['total_scripts']);
log_master("Total execution time: {$total_execution_time} seconds");
log_master("");

// Analyze results
foreach ($results as $script => $result) {
    if ($result === false) {
        $summary_stats['failed_scripts']++;
        log_master("✗ $script - FAILED TO EXECUTE", "ERROR");
    } else {
        $summary_stats['successful_scripts']++;
        $summary_stats['total_errors'] += $result['errors'];
        $summary_stats['total_warnings'] += $result['warnings'];
        
        $status = "CLEAN";
        $level = "INFO";
        if ($result['errors'] > 0) {
            $status = "ERRORS (" . $result['errors'] . ")";
            $level = "ERROR";
        } elseif ($result['warnings'] > 0) {
            $status = "WARNINGS (" . $result['warnings'] . ")";
            $level = "WARNING";
        }
        
        log_master("✓ $script - $status", $level);
        log_master("  Execution time: {$result['execution_time']}s, Memory: " . format_bytes($result['memory_used']));
    }
}

log_master("");
log_master("OVERALL STATISTICS:");
log_master("Successful scripts: " . $summary_stats['successful_scripts']);
log_master("Failed scripts: " . $summary_stats['failed_scripts']);
log_master("Total errors found: " . $summary_stats['total_errors']);
log_master("Total warnings found: " . $summary_stats['total_warnings']);
log_master("");

// Determine overall system status
if ($summary_stats['failed_scripts'] > 0 || $summary_stats['total_errors'] > 0) {
    log_master("OVERALL SYSTEM STATUS: CRITICAL ISSUES DETECTED", "ERROR");
    log_master("", "ERROR");
    log_master("IMMEDIATE ACTION REQUIRED:", "ERROR");
    log_master("1. Review individual diagnostic logs for detailed error information", "ERROR");
    log_master("2. Focus on fixing CRITICAL and ERROR level issues first", "ERROR");
    log_master("3. Address the following priority order:", "ERROR");
    log_master("   a) PHP syntax errors (run syntax_checker.php output)", "ERROR");
    log_master("   b) Database connectivity issues (run database_test.php output)", "ERROR");
    log_master("   c) Session configuration problems (run session_test.php output)", "ERROR");
    log_master("   d) Missing dependencies and file permissions", "ERROR");
    log_master("4. Re-run diagnostics after each fix to verify resolution", "ERROR");
    log_master("5. Contact system administrator if issues persist", "ERROR");
    
} elseif ($summary_stats['total_warnings'] > 0) {
    log_master("OVERALL SYSTEM STATUS: MINOR ISSUES DETECTED", "WARNING");
    log_master("System can function but performance or security could be improved", "WARNING");
    log_master("Review warnings in individual diagnostic logs for optimization opportunities", "WARNING");
    
} else {
    log_master("OVERALL SYSTEM STATUS: HEALTHY", "SUCCESS");
    log_master("All diagnostic tests passed successfully");
    log_master("System appears to be functioning correctly");
}

log_master("");
log_master("DIAGNOSTIC LOG FILES CREATED:");
$log_files = glob(__DIR__ . '/logs/*_' . date('Y-m-d') . '_*.log');
foreach ($log_files as $log_file) {
    $file_size = filesize($log_file);
    $relative_path = str_replace(__DIR__ . '/', '', $log_file);
    log_master("- $relative_path (" . format_bytes($file_size) . ")");
}

log_master("");
log_master("NEXT STEPS:");
log_master("1. Review this master diagnostic log: $master_log");
log_master("2. If errors were found, check individual diagnostic logs for details");
log_master("3. Fix issues in priority order (Critical -> Error -> Warning)");
log_master("4. Re-run specific diagnostic scripts to verify fixes");
log_master("5. Run this master diagnostic again after making changes");

log_master("");
log_master("SUPPORT INFORMATION:");
log_master("- All diagnostic logs are saved in the /logs/ directory");
log_master("- Logs are timestamped and safe to share with support teams");
log_master("- No sensitive data (passwords, etc.) is logged");
log_master("- Diagnostics are read-only and safe for production environments");

log_master("");
log_master("================================");
log_master("DIAGNOSTIC SESSION COMPLETED");
log_master("Completed: " . date('Y-m-d H:i:s'));
log_master("Master log: $master_log");
log_master("================================");

// If running from web browser, provide HTML output
if (php_sapi_name() !== 'cli' && !headers_sent()) {
    echo "<br><br><strong>Diagnostic session completed!</strong><br>";
    echo "Check the logs directory for detailed reports.<br>";
    echo "Master log: " . basename($master_log) . "<br>";
    
    if ($summary_stats['total_errors'] > 0) {
        echo "<br><span style='color: red; font-weight: bold;'>CRITICAL ISSUES FOUND - IMMEDIATE ACTION REQUIRED</span><br>";
    } elseif ($summary_stats['total_warnings'] > 0) {
        echo "<br><span style='color: orange; font-weight: bold;'>WARNINGS FOUND - REVIEW RECOMMENDED</span><br>";
    } else {
        echo "<br><span style='color: green; font-weight: bold;'>SYSTEM APPEARS HEALTHY</span><br>";
    }
}

?>