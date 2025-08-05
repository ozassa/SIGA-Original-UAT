<?php
/**
 * SIGA System Diagnostic Script
 * Performs comprehensive testing of the system to identify actual errors
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== SIGA SYSTEM DIAGNOSTIC REPORT ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// Test 1: PHP Syntax Check for Access.php
echo "1. TESTING ACCESS.PHP SYNTAX:\n";
$accessPath = __DIR__ . '/src/role/access/Access.php';
if (file_exists($accessPath)) {
    $output = [];
    $returnVar = 0;
    exec("php -l \"$accessPath\" 2>&1", $output, $returnVar);
    
    if ($returnVar === 0) {
        echo "   ✓ Access.php - No syntax errors\n";
    } else {
        echo "   ✗ Access.php - SYNTAX ERRORS FOUND:\n";
        foreach ($output as $line) {
            echo "     $line\n";
        }
    }
} else {
    echo "   ✗ Access.php - FILE NOT FOUND\n";
}

// Test 2: PHP Syntax Check for remember.php
echo "\n2. TESTING REMEMBER.PHP SYNTAX:\n";
$rememberPath = __DIR__ . '/remember.php';
if (file_exists($rememberPath)) {
    $output = [];
    $returnVar = 0;
    exec("php -l \"$rememberPath\" 2>&1", $output, $returnVar);
    
    if ($returnVar === 0) {
        echo "   ✓ remember.php - No syntax errors\n";
    } else {
        echo "   ✗ remember.php - SYNTAX ERRORS FOUND:\n";
        foreach ($output as $line) {
            echo "     $line\n";
        }
    }
} else {
    echo "   ✗ remember.php - FILE NOT FOUND\n";
}

// Test 3: Check required dependencies
echo "\n3. TESTING REQUIRED DEPENDENCIES:\n";
$dependencies = [
    'session_config.php' => __DIR__ . '/session_config.php',
    'security_functions.php' => __DIR__ . '/security_functions.php',
    'hybrid_auth.php' => __DIR__ . '/hybrid_auth.php',
    'src/dbOpen.php' => __DIR__ . '/src/dbOpen.php',
    'src/role/rolePrefix.php' => __DIR__ . '/src/role/rolePrefix.php'
];

foreach ($dependencies as $name => $path) {
    if (file_exists($path)) {
        $output = [];
        $returnVar = 0;
        exec("php -l \"$path\" 2>&1", $output, $returnVar);
        
        if ($returnVar === 0) {
            echo "   ✓ $name - No syntax errors\n";
        } else {
            echo "   ✗ $name - SYNTAX ERRORS:\n";
            foreach ($output as $line) {
                echo "     $line\n";
            }
        }
    } else {
        echo "   ✗ $name - FILE NOT FOUND\n";
    }
}

// Test 4: Check for potential issues in remember.php around line 232
echo "\n4. ANALYZING REMEMBER.PHP AROUND LINE 232:\n";
if (file_exists($rememberPath)) {
    $lines = file($rememberPath);
    if (count($lines) >= 232) {
        echo "   Lines 230-235:\n";
        for ($i = 229; $i < 235 && $i < count($lines); $i++) {
            $lineNum = $i + 1;
            $line = rtrim($lines[$i]);
            echo "     $lineNum: $line\n";
        }
        
        // Check for common issues
        $line232 = isset($lines[231]) ? trim($lines[231]) : '';
        if (strpos($line232, '} else {') !== false) {
            echo "   ✓ Line 232 appears to be valid PHP syntax\n";
        } else {
            echo "   ! Line 232 content: '$line232'\n";
        }
    } else {
        echo "   ✗ File has fewer than 232 lines\n";
    }
}

// Test 5: Test database connection (if possible)
echo "\n5. TESTING DATABASE CONNECTION:\n";
try {
    // Temporarily suppress errors for connection test
    $oldErrorReporting = error_reporting(0);
    
    // Try to include database connection
    if (file_exists(__DIR__ . '/src/dbOpen.php')) {
        include_once __DIR__ . '/src/dbOpen.php';
        
        if (isset($db) && $db !== false) {
            echo "   ✓ Database connection successful\n";
        } else {
            echo "   ✗ Database connection failed\n";
        }
    } else {
        echo "   ✗ Database configuration file not found\n";
    }
    
    error_reporting($oldErrorReporting);
} catch (Exception $e) {
    echo "   ✗ Database connection error: " . $e->getMessage() . "\n";
}

// Test 6: Check for missing functions or constants
echo "\n6. TESTING REQUIRED CONSTANTS AND FUNCTIONS:\n";

// Test if SALT is defined
if (defined('SALT')) {
    echo "   ✓ SALT constant is defined\n";
} else {
    echo "   ✗ SALT constant is NOT defined\n";
}

// Test ODBC functions
if (function_exists('odbc_connect')) {
    echo "   ✓ ODBC functions are available\n";
} else {
    echo "   ✗ ODBC functions are NOT available\n";
}

// Test session functions
if (function_exists('session_start')) {
    echo "   ✓ Session functions are available\n";
} else {
    echo "   ✗ Session functions are NOT available\n";
}

// Test 7: Check PHP version compatibility
echo "\n7. PHP ENVIRONMENT CHECK:\n";
echo "   PHP Version: " . PHP_VERSION . "\n";

if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
    echo "   ✓ PHP version is compatible\n";
} else {
    echo "   ! PHP version may have compatibility issues\n";
}

// Test 8: Check file permissions
echo "\n8. FILE PERMISSIONS CHECK:\n";
$criticalFiles = [
    'src/role/access/Access.php',
    'remember.php',
    'session_config.php',
    'security_functions.php'
];

foreach ($criticalFiles as $file) {
    $fullPath = __DIR__ . '/' . $file;
    if (file_exists($fullPath)) {
        if (is_readable($fullPath)) {
            echo "   ✓ $file - Readable\n";
        } else {
            echo "   ✗ $file - NOT readable\n";
        }
    }
}

// Test 9: Check for error logs
echo "\n9. ERROR LOG CHECK:\n";
$logPaths = [
    '/logs/security/',
    '/logs/',
    ini_get('error_log')
];

foreach ($logPaths as $logPath) {
    if ($logPath && is_dir(__DIR__ . $logPath)) {
        echo "   ✓ Log directory found: $logPath\n";
    } elseif ($logPath && file_exists($logPath)) {
        echo "   ✓ Error log file found: $logPath\n";
    }
}

echo "\n=== DIAGNOSTIC COMPLETE ===\n";
echo "If this script shows no syntax errors but you're still getting 500 errors,\n";
echo "the issue is likely runtime-related (database connection, missing includes, etc.)\n";
?>