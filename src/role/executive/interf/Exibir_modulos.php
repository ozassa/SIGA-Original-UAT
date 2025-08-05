<?php
require_once("../../../dbOpen.php");

$idInform = $_REQUEST['idInform'];
$i_Sub_Produto = $_REQUEST['i_Sub_Produto'];

// Consulta preparada para evitar SQL Injection
$qry = "UPDATE Inform SET i_Sub_Produto = ? WHERE id = ?";
$stmt = odbc_prepare($db, $qry);

// Executando a consulta preparada
$res = odbc_execute($stmt, [$i_Sub_Produto, $idInform]);

if ($res) {
    echo "1";
} else {
    echo "0";
}

// Libera a conexão ODBC
odbc_free_result($stmt);
?>
