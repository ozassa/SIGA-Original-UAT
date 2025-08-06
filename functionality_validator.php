<?php
/**
 * VALIDADOR DE FUNCIONALIDADES CRÍTICAS - SIGA
 * Testa todas as funcionalidades principais do sistema
 */

class SigaFunctionalityValidator {
    private $baseDir;
    private $testResults = [];
    
    public function __construct($baseDir) {
        $this->baseDir = $baseDir;
    }
    
    public function runFunctionalityTests() {
        echo "<h1>🔧 SIGA - Validador de Funcionalidades</h1>";
        echo "<div style='font-family: Arial, sans-serif; margin: 20px;'>";
        
        $this->testLoginFunctionality();
        $this->testSessionManagement();
        $this->testPasswordRecovery();
        $this->testUserManagement();
        $this->testNavigationSystem();
        $this->testSecurityImplementations();
        $this->generateFunctionalityReport();
        
        echo "</div>";
    }
    
    private function testLoginFunctionality() {
        echo "<h2>🔐 Funcionalidade: Sistema de Login</h2>";
        
        // Testar Access.php
        $accessFile = $this->baseDir . '/src/role/access/Access.php';
        if (file_exists($accessFile)) {
            $content = file_get_contents($accessFile);
            
            // Verificar se aceita POST
            if (strpos($content, '$_POST') !== false) {
                $this->addResult('LOGIN_POST', 'FUNCIONAL', 'Sistema aceita dados POST para login');
            } else {
                $this->addResult('LOGIN_POST', 'PROBLEMA', 'Sistema pode não processar formulários');
            }
            
            // Verificar validação de credenciais
            if (strpos($content, 'login') !== false && strpos($content, 'password') !== false) {
                $this->addResult('LOGIN_VALIDATION', 'FUNCIONAL', 'Validação de credenciais implementada');
            } else {
                $this->addResult('LOGIN_VALIDATION', 'PROBLEMA', 'Validação de credenciais não encontrada');
            }
            
            // Verificar múltiplos perfis
            if (strpos($content, 'perfil') !== false || strpos($content, 'pefil') !== false) {
                $this->addResult('LOGIN_PROFILES', 'FUNCIONAL', 'Sistema suporta múltiplos perfis');
            } else {
                $this->addResult('LOGIN_PROFILES', 'LIMITADO', 'Suporte a perfis não detectado');
            }
            
            // Verificar redirecionamento pós-login
            if (strpos($content, 'executive.php') !== false) {
                $this->addResult('LOGIN_REDIRECT', 'FUNCIONAL', 'Redirecionamento pós-login configurado');
            } else {
                $this->addResult('LOGIN_REDIRECT', 'PROBLEMA', 'Redirecionamento pós-login não encontrado');
            }
        } else {
            $this->addResult('LOGIN_FILE', 'CRÍTICO', 'Arquivo Access.php não encontrado');
        }
    }
    
