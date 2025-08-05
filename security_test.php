<?php
/**
 * Script de Teste das Implementações de Segurança SIGA
 * 
 * Este script executa testes básicos para verificar se todas as
 * funcionalidades de segurança estão funcionando corretamente.
 * 
 * IMPORTANTE: Execute apenas em ambiente de desenvolvimento/teste!
 */

require_once __DIR__ . '/session_config.php';
require_once __DIR__ . '/security_functions.php';
require_once __DIR__ . '/hybrid_auth.php';
require_once __DIR__ . '/secure_password_recovery.php';

// Verificar se é ambiente de desenvolvimento
if (!defined('ENV') || ENV !== 'development') {
    die('Este script só pode ser executado em ambiente de desenvolvimento!');
}

echo "<h1>Teste das Implementações de Segurança SIGA</h1>\n";
echo "<pre>\n";

$tests_passed = 0;
$tests_failed = 0;

function test($description, $result) {
    global $tests_passed, $tests_failed;
    
    if ($result) {
        echo "✅ PASSOU: $description\n";
        $tests_passed++;
    } else {
        echo "❌ FALHOU: $description\n";
        $tests_failed++;
    }
}

echo "=== TESTANDO SISTEMA HÍBRIDO DE AUTENTICAÇÃO ===\n\n";

// Teste 1: Geração de hash BCRYPT
$password = "TestPassword123!";
$hash = hybrid_password_hash($password);
test("Geração de hash BCRYPT", str_starts_with($hash, '$2y$'));

// Teste 2: Verificação de senha BCRYPT
$verify_result = hybrid_password_verify($password, $hash);
test("Verificação de senha BCRYPT", $verify_result === true);

// Teste 3: Senha incorreta deve falhar
$wrong_verify = hybrid_password_verify("WrongPassword", $hash);
test("Senha incorreta rejeitada", $wrong_verify === false);

// Teste 4: Hash precisa de atualização (simulando hash antigo)
$old_hash = crypt($password, '$2a$07$usesomesillystringforsalt$');
$needs_rehash = hybrid_password_needs_rehash($old_hash);
test("Detecção de hash antigo", $needs_rehash === true);

// Teste 5: Hash novo não precisa atualização
$no_rehash = hybrid_password_needs_rehash($hash);
test("Hash novo não precisa atualização", $no_rehash === false);

echo "\n=== TESTANDO PROTEÇÃO CSRF ===\n\n";

// Teste 6: Geração de token CSRF
$token = generate_csrf_token();
test("Geração de token CSRF", !empty($token) && strlen($token) === 64);

// Teste 7: Validação de token CSRF
$token_valid = validate_csrf_token($token);
test("Validação de token CSRF", $token_valid === true);

// Teste 8: Token inválido rejeitado
$invalid_token = validate_csrf_token("invalid_token");
test("Token inválido rejeitado", $invalid_token === false);

echo "\n=== TESTANDO SANITIZAÇÃO ===\n\n";

// Teste 9: Sanitização HTML
$dirty = "<script>alert('xss')</script>Hello";
$clean = safe_output($dirty, 'html');
test("Sanitização XSS básica", $clean === "&lt;script&gt;alert('xss')&lt;/script&gt;Hello");

// Teste 10: Sanitização de URL
$url_dirty = "javascript:alert('xss')";
$url_clean = safe_output($url_dirty, 'url');
test("Sanitização de URL", $url_clean !== $url_dirty);

// Teste 11: Validação de entrada
$valid_email = validate_input("test@example.com", 'email');
test("Validação de email válido", $valid_email === "test@example.com");

$invalid_email = validate_input("not-an-email", 'email');
test("Rejeição de email inválido", $invalid_email === null);

echo "\n=== TESTANDO RECUPERAÇÃO DE SENHA ===\n\n";

// Teste 12: Geração de token de recuperação
$recovery_token = generate_recovery_token("test@example.com", 123);
test("Geração de token de recuperação", !empty($recovery_token));

// Teste 13: Validação de token de recuperação
$token_data = validate_recovery_token($recovery_token);
test("Validação de token de recuperação", $token_data !== false && $token_data['email'] === "test@example.com");

// Teste 14: Token expirado (simulado)
$expired_token_data = [
    'email' => 'test@example.com',
    'user_id' => 123,
    'timestamp' => time() - 1000, // 1000 segundos atrás
    'random' => 'test'
];
$expired_token = base64_encode(json_encode($expired_token_data));
$expired_result = validate_recovery_token($expired_token);
test("Token expirado rejeitado", $expired_result === false);

echo "\n=== TESTANDO DETECÇÃO XSS ===\n\n";

// Teste 15: Detecção de script
$xss_script = "<script>alert('xss')</script>";
test("Detecção de script XSS", is_xss_attempt($xss_script) === true);

// Teste 16: Detecção de javascript:
$xss_js = "javascript:alert('xss')";
test("Detecção de javascript: XSS", is_xss_attempt($xss_js) === true);

