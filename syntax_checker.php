<?php
/**
 * SIGA PHP Syntax Validation Tool
 * 
 * Systematically checks PHP syntax of all critical files
 * to identify syntax errors causing 500 errors.
 * 
 * SAFE FOR PRODUCTION - Only performs syntax checking
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$syntax_log = __DIR__ . '/logs/syntax_check_' . date('Y-m-d_H-i-s') . '.log';
if (!is_dir(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0755, true);
}

function log_syntax($message, $level = 'INFO') {
    global $syntax_log;
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] [$level] $message" . PHP_EOL;
    file_put_contents($syntax_log, $log_entry, FILE_APPEND | LOCK_EX);
    echo $log_entry;
}

log_syntax("=== SIGA PHP SYNTAX VALIDATION ===");

// 1. DEFINE FILES TO CHECK
$files_to_check = [
    // Core system files
    'config.php',
    'index.php', 
    'session_config.php',
    'security_functions.php',
    'hybrid_auth.php',
    'remember.php',
    
    // Database files
    'src/dbOpen.php',
    
    // Access control
    'src/role/access/Access.php',
    'src/role/rolePrefix.php',
    
    // Authentication files
    'auth_check.php',
    'recover_password.php',
    'reset_password.php',
    
    // Emergency files
    'emergency_index.php',
    'emergency_index_db.php',
    'emergency_index_v2.php',
    
    // Validation files
    'InputValidationFramework.php',
    'ValidationConfig.php',
    'ValidationMiddleware.php',
    
    // Additional critical files
    'main.php',
    'home.php',
    'menu.php',
    'header.php',
    'footer.php'
];

// Add all PHP files in src/role/access/
$access_dir = __DIR__ . '/src/role/access/';
if (is_dir($access_dir)) {
    $access_files = glob($access_dir . '*.php');
    foreach ($access_files as $file) {
        $relative_path = str_replace(__DIR__ . '/', '', $file);
        if (!in_array($relative_path, $files_to_check)) {
            $files_to_check[] = $relative_path;
        }
    }
}

log_syntax("Total files to check: " . count($files_to_check));

// 2. PERFORM SYNTAX CHECKS
log_syntax("1. PERFORMING SYNTAX VALIDATION", "TEST");

$syntax_errors = [];
$syntax_warnings = [];
$files_checked = 0;

foreach ($files_to_check as $relative_path) {
    $full_path = __DIR__ . '/' . $relative_path;
    
    if (!file_exists($full_path)) {
        log_syntax("   ! $relative_path - FILE NOT FOUND", "WARNING");
        $syntax_warnings[] = $relative_path;
        continue;
    }
    
    if (!is_readable($full_path)) {
        log_syntax("   ! $relative_path - NOT READABLE", "WARNING");
        $syntax_warnings[] = $relative_path;
        continue;
    }
    
    $files_checked++;
    
    // Perform syntax check
    $output = [];
    $return_code = 0;
    $command = "php -l " . escapeshellarg($full_path) . " 2>&1";
    exec($command, $output, $return_code);
    
    if ($return_code === 0) {
        log_syntax("   ✓ $relative_path - SYNTAX OK");
    } else {
        log_syntax("   ✗ $relative_path - SYNTAX ERROR", "ERROR");
        $syntax_errors[] = $relative_path;
        
        // Log detailed error information
        foreach ($output as $error_line) {
            log_syntax("     $error_line", "ERROR");
        }
        log_syntax("", "ERROR"); // Empty line for readability
    }
}

log_syntax("Files checked: $files_checked");

// 3. DEEP ANALYSIS OF PROBLEMATIC FILES
if (!empty($syntax_errors)) {
    log_syntax("2. DETAILED ANALYSIS OF SYNTAX ERRORS", "TEST");
    
    foreach ($syntax_errors as $error_file) {
        $full_path = __DIR__ . '/' . $error_file;
        log_syntax("   Analyzing: $error_file", "ERROR");
        
        if (file_exists($full_path)) {
            $file_content = file_get_contents($full_path);
            $lines = file($full_path);
            
            // Check for common syntax issues
            $common_issues = [
                'Unclosed quotes' => ['/["\'][^"\']*$/', 'Lines ending with unclosed quotes'],
                'Missing semicolons' => ['/[^;{}]\s*\n\s*[a-zA-Z$]/', 'Possible missing semicolons'],
                'Unmatched braces' => ['', 'Check brace matching manually'],
                'PHP tags' => ['/\<\?\s/', 'Incorrect PHP opening tags'],
                'Short tags' => ['/\<\?\s[^p]/', 'PHP short tags (may not be enabled)']
            ];
            
            foreach ($common_issues as $issue_name => $pattern_info) {
                if (!empty($pattern_info[0])) {
                    if (preg_match_all($pattern_info[0], $file_content, $matches, PREG_OFFSET_CAPTURE)) {
                        log_syntax("     Potential issue: $issue_name", "ERROR");
                        log_syntax("     Description: " . $pattern_info[1], "ERROR");
                    }
                }
            }
            
            // Check brace matching
            $open_braces = substr_count($file_content, '{');
            $close_braces = substr_count($file_content, '}');
            if ($open_braces !== $close_braces) {
                log_syntax("     Brace mismatch: $open_braces opening, $close_braces closing", "ERROR");
            }
            
            // Check parentheses matching
            $open_parens = substr_count($file_content, '(');
            $close_parens = substr_count($file_content, ')');
            if ($open_parens !== $close_parens) {
                log_syntax("     Parentheses mismatch: $open_parens opening, $close_parens closing", "ERROR");
            }
            
            // Show file size and line count
            log_syntax("     File size: " . strlen($file_content) . " bytes", "ERROR");
            log_syntax("     Line count: " . count($lines), "ERROR");
            
            // Show first few lines with line numbers
            log_syntax("     First 5 lines:", "ERROR");
            for ($i = 0; $i < min(5, count($lines)); $i++) {
                $line_num = $i + 1;
                $line_content = rtrim($lines[$i]);
                log_syntax("     $line_num: $line_content", "ERROR");
            }
            
            log_syntax("", "ERROR"); // Empty line for readability
        }
    }
}

// 4. CHECK FOR SPECIFIC KNOWN ISSUES
log_syntax("3. CHECKING FOR KNOWN ISSUES", "TEST");

// Check remember.php around line 232 (known issue from diagnostic)
$remember_path = __DIR__ . '/remember.php';
if (file_exists($remember_path)) {
    $remember_lines = file($remember_path);
    if (count($remember_lines) >= 232) {
        log_syntax("   Checking remember.php line 232:");
        
        // Show lines around 232
        for ($i = 229; $i < 235 && $i < count($remember_lines); $i++) {
            $line_num = $i + 1;
            $line_content = rtrim($remember_lines[$i]);
            log_syntax("     $line_num: $line_content");
            
            // Check for specific issues on line 232
            if ($i === 231) { // Line 232 (0-indexed)
                if (trim($line_content) === '} else {') {
                    log_syntax("   ✓ Line 232 syntax appears correct");
                } elseif (empty(trim($line_content))) {
                    log_syntax("   ! Line 232 is empty", "WARNING");
                } else {
                    log_syntax("   ! Line 232 content unexpected: '$line_content'", "WARNING");
                }
            }
        }
    } else {
        log_syntax("   ! remember.php has fewer than 232 lines", "WARNING");
    }
}

// 5. CHECK FOR ENCODING ISSUES
log_syntax("4. CHECKING FOR ENCODING ISSUES", "TEST");

foreach ($syntax_errors as $error_file) {
    $full_path = __DIR__ . '/' . $error_file;
    if (file_exists($full_path)) {
        $file_content = file_get_contents($full_path);
        
        // Check for BOM
        if (substr($file_content, 0, 3) === "\xEF\xBB\xBF") {
            log_syntax("   ! $error_file has UTF-8 BOM", "WARNING");
        }
        
        // Check encoding
        if (!mb_check_encoding($file_content, 'UTF-8') && !mb_check_encoding($file_content, 'ISO-8859-1')) {
            log_syntax("   ! $error_file has encoding issues", "WARNING");
        }
        
        // Check for null bytes
        if (strpos($file_content, "\0") !== false) {
            log_syntax("   ! $error_file contains null bytes", "WARNING");
        }
    }
}

// 6. FIND ALL PHP FILES FOR COMPREHENSIVE CHECK
log_syntax("5. COMPREHENSIVE PHP FILE SCAN", "TEST");

$all_php_files = [];

// Recursively find PHP files
function find_php_files($dir, $base_dir) {
    $php_files = [];
    if (is_dir($dir)) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            
            $full_path = $dir . '/' . $file;
            $relative_path = str_replace($base_dir . '/', '', $full_path);
            
            if (is_dir($full_path)) {
                // Skip certain directories
                if (in_array($file, ['vendor', 'node_modules', '.git', 'backup'])) {
                    continue;
                }
                $php_files = array_merge($php_files, find_php_files($full_path, $base_dir));
            } elseif (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $php_files[] = $relative_path;
            }
        }
    }
    return $php_files;
}

$all_php_files = find_php_files(__DIR__, __DIR__);
log_syntax("Found " . count($all_php_files) . " PHP files total");

// Quick syntax check of all PHP files
$additional_errors = 0;
foreach ($all_php_files as $php_file) {
    if (in_array($php_file, $files_to_check)) {
        continue; // Already checked
    }
    
    $full_path = __DIR__ . '/' . $php_file;
    $output = [];
    $return_code = 0;
    exec("php -l " . escapeshellarg($full_path) . " 2>&1", $output, $return_code);
    
    if ($return_code !== 0) {
        $additional_errors++;
        log_syntax("   ✗ $php_file - Additional syntax error found", "ERROR");
    }
}

if ($additional_errors > 0) {
    log_syntax("Found $additional_errors additional files with syntax errors", "ERROR");
} else {
    log_syntax("No additional syntax errors found in other PHP files");
}

// SUMMARY AND RECOMMENDATIONS
log_syntax("=== SYNTAX CHECK SUMMARY ===");

$total_errors = count($syntax_errors) + $additional_errors;
$total_warnings = count($syntax_warnings);

log_syntax("Files checked: $files_checked");
log_syntax("Syntax errors: $total_errors");
log_syntax("Warnings: $total_warnings");

if ($total_errors > 0) {
    log_syntax("SYNTAX STATUS: CRITICAL ERRORS FOUND", "ERROR");
    log_syntax("");
    log_syntax("FILES WITH SYNTAX ERRORS:", "ERROR");
    foreach ($syntax_errors as $error_file) {
        log_syntax("  - $error_file", "ERROR");
    }
    log_syntax("");
    log_syntax("IMMEDIATE ACTIONS REQUIRED:", "ERROR");
    log_syntax("1. Fix syntax errors in the files listed above", "ERROR");
    log_syntax("2. Use a PHP IDE or editor with syntax highlighting", "ERROR");
    log_syntax("3. Test each file individually: php -l filename.php", "ERROR");
    log_syntax("4. Check for missing semicolons, unmatched braces, quotes", "ERROR");
    log_syntax("5. Verify file encoding (should be UTF-8 without BOM)", "ERROR");
} elseif ($total_warnings > 0) {
    log_syntax("SYNTAX STATUS: MINOR ISSUES", "WARNING");
    log_syntax("All critical syntax checks passed, but some files are missing");
} else {
    log_syntax("SYNTAX STATUS: ALL CLEAR");
    log_syntax("No syntax errors found in any PHP files");
}

log_syntax("");
log_syntax("Detailed syntax log saved to: $syntax_log");

?>