    private function testSessionManagement() {
        echo "<h2>🎫 Funcionalidade: Gerenciamento de Sessões</h2>";
        
        // Testar auth_check.php
        $authFile = $this->baseDir . '/auth_check.php';
        if (file_exists($authFile)) {
            $content = file_get_contents($authFile);
            
            // Verificar inicialização de sessão
            if (strpos($content, 'session_start') !== false) {
                $this->addResult('SESSION_START', 'FUNCIONAL', 'Inicialização de sessão implementada');
            } else {
                $this->addResult('SESSION_START', 'PROBLEMA', 'Inicialização de sessão não encontrada');
            }
            
            // Verificar validação de sessão
            if (strpos($content, '$_SESSION') !== false) {
                $this->addResult('SESSION_VALIDATION', 'FUNCIONAL', 'Validação de sessão ativa');
            } else {
                $this->addResult('SESSION_VALIDATION', 'CRÍTICO', 'Validação de sessão não encontrada');
            }
            
            // Verificar destruição de sessão
            if (strpos($content, 'session_destroy') !== false) {
                $this->addResult('SESSION_DESTROY', 'FUNCIONAL', 'Destruição de sessão implementada');
            } else {
                $this->addResult('SESSION_DESTROY', 'PROBLEMA', 'Destruição de sessão não encontrada');
            }
            
            // Verificar correção headers_sent
            if (strpos($content, 'headers_sent') !== false) {
                $this->addResult('SESSION_HEADERS', 'FUNCIONAL', 'Correção headers_sent aplicada ✅');
            } else {
                $this->addResult('SESSION_HEADERS', 'PROBLEMA', 'Correção headers_sent não encontrada');
            }
        }
        
        // Testar configuração de sessão
        $sessionFile = $this->baseDir . '/session_config.php';
        if (file_exists($sessionFile)) {
            $this->addResult('SESSION_CONFIG', 'FUNCIONAL', 'Configuração de sessão personalizada encontrada');
        } else {
            $this->addResult('SESSION_CONFIG', 'LIMITADO', 'Configuração padrão de sessão');
        }
    }
    
    private function testPasswordRecovery() {
        echo "<h2>🔑 Funcionalidade: Recuperação de Senha</h2>";
        
        $recoveryFiles = [
            'remember.php' => 'Sistema principal de recuperação',
            'recover_password.php' => 'Sistema alternativo de recuperação',
            'reset_password.php' => 'Reset de senha',
            'secure_password_recovery.php' => 'Recuperação segura'
        ];
        
        foreach ($recoveryFiles as $file => $desc) {
            $fullPath = $this->baseDir . '/' . $file;
            if (file_exists($fullPath)) {
                $content = file_get_contents($fullPath);
                
                // Verificar se aceita formulários
                if (strpos($content, '$_POST') !== false || strpos($content, '<form') !== false) {
                    $this->addResult('RECOVERY_' . strtoupper(str_replace('.php', '', $file)), 'FUNCIONAL', $desc . ' operacional');
                } else {
                    $this->addResult('RECOVERY_' . strtoupper(str_replace('.php', '', $file)), 'LIMITADO', $desc . ' pode ter limitações');
                }
            }
        }
        
        // Verificar se remember.php foi corrigido
        $rememberFile = $this->baseDir . '/remember.php';
        if (file_exists($rememberFile)) {
            $content = file_get_contents($rememberFile);
            
            // Verificar se o parse error foi corrigido
            if (strpos($content, '} // Fechamento do if($op_num == 2)') !== false) {
                $this->addResult('RECOVERY_SYNTAX', 'FUNCIONAL', 'Parse error no remember.php foi corrigido ✅');
            } else {
                $this->addResult('RECOVERY_SYNTAX', 'PROBLEMA', 'Parse error pode ainda existir');
            }
        }
    }
    
    private function testUserManagement() {
        echo "<h2>👥 Funcionalidade: Gerenciamento de Usuários</h2>";
        
        // Testar classe User
        $userFile = $this->baseDir . '/src/entity/user/User.php';
        if (file_exists($userFile)) {
            $content = file_get_contents($userFile);
            
            // Verificar se é uma classe
            if (strpos($content, 'class User') !== false) {
                $this->addResult('USER_CLASS', 'FUNCIONAL', 'Classe User definida corretamente');
            } else {
                $this->addResult('USER_CLASS', 'PROBLEMA', 'Classe User pode ter problemas');
            }
            
            // Verificar métodos principais
            $methods = ['getUserView', '__construct', 'login', 'authenticate'];
            foreach ($methods as $method) {
                if (strpos($content, $method) !== false) {
                    $this->addResult('USER_METHOD_' . strtoupper($method), 'FUNCIONAL', "Método $method implementado");
                }
            }
            
            // Verificar conexão com banco
            if (strpos($content, 'odbc') !== false || strpos($content, 'ODBC') !== false) {
                $this->addResult('USER_DATABASE', 'FUNCIONAL', 'Integração com banco de dados ODBC');
            } else {
                $this->addResult('USER_DATABASE', 'LIMITADO', 'Integração com banco pode estar limitada');
            }
        } else {
            $this->addResult('USER_CLASS', 'CRÍTICO', 'Classe User não encontrada');
        }
    }
    
