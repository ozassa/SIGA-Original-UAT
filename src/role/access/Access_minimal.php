<?php
/**
 * VERSÃO MÍNIMA DO ACCESS.PHP - EMERGÊNCIA ABSOLUTA
 * Esta versão funciona SEM dependências externas
 */

// Iniciar sessão básica
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Log de debug
$debug_log = "=== ACCESS MINIMAL DEBUG ===\n";
$debug_log .= "Time: " . date('Y-m-d H:i:s') . "\n";
$debug_log .= "Method: " . $_SERVER['REQUEST_METHOD'] . "\n";
$debug_log .= "ODBC loaded: " . (extension_loaded('odbc') ? 'YES' : 'NO') . "\n";

// Verificar se é requisição POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $debug_log .= "POST data received\n";
    $debug_log .= "Login: " . ($_POST['login'] ?? 'not set') . "\n";
    $debug_log .= "Password: " . (isset($_POST['password']) ? 'provided' : 'not set') . "\n";
    $debug_log .= "Validar: " . ($_POST['validar'] ?? 'not set') . "\n";
}

// Salvar debug log
file_put_contents(__DIR__ . '/access_debug.log', $debug_log, FILE_APPEND);

// Verificar ODBC primeiro
if (!extension_loaded('odbc')) {
    error_log("Access_minimal: ODBC not loaded, redirecting to emergency");
    header("Location: ../../../emergency_login.php");
    exit();
}

// Verificar se é comando de saída
$comm = '';
if (isset($_GET['comm'])) {
    $comm = $_GET['comm'];
} elseif (isset($_POST['comm'])) {
    $comm = $_POST['comm'];
}

if ($comm === 'exit') {
    session_destroy();
    header("Location: ../../../index.php");
    exit();
}

// Processar login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validar']) && $_POST['validar'] === 'login') {
    
    $login = trim($_POST['login'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($login) || empty($password)) {
        header("Location: ../../../index.php?erro=1");
        exit();
    }
    
    // Aqui tentamos incluir os arquivos necessários COM tratamento de erro
    try {
        // Incluir dbOpen primeiro
        require_once(__DIR__ . "/../../dbOpen.php");
        
        if (!isset($db) || !$db) {
            throw new Exception("Database connection failed");
        }
        
        // Incluir classe User
        require_once(__DIR__ . "/../../entity/user/User.php");
        
        // Tentar autenticação simples
        $perfis = ['F', 'CO', 'C', 'E', 'A'];
        $user_found = false;
        
        foreach ($perfis as $perfil) {
            try {
                $user = new User($login, $password, $perfil, $db);
                $userData = $user->getUserView();
                
                if ($userData && isset($userData['userID'])) {
                    // Login bem-sucedido
                    $_SESSION['userID'] = $userData['userID'];
                    $_SESSION['nameUser'] = $userData['nameUser'];
                    $_SESSION['login'] = $login;
                    $_SESSION['pefil'] = $perfil;
                    
                    error_log("Access_minimal: Login successful for user: $login");
                    
                    // Redirecionar para sistema
                    header("Location: ../../executive/executive.php");
                    exit();
                }
            } catch (Exception $e) {
                error_log("Access_minimal: Error with profile $perfil: " . $e->getMessage());
                continue;
            }
        }
        
        // Se chegou aqui, login falhou
        error_log("Access_minimal: Login failed for user: $login");
        header("Location: ../../../index.php?erro=1");
        exit();
        
    } catch (Exception $e) {
        error_log("Access_minimal: Critical error: " . $e->getMessage());
        header("Location: ../../../emergency_login.php?erro=system");
        exit();
    }
}

// Se não é POST ou login, redirecionar para index
header("Location: ../../../index.php");
exit();

?>