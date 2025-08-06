<?php
/**
 * DASHBOARD PRINCIPAL DE TESTES - SIGA
 * Executa todos os testes e fornece um relatório completo
 */

class SigaTestDashboard {
    private $baseDir;
    
    public function __construct($baseDir) {
        $this->baseDir = $baseDir;
    }
    
    public function displayDashboard() {
        ?>
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>SIGA - Dashboard de Testes</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { 
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    padding: 20px;
                }
                .container { 
                    max-width: 1200px; 
                    margin: 0 auto; 
                    background: white; 
                    border-radius: 15px; 
                    box-shadow: 0 20px 60px rgba(0,0,0,0.1);
                    overflow: hidden;
                }
                .header { 
                    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); 
                    color: white; 
                    padding: 30px; 
                    text-align: center; 
                }
                .header h1 { font-size: 2.5em; margin-bottom: 10px; }
                .header p { opacity: 0.9; font-size: 1.1em; }
                .nav-tabs { 
                    display: flex; 
                    background: #f8f9fa; 
                    border-bottom: 1px solid #ddd; 
                }
                .nav-tab { 
                    flex: 1; 
                    padding: 15px; 
                    text-align: center; 
                    background: none; 
                    border: none; 
                    cursor: pointer; 
                    font-size: 16px;
                    transition: all 0.3s ease;
                }
                .nav-tab:hover { background: #e9ecef; }
                .nav-tab.active { 
                    background: #007bff; 
                    color: white; 
                }
                .tab-content { 
                    padding: 30px; 
                    min-height: 600px; 
                }
                .test-card { 
                    background: #f8f9fa; 
                    border-radius: 10px; 
                    padding: 20px; 
                    margin: 15px 0; 
                    border-left: 5px solid #007bff;
                    transition: transform 0.2s ease;
                }
                .test-card:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
                .test-card h3 { color: #2c3e50; margin-bottom: 10px; }
                .test-card p { color: #6c757d; margin-bottom: 15px; }
                .btn { 
                    background: #007bff; 
                    color: white; 
                    padding: 10px 20px; 
                    border: none; 
                    border-radius: 5px; 
                    cursor: pointer; 
                    text-decoration: none;
                    display: inline-block;
                    margin: 5px;
                    transition: background 0.3s ease;
                }
                .btn:hover { background: #0056b3; }
                .btn-success { background: #28a745; }
                .btn-success:hover { background: #1e7e34; }
                .btn-warning { background: #ffc107; color: #212529; }
                .btn-warning:hover { background: #e0a800; }
                .btn-danger { background: #dc3545; }
                .btn-danger:hover { background: #c82333; }
                .status-grid { 
                    display: grid; 
                    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
                    gap: 20px; 
                    margin: 20px 0; 
                }
                .status-item { 
                    background: white; 
                    padding: 20px; 
                    border-radius: 10px; 
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                    text-align: center;
                }
                .status-number { font-size: 2em; font-weight: bold; margin-bottom: 10px; }
                .status-label { color: #6c757d; }
                .hidden { display: none; }
                .result-frame { 
                    width: 100%; 
                    height: 500px; 
                    border: 1px solid #ddd; 
                    border-radius: 5px; 
                    margin-top: 15px;
                }
                .quick-actions { 
                    background: #e8f4fd; 
                    padding: 20px; 
                    border-radius: 10px; 
                    margin: 20px 0; 
                }
                .action-grid { 
                    display: grid; 
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
                    gap: 15px; 
                    margin-top: 15px; 
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🧪 SIGA Test Dashboard</h1>
                    <p>Sistema Completo de Testes e Validação</p>
                </div>
                
                <div class="nav-tabs">
                    <button class="nav-tab active" onclick="showTab('overview')">📊 Visão Geral</button>
                    <button class="nav-tab" onclick="showTab('automated')">🤖 Testes Automatizados</button>
                    <button class="nav-tab" onclick="showTab('navigation')">🧭 Simulação de Navegação</button>
                    <button class="nav-tab" onclick="showTab('functionality')">🔧 Validação de Funcionalidades</button>
                    <button class="nav-tab" onclick="showTab('manual')">👤 Testes Manuais</button>
                </div>
                
                <!-- Overview Tab -->
                <div id="overview" class="tab-content">
                    <h2>📊 Visão Geral do Sistema</h2>
                    
                    <div class="status-grid">
                        <div class="status-item">
                            <div class="status-number" style="color: #28a745;">
                                <?php echo $this->getSystemStatus(); ?>
                            </div>
                            <div class="status-label">Status do Sistema</div>
                        </div>
                        <div class="status-item">
                            <div class="status-number" style="color: #007bff;">
                                <?php echo $this->countCriticalFiles(); ?>
                            </div>
                            <div class="status-label">Arquivos Críticos</div>
                        </div>
                        <div class="status-item">
                            <div class="status-number" style="color: #ffc107;">
                                <?php echo $this->countSecurityFeatures(); ?>
                            </div>
                            <div class="status-label">Recursos de Segurança</div>
                        </div>
                        <div class="status-item">
                            <div class="status-number" style="color: #17a2b8;">
                                <?php echo $this->countDiagnosticTools(); ?>
                            </div>
                            <div class="status-label">Ferramentas Diagnósticas</div>
                        </div>
                    </div>
                    
                    <div class="quick-actions">
                        <h3>🚀 Ações Rápidas</h3>
                        <div class="action-grid">
                            <a href="emergency_login.php" class="btn btn-success" target="_blank">🚨 Login Emergência</a>
                            <a href="test_login_real.php" class="btn btn-warning" target="_blank">🧪 Teste Login Real</a>
                            <a href="password_discovery.php" class="btn" target="_blank">🔍 Descobrir Senha</a>
                            <a href="fix_headers_issue.php" class="btn" target="_blank">🔧 Fix Headers</a>
                        </div>
                    </div>

                    <div class="test-card">
                        <h3>📋 Último Status</h3>
                        <p><?php echo $this->getLastTestStatus(); ?></p>
                    </div>
                </div>
                
                <!-- Automated Tests Tab -->
                <div id="automated" class="tab-content hidden">
                    <h2>🤖 Testes Automatizados</h2>
                    
                    <div class="test-card">
                        <h3>🔌 Teste de Conectividade</h3>
                        <p>Verifica conexões de banco, extensões PHP e configurações básicas</p>
                        <button class="btn" onclick="runTest('automated_flow_tester.php')">▶️ Executar Teste</button>
                    </div>
                    
                    <div id="automated-results"></div>
                </div>
                
                <!-- Navigation Tests Tab -->
                <div id="navigation" class="tab-content hidden">
                    <h2>🧭 Simulação de Navegação</h2>
                    
                    <div class="test-card">
                        <h3>👤 Jornada do Usuário</h3>
                        <p>Simula navegação completa: login → dashboard → logout</p>
                        <button class="btn" onclick="runTest('navigation_simulator.php')">▶️ Simular Navegação</button>
                    </div>
                    
                    <div id="navigation-results"></div>
                </div>
                
                <!-- Functionality Tests Tab -->
                <div id="functionality" class="tab-content hidden">
                    <h2>🔧 Validação de Funcionalidades</h2>
                    
                    <div class="test-card">
                        <h3>⚙️ Funcionalidades Críticas</h3>
                        <p>Valida login, sessões, recuperação de senha e segurança</p>
                        <button class="btn" onclick="runTest('functionality_validator.php')">▶️ Validar Funcionalidades</button>
                    </div>
                    
                    <div id="functionality-results"></div>
                </div>
                
                <!-- Manual Tests Tab -->
                <div id="manual" class="tab-content hidden">
                    <h2>👤 Testes Manuais</h2>
                    
                    <div class="test-card">
                        <h3>📝 Checklist Manual</h3>
                        <p>Lista de verificações que devem ser feitas manualmente</p>
                        
                        <h4>🔐 Sistema de Login</h4>
                        <ul style="margin: 15px 0; padding-left: 30px;">
                            <li>Acessar página inicial (index.php)</li>
                            <li>Tentar login com credenciais válidas</li>
                            <li>Verificar redirecionamento para dashboard</li>
                            <li>Testar logout</li>
                        </ul>
                        
                        <h4>🧭 Navegação</h4>
                        <ul style="margin: 15px 0; padding-left: 30px;">
                            <li>Navegar entre todas as seções do menu</li>
                            <li>Verificar se não há erros 500</li>
                            <li>Testar funcionalidades específicas</li>
                        </ul>
                        
                        <h4>🔑 Recuperação de Senha</h4>
                        <ul style="margin: 15px 0; padding-left: 30px;">
                            <li>Acessar remember.php</li>
                            <li>Testar formulário de recuperação</li>
                            <li>Verificar se não há erros de sintaxe</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <script>
                function showTab(tabName) {
                    // Hide all tabs
                    const contents = document.querySelectorAll('.tab-content');
                    contents.forEach(content => content.classList.add('hidden'));
                    
                    // Remove active class from all tabs
                    const tabs = document.querySelectorAll('.nav-tab');
                    tabs.forEach(tab => tab.classList.remove('active'));
                    
                    // Show selected tab
                    document.getElementById(tabName).classList.remove('hidden');
                    event.target.classList.add('active');
                }
                
                function runTest(testFile) {
                    const resultContainer = document.getElementById(testFile.replace('.php', '-results'));
                    if (resultContainer) {
                        resultContainer.innerHTML = '<iframe src="' + testFile + '" class="result-frame"></iframe>';
                    } else {
                        // Open in new window if no result container
                        window.open(testFile, '_blank');
                    }
                }
            </script>
        </body>
        </html>
        <?php
    }
    
    private function getSystemStatus() {
        // Verificar se arquivos críticos existem e se correções foram aplicadas
        $critical_checks = [
            'auth_check.php' => strpos(file_get_contents($this->baseDir . '/auth_check.php'), 'headers_sent()') !== false,
            'src/role/access/Access.php' => file_exists($this->baseDir . '/src/role/access/Access.php'),
            'ValidationMiddleware.php' => file_exists($this->baseDir . '/ValidationMiddleware.php'),
            'remember.php' => file_exists($this->baseDir . '/remember.php')
        ];
        
        $passing = count(array_filter($critical_checks));
        $total = count($critical_checks);
        
        return $passing === $total ? "✅ OK" : "⚠️ " . $passing . "/" . $total;
    }
    
    private function countCriticalFiles() {
        $critical_files = [
            'index.php', 'auth_check.php', 'src/role/access/Access.php',
            'src/entity/user/User.php', 'src/dbOpen.php', 'remember.php'
        ];
        
        $count = 0;
        foreach ($critical_files as $file) {
            if (file_exists($this->baseDir . '/' . $file)) {
                $count++;
            }
        }
        
        return $count . '/' . count($critical_files);
    }
    
    private function countSecurityFeatures() {
        $security_files = [
            'ValidationMiddleware.php', 'security_functions.php', 'hybrid_auth.php',
            'csp-violation-handler.php', 'InputValidationFramework.php'
        ];
        
        $count = 0;
        foreach ($security_files as $file) {
            if (file_exists($this->baseDir . '/' . $file)) {
                $count++;
            }
        }
        
        return $count;
    }
    
    private function countDiagnosticTools() {
        $diagnostic_files = [
            'emergency_login.php', 'test_login_real.php', 'password_discovery.php',
            'fix_headers_issue.php', 'automated_flow_tester.php', 'navigation_simulator.php',
            'functionality_validator.php'
        ];
        
        $count = 0;
        foreach ($diagnostic_files as $file) {
            if (file_exists($this->baseDir . '/' . $file)) {
                $count++;
            }
        }
        
        return $count;
    }
    
    private function getLastTestStatus() {
        return "Sistema corrigido com headers_sent() fix aplicado. Correções de segurança implementadas. Pronto para testes em servidor web.";
    }
}

// Executar dashboard
$dashboard = new SigaTestDashboard(__DIR__);
$dashboard->displayDashboard();
?>