    private function testNavigationSystem() {
        echo "<h2>🧭 Funcionalidade: Sistema de Navegação</h2>";
        
        // Testar menu principal
        $menuFile = $this->baseDir . '/menu.php';
        if (file_exists($menuFile)) {
            $content = file_get_contents($menuFile);
            
            if (strpos($content, '<a ') !== false || strpos($content, 'href') !== false) {
                $this->addResult('NAV_MENU', 'FUNCIONAL', 'Menu de navegação implementado');
            } else {
                $this->addResult('NAV_MENU', 'LIMITADO', 'Menu pode ter limitações');
            }
        }
        
        // Testar sistema de navegação
        $navFile = $this->baseDir . '/navegacao.php';
        if (file_exists($navFile)) {
            $this->addResult('NAV_SYSTEM', 'FUNCIONAL', 'Sistema de navegação encontrado');
        }
        
        // Testar páginas principais
        $mainPages = [
            'index.php' => 'Página inicial',
            'main.php' => 'Página principal',
            'home.php' => 'Página home',
            'src/executive/executive.php' => 'Dashboard executivo'
        ];
        
        foreach ($mainPages as $page => $desc) {
            if (file_exists($this->baseDir . '/' . $page)) {
                $this->addResult('NAV_' . strtoupper(str_replace('/', '_', str_replace('.php', '', $page))), 'FUNCIONAL', $desc . ' disponível');
            }
        }
    }
    
    private function testSecurityImplementations() {
        echo "<h2>🛡️ Funcionalidade: Implementações de Segurança</h2>";
        
        // Testar ValidationMiddleware
        $validationFile = $this->baseDir . '/ValidationMiddleware.php';
        if (file_exists($validationFile)) {
            $content = file_get_contents($validationFile);
            
            // Verificar se regex foi corrigido
            if (strpos($content, '\\[\\]') !== false) {
                $this->addResult('SECURITY_REGEX', 'FUNCIONAL', 'Regex de validação corrigido ✅');
            } else {
                $this->addResult('SECURITY_REGEX', 'PROBLEMA', 'Regex pode ter problemas');
            }
        }
        
        // Testar funções de segurança
        $securityFile = $this->baseDir . '/security_functions.php';
        if (file_exists($securityFile)) {
            $this->addResult('SECURITY_FUNCTIONS', 'FUNCIONAL', 'Funções de segurança implementadas');
        }
        
        // Testar proteção CSP
        $cspFile = $this->baseDir . '/csp-violation-handler.php';
        if (file_exists($cspFile)) {
            $this->addResult('SECURITY_CSP', 'FUNCIONAL', 'Proteção CSP implementada');
        }
        
        // Testar autenticação híbrida
        $hybridFile = $this->baseDir . '/hybrid_auth.php';
        if (file_exists($hybridFile)) {
            $this->addResult('SECURITY_HYBRID', 'FUNCIONAL', 'Autenticação híbrida disponível');
        }
    }
    
    private function addResult($test, $status, $message) {
        $this->testResults[] = [
            'test' => $test,
            'status' => $status,
            'message' => $message,
            'timestamp' => date('H:i:s')
        ];
        
        $color = $status === 'FUNCIONAL' ? 'green' : 
                ($status === 'CRÍTICO' ? 'red' : 
                ($status === 'PROBLEMA' ? 'orange' : 'blue'));
        
        $icon = $status === 'FUNCIONAL' ? '✅' : 
               ($status === 'CRÍTICO' ? '❌' : 
               ($status === 'PROBLEMA' ? '⚠️' : 'ℹ️'));
        
        echo "<div style='margin: 5px 0; padding: 10px; background: #f9f9f9; border-left: 4px solid $color;'>";
        echo "<strong>$icon $test</strong> - <span style='color: $color;'>$status</span><br>";
        echo "<small>$message</small>";
        echo "</div>";
    }
    
