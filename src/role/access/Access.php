<?php
/**
 * Versão segura do Access.php com verificação de ODBC primeiro
 */

// Habilitar exibição de erros para debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// PRIMEIRA VERIFICAÇÃO: EXTENSÃO ODBC
if (!extension_loaded('odbc')) {
    error_log("CRITICAL: ODBC extension not loaded - redirecting to index");
    header("Location: ../../../index.php?erro=2");
    exit();
}

// Agora que sabemos que ODBC existe, podemos prosseguir
try {
    require_once __DIR__ . '/../../../session_config.php';
} catch (Exception $e) {
    error_log("Error loading session_config: " . $e->getMessage());
    header("Location: ../../../index.php?erro=2");
    exit();
}

// Obter comando
if (isset($_GET['comm'])) {
    $comm = $_GET['comm'];
} elseif (isset($_POST['comm'])) {
    $comm = $_POST['comm'];
} else {
    $comm = "";
}

$comm = htmlspecialchars($comm, ENT_QUOTES, 'UTF-8');

// Incluir arquivos críticos com tratamento de erro
try {
    require_once __DIR__ . "/../rolePrefix.php";
} catch (Exception $e) {
    error_log("Error loading rolePrefix: " . $e->getMessage());
    header("Location: ../../../index.php?erro=2");
    exit();
}

// Verificar se temos conexão de banco
if (!isset($db) || !$db) {
    error_log("Database connection not available");
    header("Location: ../../../index.php?erro=2");
    exit();
}

// Incluir funções de segurança se existirem
if (file_exists(__DIR__ . "/../../../security_functions.php")) {
    try {
        require_once __DIR__ . "/../../../security_functions.php";
    } catch (Exception $e) {
        error_log("Error loading security_functions: " . $e->getMessage());
        // Não é crítico, continuar sem
    }
}

// Incluir autenticação híbrida se existir
if (file_exists(__DIR__ . "/../../../hybrid_auth.php")) {
    try {
        require_once __DIR__ . "/../../../hybrid_auth.php";
    } catch (Exception $e) {
        error_log("Error loading hybrid_auth: " . $e->getMessage());
        // Não é crítico, continuar sem
    }
}

// Obter dados de validação
$validar = isset($_REQUEST['validar']) ? $_REQUEST['validar'] : false;

// Tratar comando de saída
if ($comm == 'exit') {
    // Limpeza segura da sessão
    $_SESSION['userID'] = '';
    $_SESSION['nameUser'] = '';
    $_SESSION['login'] = '';
    $_SESSION['pefil'] = '';
    
    // Destruir sessão
    session_destroy();
    session_start();
    
    header("Location: ../../../index.php");
    exit();
}

// Processar login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validar == 'login') {
    $login = isset($_POST['login']) ? trim($_POST['login']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    
    // Validações básicas
    if (empty($login) || empty($password)) {
        header("Location: ../../../index.php?erro=1");
        exit();
    }
    
    // Tentar autenticação
    try {
        // Usar a classe User do sistema original
        require_once(__DIR__ . "/../../entity/user/User.php");
        
        // Tentar login com diferentes perfis
        $perfis = ['F', 'CO', 'C', 'E', 'A', 'D'];
        $user_authenticated = false;
        $user_data = null;
        
        foreach ($perfis as $perfil) {
            try {
                $user = new User($login, $password, $perfil, $db);
                $userData = $user->getUserView();
                
                if ($userData != null) {
                    $user_authenticated = true;
                    $user_data = $userData;
                    break;
                }
            } catch (Exception $e) {
                error_log("Error trying profile $perfil: " . $e->getMessage());
                continue;
            }
        }
        
        if ($user_authenticated && $user_data) {
            // Login bem-sucedido
            $_SESSION['userID'] = $user_data['userID'];
            $_SESSION['nameUser'] = $user_data['nameUser'];
            $_SESSION['login'] = $login;
            $_SESSION['pefil'] = $user_data['perfil'];
            
            // Redirecionar para página principal do sistema
            header("Location: ../../executive/executive.php");
            exit();
            
        } else {
            // Login falhou
            header("Location: ../../../index.php?erro=1");
            exit();
        }
        
    } catch (Exception $e) {
        error_log("Authentication error: " . $e->getMessage());
        header("Location: ../../../index.php?erro=1");
        exit();
    }
}

// Se chegou até aqui sem ser login, redirecionar para index
header("Location: ../../../index.php");
exit();

?>