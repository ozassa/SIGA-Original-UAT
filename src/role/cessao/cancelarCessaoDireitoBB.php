<?php
$comm = isset($_REQUEST['comm']) ? $_REQUEST['comm'] : false;
$idInform = isset($_REQUEST['idInform']) ? $_REQUEST['idInform'] : false;
$id_Cessao = isset($_REQUEST['id_Cessao']) ? $_REQUEST['id_Cessao'] : false;

if ($comm) {
    $data = date("Y/m/d H:i:s");

    // Usando prepared statements para evitar SQL Injection
    $sql = "UPDATE CDBB SET d_Aceite_Cancelamento_Banco = ? WHERE id = ?";
    $stmt = odbc_prepare($db, $sql);

    if ($stmt) {
        odbc_bind_param($stmt, 1, $data, SQLVARCHAR); // Bind do parâmetro para $data
        odbc_bind_param($stmt, 2, $id_Cessao, SQLVARCHAR); // Bind do parâmetro para $id_Cessao
        odbc_execute($stmt);
    }
}

// Log Certificado Digital
$id_Parametro = '10090';
require_once("../logCertificado.php");

$location = "Location: ../cessao/Cessao.php?comm=cancelaCessaoDireitoBB";
header($location);
?>