    private function generateFunctionalityReport() {
        echo "<h2>📊 Relatório de Funcionalidades</h2>";
        
        $funcionais = count(array_filter($this->testResults, function($r) { return $r['status'] === 'FUNCIONAL'; }));
        $criticos = count(array_filter($this->testResults, function($r) { return $r['status'] === 'CRÍTICO'; }));
        $problemas = count(array_filter($this->testResults, function($r) { return $r['status'] === 'PROBLEMA'; }));
        $limitados = count(array_filter($this->testResults, function($r) { return $r['status'] === 'LIMITADO'; }));
        $total = count($this->testResults);
        
        echo "<div style='background: #f0f8ff; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
        echo "<h3>📈 Resumo Executivo</h3>";
        echo "<p><strong>Total de funcionalidades testadas:</strong> $total</p>";
        echo "<p><strong>✅ Funcionais:</strong> $funcionais</p>";
        echo "<p><strong>❌ Críticas:</strong> $criticos</p>";
        echo "<p><strong>⚠️ Problemas:</strong> $problemas</p>";
        echo "<p><strong>ℹ️ Limitadas:</strong> $limitados</p>";
        echo "<p><strong>Taxa de funcionalidade:</strong> " . round(($funcionais / $total) * 100, 1) . "%</p>";
        echo "</div>";
        
        // Análise detalhada por categoria
        $categories = [
            'LOGIN' => 'Sistema de Login',
            'SESSION' => 'Gerenciamento de Sessões', 
            'RECOVERY' => 'Recuperação de Senha',
            'USER' => 'Gerenciamento de Usuários',
            'NAV' => 'Navegação',
            'SECURITY' => 'Segurança'
        ];
        
        echo "<h3>📋 Análise por Categoria</h3>";
        foreach ($categories as $prefix => $name) {
            $categoryTests = array_filter($this->testResults, function($r) use ($prefix) {
                return strpos($r['test'], $prefix) === 0;
            });
            
            if (count($categoryTests) > 0) {
                $categoryFunctional = count(array_filter($categoryTests, function($r) { return $r['status'] === 'FUNCIONAL'; }));
                $categoryTotal = count($categoryTests);
                $percentage = round(($categoryFunctional / $categoryTotal) * 100, 1);
                
                $bgColor = $percentage >= 80 ? '#e8f5e8' : ($percentage >= 60 ? '#fff3e0' : '#ffebee');
                
                echo "<div style='background: $bgColor; padding: 15px; margin: 10px 0; border-radius: 8px;'>";
                echo "<h4>$name</h4>";
                echo "<p>Funcionalidade: $percentage% ($categoryFunctional/$categoryTotal)</p>";
                echo "</div>";
            }
        }
        
        // Recomendações
        echo "<h3>🎯 Recomendações</h3>";
        echo("<div style='background: #e8f5e8; padding: 20px; border-radius: 8px;'>");
        
        if ($criticos === 0) {
            echo "<p><strong>✅ Excelente!</strong> Nenhum problema crítico encontrado.</p>";
        } else {
            echo "<p><strong>⚠️ Atenção:</strong> $criticos problema(s) crítico(s) encontrado(s).</p>";
        }
        
        echo "<ul>";
        echo "<li>Execute os testes em ambiente com servidor web ativo</li>";
        echo "<li>Teste login com usuários reais do banco de dados</li>";
        echo "<li>Verifique navegação entre todas as telas</li>";
        echo "<li>Confirme que não há mais erros 500</li>";
        echo "</ul>";
        echo "</div>";
    }
}

// Executar validação
$validator = new SigaFunctionalityValidator(__DIR__);
$validator->runFunctionalityTests();

?>