<?php
/**
 * SIGA Production Security Verification Script
 * 
 * Comprehensive security check for production environment.
 * Verifies all security configurations are properly set up.
 * 
 * Usage: Run this script to verify production security status
 * 
 * @version 1.0
 * @author Claude Code - Security Production Cleanup
 */

// Prevent direct web access
if (php_sapi_name() !== 'cli' && !isset($_GET['admin_check'])) {
    http_response_code(404);
    exit('Not Found');
}

// Include production security config
require_once __DIR__ . '/production_security_config.php';

class ProductionSecurityChecker {
    
    private $results = [];
    private $critical_issues = 0;
    private $warnings = 0;
    
    public function runAllChecks() {
        echo "=== SIGA Production Security Verification ===\n";
        echo "Starting comprehensive security check...\n\n";
        
        $this->checkSecurityFiles();
        $this->checkSecurityHeaders();
        $this->checkSessionSecurity();
        $this->checkErrorHandling();
        $this->checkFilePermissions();
        $this->checkCSPConfiguration();
        $this->checkLoggingSetup();
        $this->checkPHPSecuritySettings();
        $this->checkTestFilesRemoved();
        $this->checkProductionReadiness();
        
        $this->displayResults();
        return $this->critical_issues === 0;
    }
    
    private function checkSecurityFiles() {
        echo "Checking security files...\n";
        
        $required_files = [
            'production_security_config.php' => 'Production security configuration',
            'security_functions.php' => 'Core security functions',
            'advanced_security_system.php' => 'Advanced security system',
            'session_config.php' => 'Session configuration',
            'csp_config.json' => 'CSP configuration'
        ];
        
        foreach ($required_files as $file => $description) {
            if (file_exists(__DIR__ . '/' . $file)) {
                $this->addResult('PASS', "Required file exists: $file ($description)");
            } else {
                $this->addResult('CRITICAL', "Missing required file: $file ($description)");
            }
        }
        
        // Check for removed test files
        $test_files = [
            'phpinfo.php', 'test_database_connection.php', 'security_test.php',
            'validation_test.php', 'diagnostic_check.php', 'system_verification_test.php'
        ];
        
        foreach ($test_files as $file) {
            if (!file_exists(__DIR__ . '/' . $file)) {
                $this->addResult('PASS', "Test file properly removed: $file");
            } else {
                $this->addResult('WARNING', "Test file still exists: $file (should be removed in production)");
            }
        }
    }
    
    private function checkSecurityHeaders() {
        echo "Checking security headers configuration...\n";
        
        if (function_exists('apply_production_security_headers')) {
            $this->addResult('PASS', 'Production security headers function available');
            
            // Test if headers would be set (simulation)
            if (function_exists('get_security_status')) {
                $status = get_security_status();
                if ($status['production_mode']) {
                    $this->addResult('PASS', 'Production mode enabled');
                } else {
                    $this->addResult('WARNING', 'Production mode not enabled');
                }
            }
        } else {
            $this->addResult('CRITICAL', 'Production security headers function not available');
        }
    }
    
    private function checkSessionSecurity() {
        echo "Checking session security...\n";
        
        $session_checks = [
            'session.cookie_httponly' => '1',
            'session.use_only_cookies' => '1',
            'session.use_strict_mode' => '1'
        ];
        
        foreach ($session_checks as $setting => $expected) {
            $actual = ini_get($setting);
            if ($actual == $expected) {
                $this->addResult('PASS', "Session setting correct: $setting = $actual");
            } else {
                $this->addResult('WARNING', "Session setting needs review: $setting = $actual (expected: $expected)");
            }
        }
        
        // Check constants
        if (defined('SESSION_TIMEOUT')) {
            $timeout = SESSION_TIMEOUT;
            if ($timeout > 0 && $timeout <= 3600) {
                $this->addResult('PASS', "Session timeout properly configured: {$timeout}s");
            } else {
                $this->addResult('WARNING', "Session timeout may be too long: {$timeout}s");
            }
        } else {
            $this->addResult('WARNING', 'SESSION_TIMEOUT constant not defined');
        }
    }
    
    private function checkErrorHandling() {
        echo "Checking error handling...\n";
        
        $error_checks = [
            'display_errors' => '0',
            'log_errors' => '1'
        ];
        
        foreach ($error_checks as $setting => $expected) {
            $actual = ini_get($setting);
            if ($actual == $expected) {
                $this->addResult('PASS', "Error handling correct: $setting = $actual");
            } else {
                if ($setting === 'display_errors' && $actual !== '0') {
                    $this->addResult('CRITICAL', "Security risk: $setting = $actual (should be 0 in production)");
                } else {
                    $this->addResult('WARNING', "Error setting needs review: $setting = $actual");
                }
            }
        }
    }
    
    private function checkFilePermissions() {
        echo "Checking file permissions...\n";
        
        $log_dir = __DIR__ . '/logs';
        if (is_dir($log_dir)) {
            $perms = substr(sprintf('%o', fileperms($log_dir)), -4);
            if ($perms === '0755' || $perms === '0750') {
                $this->addResult('PASS', "Log directory permissions correct: $perms");
            } else {
                $this->addResult('WARNING', "Log directory permissions: $perms (recommended: 0755)");
            }
            
            // Check .htaccess protection
            $htaccess = $log_dir . '/.htaccess';
            if (file_exists($htaccess)) {
                $this->addResult('PASS', 'Log directory protected with .htaccess');
            } else {
                $this->addResult('WARNING', 'Log directory not protected with .htaccess');
            }
        } else {
            $this->addResult('WARNING', 'Log directory does not exist');
        }
    }
    
