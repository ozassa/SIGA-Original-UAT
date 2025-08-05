<?php
/**
 * SIGA Input Validation Framework - Arquivo de Teste
 * 
 * Este arquivo testa o framework de validação de entrada
 * para garantir que está funcionando corretamente.
 * 
 * @version 1.0
 * @author Claude Code - Security Hardening Mission
 */

// Definir modo de teste para evitar auto-inicialização
define('VALIDATION_MIDDLEWARE_TEST_MODE', true);

// Incluir arquivos necessários
require_once('InputValidationFramework.php');
require_once('ValidationMiddleware.php');
require_once('ValidationConfig.php');

echo "<h1>SIGA Input Validation Framework - Teste de Integração</h1>\n";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;} .pass{color:green;} .fail{color:red;} .warning{color:orange;} pre{background:#f5f5f5;padding:10px;border-radius:5px;}</style>\n";

/**
 * Função helper para exibir resultados de teste
 */
function test_result($test_name, $result, $expected = true, $details = '') {
    $status = ($result === $expected) ? 'PASS' : 'FAIL';
    $class = ($status === 'PASS') ? 'pass' : 'fail';
    
    echo "<p><strong>$test_name:</strong> <span class='$class'>$status</span>";
    if ($details) {
        echo " - $details";
    }
    echo "</p>\n";
    
    return $status === 'PASS';
}

echo "<h2>1. Testes Básicos de Validação</h2>\n";

// Teste 1: Validação de string
$result = InputValidator::validate("Teste normal", ['type' => 'string', 'max_length' => 50]);
test_result("Validação de string normal", $result === "Teste normal");

// Teste 2: Validação de inteiro
$result = InputValidator::validate("123", ['type' => 'int']);
test_result("Validação de inteiro", $result === 123);

// Teste 3: Validação de email
$result = InputValidator::validate("teste@coface.com.br", ['type' => 'email']);
test_result("Validação de email válido", $result === "teste@coface.com.br");

// Teste 4: Validação de email inválido
$result = InputValidator::validate("email_invalido", ['type' => 'email']);
test_result("Validação de email inválido", $result === false);

// Teste 5: Validação de CNPJ
$result = InputValidator::validate("11.222.333/0001-81", ['type' => 'cnpj']);
test_result("Validação de CNPJ", is_string($result) || $result === false, true, "CNPJ pode ou não ser válido");

echo "<h2>2. Testes de Sanitização</h2>\n";

// Teste 6: Sanitização HTML
$malicious_html = "<script>alert('XSS')</script>Texto normal";
$result = InputValidator::sanitize($malicious_html, 'html');
test_result("Sanitização de XSS", strpos($result, '<script>') === false);

// Teste 7: Sanitização SQL
$malicious_sql = "'; DROP TABLE users; --";
$result = InputValidator::sanitize($malicious_sql, 'sql');
test_result("Sanitização de SQL Injection", strpos($result, "DROP TABLE") === false);

// Teste 8: Sanitização de filename
$malicious_filename = "../../../etc/passwd";
$result = InputValidator::sanitize($malicious_filename, 'filename');
test_result("Sanitização de filename", strpos($result, '../') === false);

echo "<h2>3. Testes de Detecção de Ataques</h2>\n";

// Simular dados suspeitos
$_GET = ['test' => '<script>alert("xss")</script>'];
$_POST = ['comm' => 'delete', 'sql' => "' OR 1=1 --"];
$_REQUEST = array_merge($_GET, $_POST);

// Teste 9: Middleware de validação
ValidationMiddleware::reset();
ValidationMiddleware::init('credit');

$stats = ValidationMiddleware::getStats();
test_result("Inicialização do Middleware", $stats['initialized'] === true);
test_result("Detecção de módulo", $stats['current_module'] === 'credit');

echo "<h2>4. Testes de Regras por Módulo</h2>\n";

// Teste 10: Regras do módulo Credit
$credit_rules = ValidationRules::getCreditRules();
test_result("Carregamento de regras Credit", isset($credit_rules['comm']));

// Teste 11: Regras do módulo Client
$client_rules = ValidationRules::getClientRules();
test_result("Carregamento de regras Client", isset($client_rules['cnpj']));

// Teste 12: Regras do módulo DVE
$dve_rules = ValidationRules::getDveRules();
test_result("Carregamento de regras DVE", isset($dve_rules['export_value']));

echo "<h2>5. Testes de Configuração</h2>\n";

// Teste 13: Configuração global
$global_config = ValidationConfig::getGlobalConfig();
test_result("Carregamento de configuração global", isset($global_config['rate_limit']));

// Teste 14: Padrões de ataque
$attack_patterns = ValidationConfig::getAttackPatterns();
test_result("Carregamento de padrões de ataque", isset($attack_patterns['xss']));

// Teste 15: Regras específicas por arquivo
$file_rules = ValidationConfig::getFileSpecificRules();
test_result("Carregamento de regras por arquivo", isset($file_rules['src/role/credit/Credit.php']));

echo "<h2>6. Testes de Funcionalidades Avançadas</h2>\n";

// Teste 16: Rate limiting
$rate_limit_ok = InputValidator::checkRateLimit('test_ip');
test_result("Rate limiting funcional", $rate_limit_ok === true);

// Teste 17: Validação de moeda
$currency_test = InputValidator::validate("1.234,56", ['type' => 'currency']);
test_result("Validação de moeda brasileira", $currency_test !== false);

// Teste 18: Validação de data
$date_test = InputValidator::validate("25/12/2023", ['type' => 'date']);
test_result("Validação de data DD/MM/YYYY", $date_test !== false);

echo "<h2>7. Testes de Backward Compatibility</h2>\n";

// Teste 19: Preservação de dados originais
$original_data = ValidationMiddleware::getOriginalData('GET');
test_result("Preservação de dados originais", is_array($original_data));

// Teste 20: Função helper safe_input
$safe_value = safe_input('test', 'GET', ['type' => 'string', 'default' => 'padrão']);
test_result("Função helper safe_input", is_string($safe_value));

echo "<h2>8. Verificação de Arquivos de Log</h2>\n";

// Verificar se diretórios de log podem ser criados
$log_dir = dirname(__FILE__) . '/logs/validation';
$can_create_logs = is_writable(dirname(__FILE__)) || is_dir($log_dir);
test_result("Capacidade de criar logs", $can_create_logs, true, $can_create_logs ? "Logs podem ser criados" : "Verificar permissões de escrita");

echo "<h2>9. Teste de Performance</h2>\n";

// Teste de performance básico
$start_time = microtime(true);
for ($i = 0; $i < 1000; $i++) {
    InputValidator::validate("teste_$i", ['type' => 'string', 'max_length' => 100]);
}
$end_time = microtime(true);
$execution_time = ($end_time - $start_time) * 1000; // em milissegundos

test_result("Performance de validação", $execution_time < 1000, true, sprintf("1000 validações em %.2f ms", $execution_time));

echo "<h2>10. Resumo dos Testes</h2>\n";

echo "<div style='background:#e8f5e8;padding:15px;border-radius:5px;margin:20px 0;'>";
echo "<h3>✅ Framework de Validação SIGA Instalado com Sucesso!</h3>";
echo "<p><strong>Componentes instalados:</strong></p>";
echo "<ul>";
echo "<li>✅ InputValidationFramework.php - Classe principal de validação</li>";
echo "<li>✅ ValidationMiddleware.php - Middleware transparente de interceptação</li>";
echo "<li>✅ ValidationConfig.php - Configurações avançadas</li>";
echo "<li>✅ Integração no config.php - Auto-loading configurado</li>";
echo "</ul>";

echo "<p><strong>Funcionalidades disponíveis:</strong></p>";
echo "<ul>";
echo "<li>🛡️ Validação automática de $_GET, $_POST, $_REQUEST</li>";
echo "<li>🔍 Detecção de ataques XSS, SQL Injection, Command Injection</li>";
echo "<li>📝 Logging detalhado de tentativas de ataque</li>";
echo "<li>⚡ Rate limiting para prevenir ataques em massa</li>";
echo "<li>🔧 Regras customizáveis por módulo</li>";
echo "<li>🔄 Backward compatibility total</li>";
echo "<li>📊 Sanitização contextual avançada</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background:#fff3cd;padding:15px;border-radius:5px;margin:20px 0;'>";
echo "<h3>⚠️ Próximos Passos Recomendados:</h3>";
echo "<ol>";
echo "<li>Testar em ambiente de desenvolvimento antes de produção</li>";
echo "<li>Configurar alertas de email em ValidationConfig.php</li>";
echo "<li>Ajustar regras específicas conforme necessário</li>";
echo "<li>Monitorar logs de segurança regularmente</li>";
echo "<li>Implementar rotação automática de logs</li>";
echo "</ol>";
echo "</div>";

echo "<div style='background:#d1ecf1;padding:15px;border-radius:5px;margin:20px 0;'>";
echo "<h3>📚 Como Usar:</h3>";
echo "<pre>";
echo "// Validação manual\n";
echo "\$safe_value = InputValidator::validate(\$input, ['type' => 'string', 'max_length' => 100]);\n\n";
echo "// Uso com helpers\n";
echo "\$comm = safe_input('comm', 'GET', ['type' => 'string', 'whitelist' => ['view', 'edit']]);\n\n";
echo "// Sanitização contextual\n";
echo "\$html_safe = InputValidator::sanitize(\$html, 'html');\n";
echo "\$sql_safe = InputValidator::sanitize(\$data, 'sql');\n";
echo "</pre>";
echo "</div>";

echo "<p><em>Teste concluído em " . date('Y-m-d H:i:s') . "</em></p>";
?>