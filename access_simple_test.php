<?php
/**
 * Versão simplificada do Access.php para identificar o problema
 */

// Habilitar todos os erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "=== TESTE SIMPLIFICADO ACCESS.PHP ===<br><br>";

// Passo 1: Testar includes básicos
echo "PASSO 1: Testando includes básicos<br>";

try {
    echo "- Incluindo session_config.php... ";
    require_once __DIR__ . '/session_config.php';
    echo "OK<br>";
} catch (Throwable $e) {
    echo "ERRO: " . $e->getMessage() . "<br>";
    die("Parou aqui!");
}

// Passo 2: Testar se temos $_POST
echo "<br>PASSO 2: Verificando dados POST<br>";
echo "- REQUEST_METHOD: " . ($_SERVER['REQUEST_METHOD'] ?? 'não definido') . "<br>";
echo "- POST login: " . ($_POST['login'] ?? 'não definido') . "<br>";
echo "- POST password: " . ($_POST['password'] ?? 'não definido') . "<br>";

// Passo 3: Definir comm
echo "<br>PASSO 3: Definindo variável comm<br>";
if (isset($_GET['comm'])) {
    $comm = $_GET['comm'];
    echo "- comm via GET: $comm<br>";
} elseif (isset($_POST['comm'])) {
    $comm = $_POST['comm'];
    echo "- comm via POST: $comm<br>";
} else {
    $comm = "";
    echo "- comm vazio<br>";
}

$comm = htmlspecialchars($comm, ENT_QUOTES, 'UTF-8');
echo "- comm sanitizado: $comm<br>";

// Passo 4: Incluir rolePrefix (CRÍTICO)
echo "<br>PASSO 4: Incluindo rolePrefix.php<br>";
try {
    echo "- Verificando se arquivo existe... ";
    if (file_exists(__DIR__ . '/src/role/rolePrefix.php')) {
        echo "SIM<br>";
        echo "- Incluindo rolePrefix.php... ";
        require_once __DIR__ . '/src/role/rolePrefix.php';
        echo "OK<br>";
    } else {
        echo "ARQUIVO NÃO ENCONTRADO!<br>";
        die("rolePrefix.php não existe!");
    }
} catch (Throwable $e) {
    echo "ERRO: " . $e->getMessage() . "<br>";
    echo "LINHA: " . $e->getLine() . "<br>";
    echo "ARQUIVO: " . $e->getFile() . "<br>";
    die("Erro fatal no rolePrefix!");
}

// Passo 5: Verificar extensão ODBC
echo "<br>PASSO 5: Verificando extensão ODBC<br>";
if (!extension_loaded('odbc')) {
    echo "- ODBC NÃO CARREGADA - ESTE É O PROBLEMA PRINCIPAL!<br>";
    echo "- Redirecionando para index.php...<br>";
    // Comentar o redirect para ver o resto do teste
    // header("Location: index.php?erro=2");
    // exit();
} else {
    echo "- ODBC carregada: OK<br>";
}

// Passo 6: Verificar variável $db
echo "<br>PASSO 6: Verificando conexão database<br>";
if (!isset($db)) {
    echo "- Variável \$db NÃO DEFINIDA<br>";
} elseif (!$db) {
    echo "- Variável \$db é FALSE (sem conexão)<br>";
} else {
    echo "- Variável \$db: OK (conectada)<br>";
}

// Passo 7: Testar includes de segurança
echo "<br>PASSO 7: Testando includes de segurança<br>";

try {
    if (file_exists(__DIR__ . '/security_functions.php')) {
        echo "- security_functions.php... ";
        require_once __DIR__ . '/security_functions.php';
        echo "OK<br>";
    } else {
        echo "- security_functions.php NÃO ENCONTRADO<br>";
    }
} catch (Throwable $e) {
    echo "- ERRO security_functions: " . $e->getMessage() . "<br>";
}

try {
    if (file_exists(__DIR__ . '/hybrid_auth.php')) {
        echo "- hybrid_auth.php... ";
        require_once __DIR__ . '/hybrid_auth.php';
        echo "OK<br>";
    } else {
        echo "- hybrid_auth.php NÃO ENCONTRADO<br>";
    }
} catch (Throwable $e) {
    echo "- ERRO hybrid_auth: " . $e->getMessage() . "<br>";
}

// Passo 8: Simular processo de login básico
echo "<br>PASSO 8: Simulando processo de login<br>";

$validar = isset($_REQUEST['validar']) ? $_REQUEST['validar'] : false;
echo "- validar: " . ($validar ? $validar : 'não definido') . "<br>";

// Verificar se é um processo de login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validar == 'login') {
    echo "- Detectado processo de LOGIN<br>";
    
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';
    
    echo "- Login: " . htmlspecialchars($login) . "<br>";
    echo "- Senha: " . (empty($password) ? 'vazia' : '***') . "<br>";
    
    // Aqui seria o ponto onde provavelmente falha
    echo "- Tentando autenticação...<br>";
    
    if (empty($login) || empty($password)) {
        echo "- LOGIN/SENHA VAZIOS - redirecionando...<br>";
        header("Location: index.php?erro=1");
        exit();
    }
    
    // Se chegou até aqui, o problema pode estar na validação de usuário
    echo "- Dados básicos validados<br>";
    
} else {
    echo "- NÃO é processo de login<br>";
}

echo "<br>=== TESTE CONCLUÍDO ===<br>";
echo "Se você chegou até aqui, o problema provavelmente está na validação/autenticação do usuário<br>";
echo "ou na conexão com o banco de dados ODBC.<br>";

?>