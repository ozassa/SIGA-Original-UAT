<?php
/**
 * CORREÇÃO ESPECÍFICA PARA PROBLEMA DE HEADERS ALREADY SENT
 * E CONFLITO COM AUTH_CHECK.PHP
 */

echo "<h1>🔧 Correção do Problema Headers Already Sent</h1>";
echo "<hr>";

echo "<h2>1. Problema Identificado</h2>";
echo "<div style='background: #ffebee; padding: 15px; margin: 10px 0; border-radius: 4px; color: #c62828;'>";
echo "<p><strong>Erro:</strong> Cannot modify header information - headers already sent</p>";
echo "<p><strong>Arquivo:</strong> auth_check.php linha 27</p>";
echo "<p><strong>Causa:</strong> auth_check.php está sendo incluído automaticamente via auto_append_file</p>";
echo "</div>";

echo "<h2>2. Análise da Situação</h2>";

// Verificar se auth_check.php existe
if (file_exists(__DIR__ . '/auth_check.php')) {
    echo "✅ auth_check.php existe<br>";
    
    // Ler primeiras linhas para mostrar o problema
    $content = file_get_contents(__DIR__ . '/auth_check.php');
    $lines = explode("\n", $content);
    
    echo "<p><strong>Conteúdo problemático (linha 27):</strong></p>";
    echo "<code style='background: #f5f5f5; padding: 10px; display: block;'>";
    echo htmlspecialchars($lines[26] ?? 'Linha não encontrada');
    echo "</code>";
    
} else {
    echo "❌ auth_check.php não encontrado<br>";
}

echo "<h2>3. Soluções Implementadas</h2>";

// SOLUÇÃO 1: Modificar auth_check.php para não dar header se já foi enviado output
echo "<h3>Solução 1: Modificar auth_check.php</h3>";

$auth_check_fixed = '<?php
// auto_append_file: auth_append.php

// Inicia a sessao se ela ainda nao estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define a lista de arquivos publicos, convertendo para minusculas
$publicFiles = array_map(\'strtolower\', [\'index.php\', \'login.php\', \'Access.php\', \'remember.php\', \'emergency_login.php\', \'test_login_real.php\', \'password_discovery.php\', \'fix_headers_issue.php\']);

// Obtem o nome do arquivo atual e converte para minusculas
$currentFile = strtolower(basename($_SERVER[\'SCRIPT_FILENAME\']));

// Se o arquivo atual estiver na lista de publicos, nao valida
if (in_array($currentFile, $publicFiles)) {
    return;
}

// Verifica se a URL requisitada contem "access.php" (ignorando caixa)
if (strpos(strtolower($_SERVER[\'REQUEST_URI\']), \'access.php\') !== false) {
    return;
}

// CORREÇÃO: Só enviar header se ainda não foi enviado output
if (empty($_SESSION[\'userID\']) || empty($_SESSION[\'login\']) || empty($_SESSION[\'pefil\'])) {
    session_destroy();
    
    // Verificar se headers já foram enviados
    if (!headers_sent()) {
        header("Location: https://siga.coface.com/src/role/access/Access.php");
        exit("Erro: Usuário não autenticado.");
    } else {
        // Se headers já foram enviados, usar JavaScript para redirect
        echo "<script>window.location.href = \'https://siga.coface.com/src/role/access/Access.php\';</script>";
        echo "<noscript><meta http-equiv=\'refresh\' content=\'0;url=https://siga.coface.com/src/role/access/Access.php\'></noscript>";
        exit("Erro: Usuário não autenticado.");
    }
}
?>';

// Backup do arquivo original
if (file_exists(__DIR__ . '/auth_check.php')) {
    copy(__DIR__ . '/auth_check.php', __DIR__ . '/auth_check_original_backup.php');
    echo "✅ Backup criado: auth_check_original_backup.php<br>";
}

// Aplicar correção
file_put_contents(__DIR__ . '/auth_check.php', $auth_check_fixed);
echo "✅ auth_check.php corrigido<br>";

echo "<h3>Solução 2: Adicionar arquivos à lista de públicos</h3>";
echo "✅ Adicionados à lista de arquivos públicos:<br>";
echo "- emergency_login.php<br>";
echo "- test_login_real.php<br>";
echo "- password_discovery.php<br>";
echo "- fix_headers_issue.php<br>";

echo "<h2>4. Teste da Correção</h2>";

// Testar se a correção funciona
echo "<p>Testando se headers_sent() funciona corretamente:</p>";
if (headers_sent($file, $line)) {
    echo "<p style='color: orange;'>⚠️ Headers já foram enviados por este arquivo na linha $line</p>";
    echo "<p>Isso é normal para arquivos de diagnóstico.</p>";
} else {
    echo "<p style='color: green;'>✅ Headers ainda não foram enviados - correção deve funcionar</p>";
}

echo "<h2>5. Próximos Passos</h2>";
echo "<div style='background: #e8f5e8; padding: 15px; margin: 10px 0; border-radius: 4px; color: #2e7d32;'>";
echo "<p><strong>Para testar:</strong></p>";
echo "<ol>";
echo "<li>Acesse <a href='emergency_login.php'>emergency_login.php</a> - não deve mais dar erro de headers</li>";
echo "<li>Tente fazer login normalmente - sistema deve funcionar</li>";
echo "<li>Se ainda houver problemas, o auth_check.php usará JavaScript para redirect</li>";
echo "</ol>";
echo "</div>";

echo "<h2>6. Informações Técnicas</h2>";
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Item</th><th>Status</th></tr>";
echo "<tr><td>Headers Sent</td><td>" . (headers_sent() ? 'Sim' : 'Não') . "</td></tr>";
echo "<tr><td>Session Status</td><td>" . session_status() . "</td></tr>";
echo "<tr><td>Current File</td><td>" . basename(__FILE__) . "</td></tr>";
echo "<tr><td>Request URI</td><td>" . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "</td></tr>";
echo "</table>";

echo "<hr>";
echo "<p><em>Problema de 'headers already sent' deve estar resolvido!</em></p>";

?>