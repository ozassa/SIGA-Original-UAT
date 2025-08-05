<?php
// auto_append_file: auth_append.php

// Inicia a sessao se ela ainda nao estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define a lista de arquivos publicos, convertendo para minusculas
$publicFiles = array_map('strtolower', ['index.php', 'login.php', 'Access.php', 'remember.php']);

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

if (empty($_SESSION['userID']) || empty($_SESSION['login']) || empty($_SESSION['pefil'])) {
    session_destroy();
    header("Location: https://siga.coface.com/src/role/access/Access.php");
    exit("Erro: Usuário não autenticado.");
}
?>
