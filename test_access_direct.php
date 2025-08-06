<?php
/**
 * Teste direto do Access.php para identificar erro 500
 */

// Habilitar todos os erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>Teste Direto Access.php - Diagnóstico de Erro 500</h2>";
echo "<hr>";

echo "<h3>1. Testando Includes Individuais</h3>";

// Testar session_config.php
echo "Testando session_config.php... ";
try {
    require_once __DIR__ . '/session_config.php';
    echo "<span style='color: green;'>OK</span><br>";
} catch (Exception $e) {
    echo "<span style='color: red;'>ERRO: " . $e->getMessage() . "</span><br>";
}

// Testar rolePrefix.php
echo "Testando src/role/rolePrefix.php... ";
try {
    require_once __DIR__ . '/src/role/rolePrefix.php';
    echo "<span style='color: green;'>OK</span><br>";
} catch (Exception $e) {
    echo "<span style='color: red;'>ERRO: " . $e->getMessage() . "</span><br>";
}

// Testar security_functions.php
echo "Testando security_functions.php... ";
try {
    if (file_exists(__DIR__ . '/security_functions.php')) {
        require_once __DIR__ . '/security_functions.php';
        echo "<span style='color: green;'>OK</span><br>";
    } else {
        echo "<span style='color: orange;'>ARQUIVO NÃO ENCONTRADO</span><br>";
    }
} catch (Exception $e) {
    echo "<span style='color: red;'>ERRO: " . $e->getMessage() . "</span><br>";
}

// Testar hybrid_auth.php
echo "Testando hybrid_auth.php... ";
try {
    if (file_exists(__DIR__ . '/hybrid_auth.php')) {
        require_once __DIR__ . '/hybrid_auth.php';
        echo "<span style='color: green;'>OK</span><br>";
    } else {
        echo "<span style='color: orange;'>ARQUIVO NÃO ENCONTRADO</span><br>";
    }
} catch (Exception $e) {
    echo "<span style='color: red;'>ERRO: " . $e->getMessage() . "</span><br>";
}

echo "<h3>2. Testando Extensões PHP</h3>";

echo "Extensão ODBC: ";
if (extension_loaded('odbc')) {
    echo "<span style='color: green;'>CARREGADA</span><br>";
} else {
    echo "<span style='color: red;'>NÃO CARREGADA - ESTE É O PROBLEMA!</span><br>";
}

echo "Extensão Session: ";
if (extension_loaded('session')) {
    echo "<span style='color: green;'>CARREGADA</span><br>";
} else {
    echo "<span style='color: red;'>NÃO CARREGADA</span><br>";
}

echo "<h3>3. Testando Variáveis Globais</h3>";

echo "Variável \$db: ";
if (isset($db)) {
    if ($db) {
        echo "<span style='color: green;'>CONECTADA</span><br>";
    } else {
        echo "<span style='color: orange;'>DEFINIDA MAS FALSA</span><br>";
    }
} else {
    echo "<span style='color: red;'>NÃO DEFINIDA</span><br>";
}

echo "<h3>4. Testando Sessão</h3>";

echo "Status da sessão: ";
$status = session_status();
switch ($status) {
    case PHP_SESSION_DISABLED:
        echo "<span style='color: red;'>DESABILITADA</span><br>";
        break;
    case PHP_SESSION_NONE:
        echo "<span style='color: orange;'>NÃO INICIADA</span><br>";
        break;
    case PHP_SESSION_ACTIVE:
        echo "<span style='color: green;'>ATIVA</span><br>";
        break;
}

echo "<h3>5. Simulando Chamada do Access.php</h3>";

// Simular dados POST
$_POST['login'] = 'teste';
$_POST['password'] = 'teste';
$_POST['validar'] = 'login';

echo "Simulando POST com dados de login...<br>";

// Tentar carregar Access.php com tratamento de erro
echo "Tentando carregar Access.php...<br>";

try {
    // Capturar qualquer output
    ob_start();
    
    // Definir variáveis que o Access.php espera
    $_SERVER['REQUEST_METHOD'] = 'POST';
    
    echo "Verificando se arquivo Access.php existe: ";
    if (file_exists(__DIR__ . '/src/role/access/Access.php')) {
        echo "<span style='color: green;'>SIM</span><br>";
        
        echo "Tentando incluir Access.php...<br>";
        
        // Tentar incluir linha por linha para identificar onde falha
        $access_content = file_get_contents(__DIR__ . '/src/role/access/Access.php');
        $lines = explode("\n", $access_content);
        
        echo "Arquivo tem " . count($lines) . " linhas<br>";
        echo "Primeiras 10 linhas:<br>";
        for ($i = 0; $i < min(10, count($lines)); $i++) {
            echo ($i + 1) . ": " . htmlspecialchars($lines[$i]) . "<br>";
        }
        
        // Tentar executar o arquivo
        echo "<br>Tentando executar Access.php...<br>";
        
        include __DIR__ . '/src/role/access/Access.php';
        
    } else {
        echo "<span style='color: red;'>NÃO</span><br>";
    }
    
    $output = ob_get_contents();
    ob_end_clean();
    
    echo "Output capturado:<br>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
    
} catch (ParseError $e) {
    echo "<span style='color: red;'>ERRO DE SINTAXE: " . $e->getMessage() . " na linha " . $e->getLine() . "</span><br>";
} catch (FatalError $e) {
    echo "<span style='color: red;'>ERRO FATAL: " . $e->getMessage() . " na linha " . $e->getLine() . "</span><br>";
} catch (Exception $e) {
    echo "<span style='color: red;'>EXCEÇÃO: " . $e->getMessage() . " na linha " . $e->getLine() . "</span><br>";
} catch (Error $e) {
    echo "<span style='color: red;'>ERRO PHP: " . $e->getMessage() . " na linha " . $e->getLine() . "</span><br>";
}

echo "<h3>6. Informações do Sistema</h3>";
echo "Versão PHP: " . phpversion() . "<br>";
echo "Sistema Operacional: " . php_uname() . "<br>";
echo "Servidor Web: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";

echo "<hr>";
echo "<h3>Conclusão</h3>";
echo "Se você vê esta mensagem, o problema não é nos includes básicos.<br>";
echo "O erro 500 provavelmente está relacionado à extensão ODBC ou à conexão de banco de dados.<br>";
echo "Verifique se a extensão php-odbc está instalada no servidor.";

?>