<?php
// VERSÃO SIMPLIFICADA DO ACCESS.PHP PARA TESTE
require_once __DIR__ . '/../../../simple_session_config.php';

// Verificações básicas
$comm = '';
if (isset($_GET['comm'])) {
    $comm = $_GET['comm'];
} elseif (isset($_POST['comm'])) {
    $comm = $_POST['comm'];
}

$comm = htmlspecialchars($comm, ENT_QUOTES, 'UTF-8');

require_once "../rolePrefix.php";

$validar = isset($_REQUEST['validar']) ? $_REQUEST['validar'] : false;

// Lógica básica de login sem as correções de segurança por enquanto
if ($validar == "login") {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // TEMPORÁRIO: Login básico sem validação (APENAS PARA TESTE)
    if (!empty($login) && !empty($password)) {
        $_SESSION['userID'] = 1;
        $_SESSION['nameUser'] = $login;
        $_SESSION['login'] = $login;
        $_SESSION['pefil'] = 'A';
        
        header("Location: ../../../main.php");
        exit();
    } else {
        header("Location: ../../../index.php?erro=1");
        exit();
    }
}

// Logout
if ($comm == "exit") {
    session_destroy();
    header("Location: ../../../index.php");
    exit();
}
?>