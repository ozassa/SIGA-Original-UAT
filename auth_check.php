<?php
// auto_append_file: auth_append.php

// Inicia a sessao se ela ainda nao estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define a lista de arquivos publicos, convertendo para minusculas
$publicFiles = array_map('strtolower', ['index.php', 'login.php', 'Access.php', 'remember.php', 'emergency_login.php', 'test_login_real.php', 'password_discovery.php', 'fix_headers_issue.php']);

// Obtem o nome do arquivo atual e converte para minusculas
$currentFile = strtolower(basename($_SERVER['SCRIPT_FILENAME']));

// Se o arquivo atual estiver na lista de publicos, nao valida
if (in_array($currentFile, $publicFiles)) {
    return;
}

// Verifica se a URL requisitada contem "access.php" (ignorando caixa)
if (strpos(strtolower($_SERVER['REQUEST_URI']), 'access.php') !== false) {
    return;
}

// CORREÇÃO: Só enviar header se ainda não foi enviado output
if (empty($_SESSION['userID']) || empty($_SESSION['login']) || empty($_SESSION['pefil'])) {
    session_destroy();
    
    // Verificar se headers já foram enviados
    if (!headers_sent()) {
        header("Location: https://siga.coface.com/src/role/access/Access.php");
        exit("Erro: Usuário não autenticado.");
    } else {
        // Se headers já foram enviados, usar JavaScript para redirect
        echo "<script>window.location.href = 'https://siga.coface.com/src/role/access/Access.php';</script>";
        echo "<noscript><meta http-equiv='refresh' content='0;url=https://siga.coface.com/src/role/access/Access.php'></noscript>";
        exit("Erro: Usuário não autenticado.");
    }
}
?>
