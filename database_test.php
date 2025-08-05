<?php
/**
 * SIGA Database Connectivity Diagnostic Tool
 * 
 * Specifically tests database connectivity and ODBC functionality
 * to identify exact database-related issues.
 * 
 * SAFE FOR PRODUCTION - Only performs read-only tests
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$test_log = __DIR__ . '/logs/database_test_' . date('Y-m-d_H-i-s') . '.log';
if (!is_dir(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0755, true);
}

function log_db_test($message, $level = 'INFO') {
    global $test_log;
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] [$level] $message" . PHP_EOL;
    file_put_contents($test_log, $log_entry, FILE_APPEND | LOCK_EX);
    echo $log_entry;
}

log_db_test("=== SIGA DATABASE CONNECTIVITY TEST ===");

// 1. CHECK ODBC EXTENSION
log_db_test("1. CHECKING ODBC EXTENSION", "TEST");

if (extension_loaded('odbc')) {
    log_db_test("   ✓ ODBC extension is loaded");
    
    // List available ODBC functions
    $odbc_functions = get_extension_funcs('odbc');
    log_db_test("   Available ODBC functions: " . count($odbc_functions));
    
    $critical_functions = ['odbc_connect', 'odbc_exec', 'odbc_fetch_row', 'odbc_errormsg'];
    foreach ($critical_functions as $func) {
        if (function_exists($func)) {
            log_db_test("   ✓ $func - Available");
        } else {
            log_db_test("   ✗ $func - Missing", "ERROR");
        }
    }
} else {
    log_db_test("   ✗ ODBC extension is NOT loaded", "ERROR");
    log_db_test("   Solution: Install php-odbc extension", "ERROR");
}

// 2. CHECK DATABASE CONFIGURATION
log_db_test("2. CHECKING DATABASE CONFIGURATION", "TEST");

try {
    if (file_exists(__DIR__ . '/src/dbOpen.php')) {
        log_db_test("   ✓ Database configuration file found");
        
        // Parse the configuration without executing it
        $config_content = file_get_contents(__DIR__ . '/src/dbOpen.php');
        
        // Extract configuration details
        if (preg_match("/define\('ENV',\s*'(\w+)'\)/", $config_content, $matches)) {
            $environment = $matches[1];
            log_db_test("   Environment: $environment");
        }
        
        // Extract database connections
        if (preg_match_all("/'DNS'\s*=>\s*'([^']+)'/", $config_content, $matches)) {
            log_db_test("   Configured DSNs:");
            foreach ($matches[1] as $dsn) {
                log_db_test("     - $dsn");
            }
        }
        
        if (preg_match_all("/'user'\s*=>\s*'([^']+)'/", $config_content, $matches)) {
            log_db_test("   Configured users:");
            foreach ($matches[1] as $user) {
                log_db_test("     - $user");
            }
        }
        
    } else {
        log_db_test("   ✗ Database configuration file NOT found", "ERROR");
    }
} catch (Exception $e) {
    log_db_test("   ✗ Error reading database configuration: " . $e->getMessage(), "ERROR");
}

// 3. TEST ODBC DSN AVAILABILITY
log_db_test("3. TESTING ODBC DSN AVAILABILITY", "TEST");

if (function_exists('odbc_data_source')) {
    log_db_test("   Available ODBC Data Sources:");
    
    $connection = null;
    try {
        $connection = @odbc_data_source(null, SQL_FETCH_FIRST);
        if ($connection) {
            do {
                log_db_test("     - DSN: " . ($connection['server'] ?? 'Unknown') . 
                           " Driver: " . ($connection['description'] ?? 'Unknown'));
                $connection = @odbc_data_source(null, SQL_FETCH_NEXT);
            } while ($connection);
        } else {
            log_db_test("   ! No ODBC data sources found or function unavailable", "WARNING");
        }
    } catch (Exception $e) {
        log_db_test("   ! Could not enumerate data sources: " . $e->getMessage(), "WARNING");
    }
} else {
    log_db_test("   ! odbc_data_source function not available", "WARNING");
}

// 4. TEST DATABASE CONNECTION
log_db_test("4. TESTING ACTUAL DATABASE CONNECTION", "TEST");

$db_connection = null;
$connection_errors = [];

try {
    // Suppress error output during connection test
    $old_error_reporting = error_reporting(0);
    
    // Include database configuration
    if (file_exists(__DIR__ . '/src/dbOpen.php')) {
        ob_start();
        include __DIR__ . '/src/dbOpen.php';
        $include_output = ob_get_clean();
        
        if (!empty($include_output)) {
            log_db_test("   ! Database config produces output:", "WARNING");
            log_db_test("     " . substr($include_output, 0, 200), "WARNING");
        }
        
        // Test the connection
        if (isset($db) && $db !== false) {
            log_db_test("   ✓ Database connection successful");
            $db_connection = $db;
            
            // Test connection validity
            if (is_resource($db_connection)) {
                log_db_test("   ✓ Connection is a valid resource");
            } else {
                log_db_test("   ! Connection is not a resource type", "WARNING");
            }
            
        } else {
            log_db_test("   ✗ Database connection failed", "ERROR");
            
            // Get ODBC error details
            if (function_exists('odbc_errormsg')) {
                $error_msg = @odbc_errormsg();
                if (!empty($error_msg)) {
                    log_db_test("   ODBC Error: $error_msg", "ERROR");
                }
            }
        }
        
        // Check if connectDatabase function is available
        if (function_exists('connectDatabase')) {
            log_db_test("   ✓ connectDatabase function is available");
            
            $test_connection = connectDatabase();
            if ($test_connection) {
                log_db_test("   ✓ connectDatabase function works");
                if ($test_connection !== $db_connection) {
                    log_db_test("   ! Different connection returned by function", "WARNING");
                }
            } else {
                log_db_test("   ✗ connectDatabase function failed", "ERROR");
            }
        }
        
    } else {
        log_db_test("   ✗ Cannot include database configuration", "ERROR");
    }
    
    error_reporting($old_error_reporting);
    
} catch (Exception $e) {
    error_reporting($old_error_reporting);
    log_db_test("   ✗ Database connection exception: " . $e->getMessage(), "ERROR");
} catch (Error $e) {
    error_reporting($old_error_reporting);
    log_db_test("   ✗ Database connection fatal error: " . $e->getMessage(), "ERROR");
}

// 5. TEST DATABASE QUERIES
if ($db_connection) {
    log_db_test("5. TESTING DATABASE QUERIES", "TEST");
    
    $test_queries = [
        "SELECT 1 as test_column" => "Basic SELECT test",
        "SELECT GETDATE()" => "SQL Server datetime test",
        "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES" => "Table count test"
    ];
    
    foreach ($test_queries as $query => $description) {
        try {
            log_db_test("   Testing: $description");
            
            $result = @odbc_exec($db_connection, $query);
            if ($result) {
                log_db_test("   ✓ Query executed successfully");
                
                // Try to fetch one row
                if (@odbc_fetch_row($result)) {
                    log_db_test("   ✓ Data fetch successful");
                } else {
                    log_db_test("   ! No data returned or fetch failed", "WARNING");
                }
                
                @odbc_free_result($result);
            } else {
                $error_msg = @odbc_errormsg($db_connection);
                log_db_test("   ✗ Query failed: $error_msg", "ERROR");
            }
            
        } catch (Exception $e) {
            log_db_test("   ✗ Query exception: " . $e->getMessage(), "ERROR");
        }
    }
    
} else {
    log_db_test("5. SKIPPING DATABASE QUERIES - No connection available", "WARNING");
}

// 6. TEST PREPARED STATEMENTS
if ($db_connection) {
    log_db_test("6. TESTING PREPARED STATEMENTS", "TEST");
    
    try {
        if (function_exists('safe_odbc_prepare')) {
            log_db_test("   ✓ safe_odbc_prepare function available");
            
            $stmt = safe_odbc_prepare($db_connection, "SELECT ? as test_param");
            if ($stmt) {
                log_db_test("   ✓ Prepared statement created");
                
                if (function_exists('safe_odbc_execute')) {
                    $result = safe_odbc_execute($stmt, ['test_value']);
                    if ($result) {
                        log_db_test("   ✓ Prepared statement executed");
                    } else {
                        log_db_test("   ✗ Prepared statement execution failed", "ERROR");
                    }
                }
            } else {
                log_db_test("   ✗ Could not create prepared statement", "ERROR");
            }
        } else {
            log_db_test("   ! safe_odbc_prepare function not available", "WARNING");
        }
        
    } catch (Exception $e) {
        log_db_test("   ✗ Prepared statement test exception: " . $e->getMessage(), "ERROR");
    }
}

// 7. CONNECTION CLEANUP TEST
log_db_test("7. TESTING CONNECTION CLEANUP", "TEST");

if ($db_connection) {
    try {
        if (is_resource($db_connection)) {
            log_db_test("   ✓ Connection is active resource");
            
            // Test connection close
            $close_result = @odbc_close($db_connection);
            if ($close_result) {
                log_db_test("   ✓ Connection closed successfully");
            } else {
                log_db_test("   ! Connection close returned false", "WARNING");
            }
        } else {
            log_db_test("   ! Connection is not a resource", "WARNING");
        }
    } catch (Exception $e) {
        log_db_test("   ✗ Connection cleanup exception: " . $e->getMessage(), "ERROR");
    }
}

// SUMMARY AND RECOMMENDATIONS
log_db_test("=== DATABASE TEST SUMMARY ===");

$log_content = file_get_contents($test_log);
$error_count = substr_count($log_content, '[ERROR]');
$warning_count = substr_count($log_content, '[WARNING]');

log_db_test("Errors: $error_count, Warnings: $warning_count");

if ($error_count > 0) {
    log_db_test("DATABASE STATUS: CRITICAL ISSUES", "ERROR");
    log_db_test("");
    log_db_test("TROUBLESHOOTING STEPS:", "ERROR");
    log_db_test("1. Verify ODBC extension is installed: php -m | grep odbc");
    log_db_test("2. Check ODBC Data Source configuration");
    log_db_test("3. Verify database server is running and accessible");
    log_db_test("4. Check database credentials and permissions");
    log_db_test("5. Review firewall and network connectivity");
} elseif ($warning_count > 0) {
    log_db_test("DATABASE STATUS: MINOR ISSUES", "WARNING");
    log_db_test("Connection works but some features may be limited");
} else {
    log_db_test("DATABASE STATUS: HEALTHY");
    log_db_test("All database connectivity tests passed");
}

log_db_test("");
log_db_test("Detailed test log saved to: $test_log");

?>