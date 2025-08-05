<?php
$comm = isset($_REQUEST['comm']) ? $_REQUEST['comm'] : false;
$idInform = isset($_REQUEST['idInform']) ? $_REQUEST['idInform'] : false;
$id_Cessao = isset($_REQUEST['id_Cessao']) ? $_REQUEST['id_Cessao'] : false;

if ($comm && $id_Cessao) {
    $data = date("Y/m/d H:i:s");

    // Prepara a query com parâmetros
    $sql = "UPDATE CDBB SET status = 3, d_Aceite_Cancelamento_Seguradora = ?, dateCancel = ? WHERE id = ?";

    // Prepara a consulta e executa com os parâmetros
    $stmt = odbc_prepare($db, $sql);
    odbc_execute($stmt, array($data, $data, $id_Cessao));
}

// Log Certificado Digital
$id_Parametro = '10100';
require_once("../logCertificado.php");

$location = "Location: ../cessao/Cessao.php?comm=cancelaCessaoDireitoSeguradora";
header($location);
?>
