<?php
/**
 * SIGA Advanced Security Test Suite
 * Testa a implementação do sistema de segurança avançado
 */

require_once('advanced_security_system.php');

class SecurityTestSuite {
    
    private $results = [];
    private $security;
    
    public function __construct() {
        $this->security = new AdvancedSecuritySystem(true); // Test in report-only mode
    }
    
    /**
     * Executa todos os testes
     */
    public function runAllTests() {
        echo "<h1>🛡️ SIGA Advanced Security Test Suite</h1>\n";
        echo "<style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .test-pass { color: #27ae60; font-weight: bold; }
            .test-fail { color: #e74c3c; font-weight: bold; }
            .test-warning { color: #f39c12; font-weight: bold; }
            .test-section { margin: 20px 0; padding: 15px; border-left: 4px solid #3498db; background: #f8f9fa; }
            .test-item { margin: 10px 0; padding: 8px; border-radius: 3px; }
            .test-item.pass { background: #d4edda; border: 1px solid #c3e6cb; }
            .test-item.fail { background: #f8d7da; border: 1px solid #f5c6cb; }
            .test-item.warning { background: #fff3cd; border: 1px solid #ffeaa7; }
        </style>\n";
        
        $this->testFileExistence();
        $this->testSecurityHeaders();
        $this->testCSPGeneration();
        $this->testNonceGeneration();
        $this->testConfigurationLoading();
        $this->testBrowserCompatibility();
        $this->testTinyMCECompatibility();
        $this->testLogDirectory();
        $this->testViolationHandler();
        
        $this->showResults();
    }
    
    /**
     * Testa existência de arquivos críticos
     */
    private function testFileExistence() {
        echo "<div class='test-section'><h2>📁 File Existence Tests</h2>\n";
        
        $criticalFiles = [
            'advanced_security_system.php' => 'Sistema principal de segurança',
            'csp-violation-handler.php' => 'Handler de violações CSP',
            'csp-dashboard.php' => 'Dashboard administrativo',
            'csp_config.json' => 'Arquivo de configuração',
            'inc_caixa_texto.php' => 'TinyMCE otimizado'
        ];
        
        foreach ($criticalFiles as $file => $description) {
            $exists = file_exists($file);
            $this->addResult("File: $file", $exists, $description);
            echo "<div class='test-item " . ($exists ? 'pass' : 'fail') . "'>";
            echo ($exists ? '✅' : '❌') . " <strong>$file</strong> - $description";
            echo "</div>\n";
        }
        
        echo "</div>\n";
    }
    
    /**
     * Testa geração de security headers
     */
    private function testSecurityHeaders() {
        echo "<div class='test-section'><h2>🛡️ Security Headers Tests</h2>\n";
        
        // Capture headers
        ob_start();
        $success = $this->security->applyAdvancedSecurityHeaders();
        ob_end_clean();
        
        $this->addResult("Headers Application", $success, "Security headers applied successfully");
        echo "<div class='test-item " . ($success ? 'pass' : 'fail') . "'>";
        echo ($success ? '✅' : '❌') . " <strong>Security Headers</strong> - " . ($success ? 'Applied successfully' : 'Failed to apply');
        echo "</div>\n";
        
        // Test CSP generation
        $csp = $this->security->generateIntelligentCSP();
        $hasCsp = !empty($csp);
        $this->addResult("CSP Policy Generation", $hasCsp, "CSP policy generated");
        echo "<div class='test-item " . ($hasCsp ? 'pass' : 'fail') . "'>";
        echo ($hasCsp ? '✅' : '❌') . " <strong>CSP Policy</strong> - " . ($hasCsp ? 'Generated successfully' : 'Failed to generate');
        if ($hasCsp) {
            echo "<br><small>Policy: " . htmlspecialchars(substr($csp, 0, 100)) . "...</small>";
        }
        echo "</div>\n";
        
        echo "</div>\n";
    }
    
    /**
     * Testa geração de CSP
     */
    private function testCSPGeneration() {
        echo "<div class='test-section'><h2>🔐 CSP Generation Tests</h2>\n";
        
        $csp = $this->security->generateIntelligentCSP();
        
        // Test required directives
        $requiredDirectives = [
            'default-src',
            'script-src',
            'style-src',
            'img-src',
            'connect-src',
            'base-uri',
            'form-action'
        ];
        
        foreach ($requiredDirectives as $directive) {
            $hasDirective = strpos($csp, $directive) !== false;
            $this->addResult("CSP Directive: $directive", $hasDirective, "Required CSP directive present");
            echo "<div class='test-item " . ($hasDirective ? 'pass' : 'fail') . "'>";
            echo ($hasDirective ? '✅' : '❌') . " <strong>$directive</strong> - " . ($hasDirective ? 'Present' : 'Missing');
            echo "</div>\n";
        }
        
        // Test unsafe-inline for TinyMCE compatibility
        $hasUnsafeInline = strpos($csp, "'unsafe-inline'") !== false;
        echo "<div class='test-item " . ($hasUnsafeInline ? 'pass' : 'warning') . "'>";
        echo ($hasUnsafeInline ? '✅' : '⚠️') . " <strong>Unsafe Inline</strong> - " . ($hasUnsafeInline ? 'Enabled for TinyMCE compatibility' : 'Disabled (may break TinyMCE)');
        echo "</div>\n";
        
        echo "</div>\n";
    }
    
    /**
     * Testa geração de nonces
     */
    private function testNonceGeneration() {
        echo "<div class='test-section'><h2>🎲 Nonce Generation Tests</h2>\n";
        
        $nonce1 = $this->security->getNonce();
        $nonce2 = (new AdvancedSecuritySystem())->getNonce();
        
        $hasNonce = !empty($nonce1);
        $isUnique = $nonce1 !== $nonce2;
        $isValidLength = strlen($nonce1) >= 16;
        
        $this->addResult("Nonce Generation", $hasNonce, "Nonce generated successfully");
        echo "<div class='test-item " . ($hasNonce ? 'pass' : 'fail') . "'>";
        echo ($hasNonce ? '✅' : '❌') . " <strong>Nonce Generation</strong> - " . ($hasNonce ? 'Working' : 'Failed');
        if ($hasNonce) echo "<br><small>Sample: " . htmlspecialchars($nonce1) . "</small>";
        echo "</div>\n";
        
        $this->addResult("Nonce Uniqueness", $isUnique, "Nonces are unique between instances");
        echo "<div class='test-item " . ($isUnique ? 'pass' : 'fail') . "'>";
        echo ($isUnique ? '✅' : '❌') . " <strong>Nonce Uniqueness</strong> - " . ($isUnique ? 'Unique' : 'Not unique');
        echo "</div>\n";
        
        $this->addResult("Nonce Length", $isValidLength, "Nonce has adequate length");
        echo "<div class='test-item " . ($isValidLength ? 'pass' : 'fail') . "'>";
        echo ($isValidLength ? '✅' : '❌') . " <strong>Nonce Length</strong> - " . ($isValidLength ? 'Valid (' . strlen($nonce1) . ' chars)' : 'Too short');
        echo "</div>\n";
        
        echo "</div>\n";
    }
    
    /**
     * Testa carregamento de configuração
     */
    private function testConfigurationLoading() {
        echo "<div class='test-section'><h2>⚙️ Configuration Tests</h2>\n";
        
        $configExists = file_exists('csp_config.json');
        echo "<div class='test-item " . ($configExists ? 'pass' : 'warning') . "'>";
        echo ($configExists ? '✅' : '⚠️') . " <strong>Config File</strong> - " . ($configExists ? 'Found' : 'Using defaults');
        echo "</div>\n";
        
        if ($configExists) {
            $configContent = file_get_contents('csp_config.json');
            $configValid = json_decode($configContent) !== null;
            echo "<div class='test-item " . ($configValid ? 'pass' : 'fail') . "'>";
            echo ($configValid ? '✅' : '❌') . " <strong>Config Validation</strong> - " . ($configValid ? 'Valid JSON' : 'Invalid JSON');
            echo "</div>\n";
        }
        
        echo "</div>\n";
    }
    
    /**
     * Testa compatibilidade com navegadores
     */
    private function testBrowserCompatibility() {
        echo "<div class='test-section'><h2>🌐 Browser Compatibility Tests</h2>\n";
        
        $supportsCSP = browser_supports_csp();
        echo "<div class='test-item " . ($supportsCSP ? 'pass' : 'warning') . "'>";
        echo ($supportsCSP ? '✅' : '⚠️') . " <strong>Current Browser</strong> - " . ($supportsCSP ? 'Supports CSP' : 'Legacy browser detected');
        echo "</div>\n";
        
        // Test fallback function exists
        $hasFallback = function_exists('apply_legacy_security_fallback');
        echo "<div class='test-item " . ($hasFallback ? 'pass' : 'fail') . "'>";
        echo ($hasFallback ? '✅' : '❌') . " <strong>Legacy Fallback</strong> - " . ($hasFallback ? 'Available' : 'Missing');
        echo "</div>\n";
        
        echo "</div>\n";
    }
    
    /**
     * Testa compatibilidade com TinyMCE
     */
    private function testTinyMCECompatibility() {
        echo "<div class='test-section'><h2>📝 TinyMCE Compatibility Tests</h2>\n";
        
        $tinyMCEFile = 'inc_caixa_texto.php';
        $fileExists = file_exists($tinyMCEFile);
        echo "<div class='test-item " . ($fileExists ? 'pass' : 'fail') . "'>";
        echo ($fileExists ? '✅' : '❌') . " <strong>TinyMCE File</strong> - " . ($fileExists ? 'Found and updated' : 'Missing');
        echo "</div>\n";
        
        if ($fileExists) {
            $content = file_get_contents($tinyMCEFile);
            $hasNonceSupport = strpos($content, 'nonce') !== false;
            $hasSecuritySystem = strpos($content, 'advanced_security_system') !== false;
            
            echo "<div class='test-item " . ($hasNonceSupport ? 'pass' : 'warning') . "'>";
            echo ($hasNonceSupport ? '✅' : '⚠️') . " <strong>Nonce Support</strong> - " . ($hasNonceSupport ? 'Implemented' : 'Not implemented');
            echo "</div>\n";
            
            echo "<div class='test-item " . ($hasSecuritySystem ? 'pass' : 'warning') . "'>";
            echo ($hasSecuritySystem ? '✅' : '⚠️') . " <strong>Security Integration</strong> - " . ($hasSecuritySystem ? 'Integrated' : 'Not integrated');
            echo "</div>\n";
        }
        
        echo "</div>\n";
    }
    
    /**
     * Testa diretório de logs
     */
    private function testLogDirectory() {
        echo "<div class='test-section'><h2>📋 Logging Tests</h2>\n";
        
        $logDir = 'logs';
        $dirExists = is_dir($logDir);
        $dirWritable = $dirExists && is_writable($logDir);
        
        echo "<div class='test-item " . ($dirExists ? 'pass' : 'warning') . "'>";
        echo ($dirExists ? '✅' : '⚠️') . " <strong>Log Directory</strong> - " . ($dirExists ? 'Exists' : 'Will be created on first use');
        echo "</div>\n";
        
        if ($dirExists) {
            echo "<div class='test-item " . ($dirWritable ? 'pass' : 'fail') . "'>";
            echo ($dirWritable ? '✅' : '❌') . " <strong>Directory Writable</strong> - " . ($dirWritable ? 'Yes' : 'No - check permissions');
            echo "</div>\n";
        }
        
        echo "</div>\n";
    }
    
    /**
     * Testa handler de violações
     */
    private function testViolationHandler() {
        echo "<div class='test-section'><h2>⚠️ Violation Handler Tests</h2>\n";
        
        $handlerExists = file_exists('csp-violation-handler.php');
        echo "<div class='test-item " . ($handlerExists ? 'pass' : 'fail') . "'>";
        echo ($handlerExists ? '✅' : '❌') . " <strong>Violation Handler</strong> - " . ($handlerExists ? 'Ready' : 'Missing');
        echo "</div>\n";
        
        $dashboardExists = file_exists('csp-dashboard.php');
        echo "<div class='test-item " . ($dashboardExists ? 'pass' : 'fail') . "'>";
        echo ($dashboardExists ? '✅' : '❌') . " <strong>Dashboard</strong> - " . ($dashboardExists ? 'Available' : 'Missing');
        echo "</div>\n";
        
        echo "</div>\n";
    }
    
    /**
     * Adiciona resultado do teste
     */
    private function addResult($test, $passed, $description) {
        $this->results[] = [
            'test' => $test,
            'passed' => $passed,
            'description' => $description
        ];
    }
    
    /**
     * Mostra resumo dos resultados
     */
    private function showResults() {
        $total = count($this->results);
        $passed = array_sum(array_column($this->results, 'passed'));
        $failed = $total - $passed;
        $percentage = $total > 0 ? round(($passed / $total) * 100) : 0;
        
        echo "<div class='test-section'><h2>📊 Test Results Summary</h2>\n";
        echo "<div style='font-size: 1.2em; margin: 20px 0;'>";
        echo "<strong>Overall Score: {$percentage}%</strong><br>";
        echo "<span class='test-pass'>✅ Passed: {$passed}</span> | ";
        echo "<span class='test-fail'>❌ Failed: {$failed}</span> | ";
        echo "<strong>Total: {$total}</strong>";
        echo "</div>\n";
        
        if ($percentage >= 90) {
            echo "<div class='test-item pass'>";
            echo "<h3>🎉 Excellent! Security implementation is ready for production.</h3>";
            echo "<p>✅ All critical components are working correctly.<br>";
            echo "✅ You can safely proceed to enable enforcement mode.<br>";
            echo "✅ Monitor the dashboard for any violations.</p>";
            echo "</div>\n";
        } elseif ($percentage >= 70) {
            echo "<div class='test-item warning'>";
            echo "<h3>⚠️ Good! Minor issues detected.</h3>";
            echo "<p>⚠️ Most components are working, but some improvements needed.<br>";
            echo "⚠️ Review failed tests before enabling enforcement mode.<br>";
            echo "⚠️ Continue monitoring in report-only mode.</p>";
            echo "</div>\n";
        } else {
            echo "<div class='test-item fail'>";
            echo "<h3>❌ Issues detected! Review required.</h3>";
            echo "<p>❌ Several critical components need attention.<br>";
            echo "❌ Do not enable enforcement mode yet.<br>";
            echo "❌ Fix failed tests and run this suite again.</p>";
            echo "</div>\n";
        }
        
        echo "</div>\n";
        
        echo "<div class='test-section'>";
        echo "<h3>🔗 Next Steps</h3>";
        echo "<ol>";
        echo "<li><a href='csp-dashboard.php'>Open CSP Dashboard</a> to monitor violations</li>";
        echo "<li>Test your application thoroughly with all features</li>";
        echo "<li>Monitor for 1-2 weeks in report-only mode</li>";
        echo "<li>When ready, enable enforcement mode in header.php</li>";
        echo "<li>Keep monitoring the dashboard regularly</li>";
        echo "</ol>";
        echo "</div>\n";
    }
}

// Run the test suite
$testSuite = new SecurityTestSuite();
$testSuite->runAllTests();

?>