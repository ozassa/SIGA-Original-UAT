<?php
session_start();

require_once("../../../dbOpen.php");

$c_Napce = $_REQUEST['c_Napce'];

$qry = 'SELECT * FROM NAPCE WHERE c_NAPCE = ?';
$stmt = odbc_prepare($dbSisSeg, $qry);

if ($stmt === false) {
    echo 'Erro ao preparar a consulta.';
    exit;
}

$result = odbc_execute($stmt, [$c_Napce]);

if ($result === false) {
    echo 'Erro ao executar a consulta.';
    exit;
}

$linha = odbc_fetch_array($stmt);

if (empty($linha)) {
    echo 'Este número de NAPCE não existe, tente outro válido.';
}
?>