// Teste 17: Texto normal não é XSS
$normal_text = "This is normal text";
test("Texto normal não é XSS", is_xss_attempt($normal_text) === false);

echo "\n=== TESTANDO FUNÇÃO DE SESSÃO ===\n\n";

// Teste 18: Criação de fingerprint
$fingerprint = create_session_fingerprint();
test("Criação de fingerprint de sessão", !empty($fingerprint) && strlen($fingerprint) === 64);

// Teste 19: Fingerprints consistentes
$fingerprint2 = create_session_fingerprint();
test("Fingerprints consistentes", $fingerprint === $fingerprint2);

echo "\n=== TESTANDO VALIDAÇÃO DE ENTRADA ===\n\n";

// Teste 20: Validação de inteiro
$valid_int = validate_input("123", 'int');
test("Validação de inteiro válido", $valid_int === 123);

$invalid_int = validate_input("abc", 'int');
test("Rejeição de inteiro inválido", $invalid_int === null);

// Teste 21: Limite de tamanho
$long_string = str_repeat("a", 300);
$truncated = validate_input($long_string, 'string', 255);
test("Limite de tamanho respeitado", $truncated === null);

echo "\n=== TESTANDO RATE LIMITING (simulado) ===\n\n";

// Para teste de rate limiting, vamos simular com dados
if (!is_dir(SECURITY_LOG_DIR)) {
    mkdir(SECURITY_LOG_DIR, 0755, true);
}

// Limpar logs de teste
$test_log = SECURITY_LOG_DIR . '/login_attempts_' . date('Y-m-d') . '.log';
if (file_exists($test_log)) {
    unlink($test_log);
}

// Simular tentativas falhadas
for ($i = 0; $i < 6; $i++) {
    log_login_attempt('192.168.1.100', 'testuser', false);
}

$failed_count = count_failed_attempts_by_ip('192.168.1.100');
test("Contagem de tentativas falhadas por IP", $failed_count >= 5);

$is_blocked = is_ip_blocked('192.168.1.100');
test("IP bloqueado após muitas tentativas", $is_blocked === true);

// Limpar log de teste
if (file_exists($test_log)) {
    unlink($test_log);
}

echo "\n=== RESULTADOS FINAIS ===\n\n";
echo "Testes Passaram: $tests_passed\n";
echo "Testes Falharam: $tests_failed\n";
echo "Total: " . ($tests_passed + $tests_failed) . "\n";

if ($tests_failed === 0) {
    echo "\n🎉 TODOS OS TESTES PASSARAM! Sistema de segurança funcionando corretamente.\n";
} else {
    echo "\n⚠️  ALGUNS TESTES FALHARAM! Verifique as implementações.\n";
}

echo "\n=== VERIFICAÇÕES ADICIONAIS ===\n\n";

// Verificar se diretórios existem
echo "Verificando estrutura de arquivos:\n";
echo "- security_functions.php: " . (file_exists(__DIR__ . '/security_functions.php') ? "✅" : "❌") . "\n";
echo "- hybrid_auth.php: " . (file_exists(__DIR__ . '/hybrid_auth.php') ? "✅" : "❌") . "\n";
echo "- secure_password_recovery.php: " . (file_exists(__DIR__ . '/secure_password_recovery.php') ? "✅" : "❌") . "\n";
echo "- session_config.php: " . (file_exists(__DIR__ . '/session_config.php') ? "✅" : "❌") . "\n";
echo "- reset_password.php: " . (file_exists(__DIR__ . '/reset_password.php') ? "✅" : "❌") . "\n";
echo "- recover_password.php: " . (file_exists(__DIR__ . '/recover_password.php') ? "✅" : "❌") . "\n";
echo "- security_dashboard.php: " . (file_exists(__DIR__ . '/security_dashboard.php') ? "✅" : "❌") . "\n";

echo "\nVerificando diretório de logs:\n";
echo "- Diretório logs/security: " . (is_dir(SECURITY_LOG_DIR) ? "✅" : "❌") . "\n";
echo "- Permissões de escrita: " . (is_writable(SECURITY_LOG_DIR) ? "✅" : "❌") . "\n";

echo "\nVerificando configurações PHP:\n";
echo "- password_hash disponível: " . (function_exists('password_hash') ? "✅" : "❌") . "\n";
echo "- random_bytes disponível: " . (function_exists('random_bytes') ? "✅" : "❌") . "\n";
echo "- Session configurada: " . (session_status() === PHP_SESSION_ACTIVE ? "✅" : "❌") . "\n";

echo "\n</pre>";

?>

<style>
body {
    font-family: 'Courier New', monospace;
    background: #f5f5f5;
    margin: 20px;
}

h1 {
    color: #333;
    border-bottom: 2px solid #007bff;
    padding-bottom: 10px;
}

pre {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    line-height: 1.5;
}
</style>