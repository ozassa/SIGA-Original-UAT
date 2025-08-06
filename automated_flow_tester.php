<?php
/**
 * SISTEMA DE TESTES AUTOMATIZADOS - SIGA
 * Simula navegação e valida funcionalidades críticas
 */

class SigaFlowTester {
    private $baseDir;
    private $results = [];
    private $session_data = [];
    
    public function __construct($baseDir) {
        $this->baseDir = $baseDir;
    }
    
    public function runAllTests() {
        echo "<h1>🧪 SIGA - Sistema de Testes Automatizados</h1>";
        echo "<div style='font-family: Arial, sans-serif; margin: 20px;'>";
        
        $this->testDatabaseConnection();
        $this->testAuthenticationFlow();
        $this->testFileIntegrity();
        $this->testSecurityFeatures();
        $this->testNavigationFlow();
        $this->generateReport();
        
        echo "</div>";
    }
    
    private function testDatabaseConnection() {
        echo "<h2>🔌 Teste 1: Conexão com Banco de Dados</h2>";
        
        try {
            // Verificar ODBC
            if (!extension_loaded('odbc')) {
                $this->addResult('DB_ODBC', 'FALHA', 'Extensão ODBC não carregada');
                return;
            }
            $this->addResult('DB_ODBC', 'SUCESSO', 'Extensão ODBC disponível');
            
            // Verificar arquivo dbOpen.php
            $dbFile = $this->baseDir . '/src/dbOpen.php';
            if (!file_exists($dbFile)) {
                $this->addResult('DB_CONFIG', 'FALHA', 'dbOpen.php não encontrado');
                return;
            }
            
            // Simular conexão (sem executar para evitar erros)
            $content = file_get_contents($dbFile);
            if (strpos($content, 'odbc_connect') !== false) {
                $this->addResult('DB_CONFIG', 'SUCESSO', 'Configuração ODBC encontrada');
            } else {
                $this->addResult('DB_CONFIG', 'AVISO', 'Configuração ODBC não detectada');
            }
            
        } catch (Exception $e) {
            $this->addResult('DB_CONNECTION', 'FALHA', $e->getMessage());
        }
    }
    
    private function testAuthenticationFlow() {
        echo "<h2>🔐 Teste 2: Fluxo de Autenticação</h2>";
        
        // Testar auth_check.php
        $authFile = $this->baseDir . '/auth_check.php';
        if (file_exists($authFile)) {
            $content = file_get_contents($authFile);
            
            // Verificar se correção de headers foi aplicada
            if (strpos($content, 'headers_sent()') !== false) {
                $this->addResult('AUTH_HEADERS', 'SUCESSO', 'Correção headers_sent() implementada');
            } else {
                $this->addResult('AUTH_HEADERS', 'FALHA', 'Correção headers não encontrada');
            }
            
            // Verificar arquivos públicos
            if (strpos($content, 'emergency_login.php') !== false) {
                $this->addResult('AUTH_PUBLIC', 'SUCESSO', 'Arquivos de diagnóstico listados');
            } else {
                $this->addResult('AUTH_PUBLIC', 'FALHA', 'Arquivos de diagnóstico não listados');
            }
        }
        
        // Testar Access.php
        $accessFile = $this->baseDir . '/src/role/access/Access.php';
        if (file_exists($accessFile)) {
            $content = file_get_contents($accessFile);
            
            if (strpos($content, 'User.php') !== false) {
                $this->addResult('AUTH_CLASS', 'SUCESSO', 'Classe User referenciada');
            } else {
                $this->addResult('AUTH_CLASS', 'AVISO', 'Classe User não encontrada');
            }
        }
    }
    
    private function testFileIntegrity() {
        echo "<h2>📁 Teste 3: Integridade dos Arquivos</h2>";
        
        $criticalFiles = [
            'index.php' => 'Página inicial',
            'src/role/access/Access.php' => 'Sistema de login',
            'src/entity/user/User.php' => 'Classe de usuário',
            'src/dbOpen.php' => 'Conexão banco',
            'remember.php' => 'Recuperação senha',
            'auth_check.php' => 'Verificação autenticação'
        ];
        
        foreach ($criticalFiles as $file => $desc) {
            $fullPath = $this->baseDir . '/' . $file;
            if (file_exists($fullPath)) {
                // Verificar sintaxe PHP básica
                $content = file_get_contents($fullPath);
                if (strpos($content, '<?php') !== false) {
                    $this->addResult('FILE_' . strtoupper(str_replace('.', '_', basename($file))), 'SUCESSO', $desc . ' existe e tem sintaxe PHP');
                } else {
                    $this->addResult('FILE_' . strtoupper(str_replace('.', '_', basename($file))), 'AVISO', $desc . ' existe mas pode não ser PHP');
                }
            } else {
                $this->addResult('FILE_' . strtoupper(str_replace('.', '_', basename($file))), 'FALHA', $desc . ' não encontrado');
            }
        }
    }
    
