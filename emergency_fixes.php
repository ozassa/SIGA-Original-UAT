<?php
/**
 * SIGA - Correções de Emergência
 * Fixes para problemas críticos identificados
 */

// Error reporting para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🚨 SIGA - Correções de Emergência</h1>";
echo "<div style='font-family: Arial, sans-serif; margin: 20px;'>";

// 1. Verificar extensão ODBC
echo "<h2>🔌 1. Verificação ODBC</h2>";
if (extension_loaded('odbc')) {
    echo "<p style='color: green;'>✅ Extensão ODBC está carregada</p>";
} else {
    echo "<p style='color: red;'>❌ Extensão ODBC não encontrada</p>";
    echo "<p><strong>Solução:</strong></p>";
    echo "<ol>";
    echo "<li>Verificar se php_odbc.dll está habilitado no php.ini</li>";
    echo "<li>Reiniciar servidor web</li>";
    echo "<li>Verificar se drivers ODBC estão instalados no servidor</li>";
    echo "</ol>";
}

// 2. Testar conexão de banco alternativa
echo "<h2>🗄️ 2. Teste de Conexão Alternativa</h2>";
if (file_exists('src/dbOpen.php')) {
    try {
        include_once('src/dbOpen.php');
        echo "<p style='color: green;'>✅ Arquivo de conexão encontrado</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Erro ao incluir dbOpen.php: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Arquivo src/dbOpen.php não encontrado</p>";
}

// 3. Verificar funções de sessão
echo "<h2>🔐 3. Verificação de Sessão</h2>";
if (function_exists('session_start')) {
    echo "<p style='color: green;'>✅ Funções de sessão disponíveis</p>";
    
    // Testar início de sessão
    if (session_status() === PHP_SESSION_NONE) {
        if (!headers_sent()) {
            session_start();
            echo "<p style='color: green;'>✅ Sessão iniciada com sucesso</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Headers já enviados - sessão não pode ser iniciada aqui</p>";
        }
    } else {
        echo "<p style='color: green;'>✅ Sessão já está ativa</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Funções de sessão não disponíveis</p>";
}

// 4. Verificar arquivos críticos
echo "<h2>📁 4. Verificação de Arquivos Críticos</h2>";
$critical_files = [
    'index.php',
    'src/role/access/Access.php',
    'remember.php',
    'auth_check.php',
    'src/dbOpen.php'
];

foreach ($critical_files as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✅ {$file} - OK</p>";
        
        // Verificar sintaxe PHP
        $output = null;
        $return_var = null;
        exec("php -l {$file} 2>&1", $output, $return_var);
        
        if ($return_var === 0) {
            echo "<small style='color: green; margin-left: 20px;'>Sintaxe PHP válida</small><br>";
        } else {
            echo "<small style='color: red; margin-left: 20px;'>Erro de sintaxe: " . implode(' ', $output) . "</small><br>";
        }
    } else {
        echo "<p style='color: red;'>❌ {$file} - Não encontrado</p>";
    }
}

// 5. Configurações recomendadas
echo "<h2>⚙️ 5. Configurações Recomendadas</h2>";
echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 8px;'>";
echo "<h3>Para Resolver o Erro ODBC:</h3>";
echo "<ol>";
echo "<li><strong>Windows/IIS:</strong>";
echo "<ul>";
echo "<li>Abrir php.ini</li>";
echo "<li>Descomentar: extension=odbc</li>";
echo "<li>Reiniciar IIS</li>";
echo "</ul></li>";
echo "<li><strong>Verificar drivers ODBC:</strong>";
echo "<ul>";
echo "<li>Painel de Controle > Ferramentas Administrativas > Fontes de Dados ODBC</li>";
echo "<li>Configurar DSN do banco de dados</li>";
echo "</ul></li>";
echo "</ol>";
echo "</div>";

// 6. Script de teste rápido
echo "<h2>🧪 6. Teste Rápido</h2>";
echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 8px;'>";
echo "<p><strong>Para testar após as correções:</strong></p>";
echo "<ol>";
echo "<li>Acesse: <a href='index.php' target='_blank'>index.php</a></li>";
echo "<li>Tente login na página principal</li>";
echo "<li>Verifique se não há mais erros 500</li>";
echo "</ol>";
echo "</div>";

echo "</div>";

// 7. Informações do sistema
echo "<h2>ℹ️ 7. Informações do Sistema</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><td><strong>Versão PHP:</strong></td><td>" . PHP_VERSION . "</td></tr>";
echo "<tr><td><strong>Servidor Web:</strong></td><td>" . ($_SERVER['SERVER_SOFTWARE'] ?? 'Não identificado') . "</td></tr>";
echo "<tr><td><strong>Sistema:</strong></td><td>" . PHP_OS . "</td></tr>";
echo "<tr><td><strong>Extensões carregadas:</strong></td><td>" . implode(', ', get_loaded_extensions()) . "</td></tr>";
echo "</table>";

?>