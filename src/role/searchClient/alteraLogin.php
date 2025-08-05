<?php

require_once("../rolePrefix.php"); // conexão

$login = $_REQUEST['login'];
$tipoclient = $_REQUEST['tipoclient'];
$email = $_REQUEST['email']; // novo campo para o e-mail
$idcliente = $_REQUEST['idcliente'];

$sqlUpdate = "UPDATE Users SET login = ?, email = ? WHERE id = ?";
$stmt = odbc_prepare($db, $sqlUpdate);
$cur = odbc_execute($stmt, [$login, $email, $idcliente]);

if ($tipoclient != '1') {
    $idcliente = $idInform; // Verifique se `$idInform` foi previamente definido.
}

if ($cur) {
    $msg = "Login alterado com sucesso.";
} else {
    $msg = "Erro ao alterar o login.";
}
?>
