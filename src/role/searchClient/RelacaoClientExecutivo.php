<?php    

require_once ("../rolePrefix.php");
$idInform = $field->getField("idInform");

// Preparação da consulta SQL com parâmetros
$qry = "SELECT a.id, a.name, c.login 
        FROM Role a
        INNER JOIN UserRole b ON b.idRole = a.id
        INNER JOIN Users c ON c.id = b.idUser
        WHERE c.id = ? AND c.perfil = ?
        ORDER BY a.name, c.login";

// Preparação do statement ODBC
$stmt = odbc_prepare($db, $qry);

// Parâmetros para a consulta
$userID = $_SESSION['userID'];
$perfil = $_SESSION['pefil'];

// Execução da consulta com os parâmetros
$cur = odbc_execute($stmt, [$userID, $perfil]);

$x = isset($x) ? $x : false;
$role = []; // Inicialização do array para evitar erro
while (odbc_fetch_row($stmt)) {
    $x = $x + 1;
    $name = odbc_result($stmt, 'name');
    $id = odbc_result($stmt, 'id');
    $role[$name] = $id . '<br>';
}

// Verificação de roles para definir o conteúdo
if ((!isset($role["policy"])) && (!isset($role["relacaoCliExec"]))) {
    $content = "naoAutorizado.php";
} else {
    $content = "../searchClient/interf/ViewClientRelacao.php";
}

$title = "Busca";
require_once("../../../home.php");
?>