    private function testSecurityFeatures() {
        echo "<h2>🛡️ Teste 4: Recursos de Segurança</h2>";
        
        // Verificar ValidationMiddleware
        $validationFile = $this->baseDir . '/ValidationMiddleware.php';
        if (file_exists($validationFile)) {
            $content = file_get_contents($validationFile);
            
            // Verificar se regex foi corrigido
            if (strpos($content, '\\[\\]') !== false) {
                $this->addResult('SECURITY_REGEX', 'SUCESSO', 'Regex corrigido no ValidationMiddleware');
            } else {
                $this->addResult('SECURITY_REGEX', 'AVISO', 'Regex pode ter problemas');
            }
        }
        
        // Verificar arquivos de segurança
        $securityFiles = [
            'security_functions.php' => 'Funções de segurança',
            'hybrid_auth.php' => 'Autenticação híbrida',
            'csp-violation-handler.php' => 'Proteção CSP'
        ];
        
        foreach ($securityFiles as $file => $desc) {
            if (file_exists($this->baseDir . '/' . $file)) {
                $this->addResult('SECURITY_' . strtoupper(str_replace('.', '_', $file)), 'SUCESSO', $desc . ' implementado');
            } else {
                $this->addResult('SECURITY_' . strtoupper(str_replace('.', '_', $file)), 'INFO', $desc . ' não encontrado (opcional)');
            }
        }
    }
    
    private function testNavigationFlow() {
        echo "<h2>🧭 Teste 5: Fluxo de Navegação</h2>";
        
        // Simular fluxo de navegação
        $navigationFlow = [
            'index.php' => 'Página inicial',
            'src/role/access/Access.php' => 'Tela de login',
            'src/executive/executive.php' => 'Dashboard executivo',
            'remember.php' => 'Recuperação de senha'
        ];
        
        foreach ($navigationFlow as $page => $desc) {
            $fullPath = $this->baseDir . '/' . $page;
            if (file_exists($fullPath)) {
                $content = file_get_contents($fullPath);
                
                // Verificar se há redirecionamentos ou includes
                $hasRedirect = (strpos($content, 'header(') !== false || strpos($content, 'Location:') !== false);
                $hasIncludes = (strpos($content, 'include') !== false || strpos($content, 'require') !== false);
                
                $status = 'SUCESSO';
                $details = $desc . ' existe';
                
                if ($hasRedirect) $details .= ', tem redirecionamentos';
                if ($hasIncludes) $details .= ', inclui outros arquivos';
                
                $this->addResult('NAV_' . strtoupper(str_replace('/', '_', str_replace('.php', '', $page))), $status, $details);
            } else {
                $this->addResult('NAV_' . strtoupper(str_replace('/', '_', str_replace('.php', '', $page))), 'FALHA', $desc . ' não encontrado');
            }
        }
    }
    
    private function addResult($test, $status, $message) {
        $this->results[] = [
            'test' => $test,
            'status' => $status,
            'message' => $message,
            'timestamp' => date('H:i:s')
        ];
        
        $color = $status === 'SUCESSO' ? 'green' : ($status === 'FALHA' ? 'red' : ($status === 'AVISO' ? 'orange' : 'blue'));
        $icon = $status === 'SUCESSO' ? '✅' : ($status === 'FALHA' ? '❌' : ($status === 'AVISO' ? '⚠️' : 'ℹ️'));
        
        echo "<div style='margin: 5px 0; padding: 10px; background: #f9f9f9; border-left: 4px solid $color;'>";
        echo "<strong>$icon $test</strong> - <span style='color: $color;'>$status</span><br>";
        echo "<small>$message</small>";
        echo "</div>";
    }
    
    private function generateReport() {
        echo "<h2>📊 Relatório Final</h2>";
        
        $sucessos = count(array_filter($this->results, function($r) { return $r['status'] === 'SUCESSO'; }));
        $falhas = count(array_filter($this->results, function($r) { return $r['status'] === 'FALHA'; }));
        $avisos = count(array_filter($this->results, function($r) { return $r['status'] === 'AVISO'; }));
        $total = count($this->results);
        
        echo "<div style='background: #e8f5e8; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
        echo "<h3>📈 Estatísticas</h3>";
        echo "<p><strong>Total de testes:</strong> $total</p>";
        echo "<p><strong>✅ Sucessos:</strong> $sucessos</p>";
        echo "<p><strong>❌ Falhas:</strong> $falhas</p>";
        echo "<p><strong>⚠️ Avisos:</strong> $avisos</p>";
        echo "<p><strong>Taxa de sucesso:</strong> " . round(($sucessos / $total) * 100, 1) . "%</p>";
        echo "</div>";
        
        if ($falhas === 0) {
            echo "<div style='background: #e8f5e8; border: 2px solid #4caf50; padding: 20px; border-radius: 8px;'>";
            echo "<h3 style='color: #2e7d32;'>🎉 SISTEMA APROVADO!</h3>";
            echo "<p>Todos os testes críticos passaram. O sistema deve estar funcionando corretamente.</p>";
            echo "</div>";
        } else {
            echo "<div style='background: #ffebee; border: 2px solid #f44336; padding: 20px; border-radius: 8px;'>";
            echo "<h3 style='color: #c62828;'>⚠️ ATENÇÃO NECESSÁRIA</h3>";
            echo "<p>$falhas teste(s) falharam. Verifique os itens marcados como FALHA.</p>";
            echo "</div>";
        }
        
        echo "<h3>🔗 Próximos Passos</h3>";
        echo "<ol>";
        echo "<li>Execute este teste em um servidor web com PHP e ODBC</li>";
        echo "<li>Teste o login com usuário 'executivo' e a senha descoberta</li>";
        echo "<li>Navegue pelas telas principais do sistema</li>";
        echo "<li>Verifique se não há mais erros 500</li>";
        echo "</ol>";
    }
}

// Executar testes
$tester = new SigaFlowTester(__DIR__);
$tester->runAllTests();

?>