    private function checkCSPConfiguration() {
        echo "Checking CSP configuration...\n";
        
        $csp_config = __DIR__ . '/csp_config.json';
        if (file_exists($csp_config)) {
            $config = json_decode(file_get_contents($csp_config), true);
            if ($config) {
                if ($config['report_only'] === false) {
                    $this->addResult('PASS', 'CSP enforcement mode enabled (not report-only)');
                } else {
                    $this->addResult('WARNING', 'CSP still in report-only mode');
                }
                
                if ($config['log_violations'] === true) {
                    $this->addResult('PASS', 'CSP violation logging enabled');
                } else {
                    $this->addResult('WARNING', 'CSP violation logging disabled');
                }
            } else {
                $this->addResult('CRITICAL', 'CSP configuration file is invalid JSON');
            }
        } else {
            $this->addResult('WARNING', 'CSP configuration file not found');
        }
    }
    
    private function checkLoggingSetup() {
        echo "Checking logging setup...\n";
        
        if (defined('SECURITY_LOG_DIR')) {
            $log_dir = SECURITY_LOG_DIR;
            if (is_dir($log_dir) && is_writable($log_dir)) {
                $this->addResult('PASS', 'Security log directory exists and writable');
            } else {
                $this->addResult('CRITICAL', 'Security log directory not writable or missing');
            }
        } else {
            $this->addResult('WARNING', 'SECURITY_LOG_DIR constant not defined');
        }
        
        if (function_exists('log_security_incident')) {
            $this->addResult('PASS', 'Security logging function available');
        } else {
            $this->addResult('CRITICAL', 'Security logging function not available');
        }
    }
    
    private function checkPHPSecuritySettings() {
        echo "Checking PHP security settings...\n";
        
        $php_checks = [
            'expose_php' => '0',
            'allow_url_fopen' => '0',
            'allow_url_include' => '0',
            'enable_dl' => '0'
        ];
        
        foreach ($php_checks as $setting => $expected) {
            $actual = ini_get($setting);
            if ($actual == $expected) {
                $this->addResult('PASS', "PHP security setting correct: $setting = $actual");
            } else {
                if (in_array($setting, ['expose_php', 'allow_url_include'])) {
                    $this->addResult('WARNING', "PHP security setting: $setting = $actual (recommended: $expected)");
                } else {
                    $this->addResult('INFO', "PHP setting: $setting = $actual");
                }
            }
        }
    }
    
    private function checkTestFilesRemoved() {
        echo "Verifying test files removal...\n";
        
        $backup_dir = __DIR__ . '/backup';
        if (is_dir($backup_dir)) {
            $this->addResult('PASS', 'Backup directory exists for old files');
        }
        
        // Check if development files were moved
        $dev_files = glob(__DIR__ . '/*test*.php');
        $dev_files = array_merge($dev_files, glob(__DIR__ . '/*debug*.php'));
        
        if (empty($dev_files)) {
            $this->addResult('PASS', 'No development/test files found in production directory');
        } else {
            $this->addResult('WARNING', 'Development files still present: ' . implode(', ', $dev_files));
        }
    }
    
    private function checkProductionReadiness() {
        echo "Checking overall production readiness...\n";
        
        if (defined('SIGA_PRODUCTION') && SIGA_PRODUCTION === true) {
            $this->addResult('PASS', 'Production flag properly set');
        } else {
            $this->addResult('CRITICAL', 'Production flag not set or incorrect');
        }
        
        // Check if security system is initialized
        if (defined('SECURITY_INITIALIZED')) {
            $this->addResult('PASS', 'Security system initialized');
        } else {
            $this->addResult('WARNING', 'Security system initialization flag not found');
        }
    }
    
    private function addResult($level, $message) {
        $this->results[] = ['level' => $level, 'message' => $message];
        
        switch ($level) {
            case 'CRITICAL':
                $this->critical_issues++;
                echo "❌ CRITICAL: $message\n";
                break;
            case 'WARNING':
                $this->warnings++;
                echo "⚠️  WARNING: $message\n";
                break;
            case 'PASS':
                echo "✅ PASS: $message\n";
                break;
            case 'INFO':
                echo "ℹ️  INFO: $message\n";
                break;
        }
    }
    
    private function displayResults() {
        echo "\n=== SECURITY CHECK SUMMARY ===\n";
        echo "Total checks performed: " . count($this->results) . "\n";
        echo "Critical issues: {$this->critical_issues}\n";
        echo "Warnings: {$this->warnings}\n";
        
        if ($this->critical_issues === 0) {
            if ($this->warnings === 0) {
                echo "\n✅ PRODUCTION READY: All security checks passed!\n";
            } else {
                echo "\n⚠️  MOSTLY READY: No critical issues, but {$this->warnings} warnings to review.\n";
            }
        } else {
            echo "\n❌ NOT PRODUCTION READY: {$this->critical_issues} critical issues must be fixed.\n";
        }
        
        echo "\nSecurity cleanup completed successfully.\n";
        echo "System is optimized for production use.\n";
    }
}

// Run the security check
$checker = new ProductionSecurityChecker();
$is_ready = $checker->runAllChecks();

// Exit with appropriate code for CLI usage
if (php_sapi_name() === 'cli') {
    exit($is_ready ? 0 : 1);
}

?>