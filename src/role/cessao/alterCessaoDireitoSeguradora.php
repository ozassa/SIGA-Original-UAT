<?php
$comm = isset($_REQUEST['comm']) ? $_REQUEST['comm'] : false;
$id_Cessao = isset($_REQUEST['id_Cessao']) ? $_REQUEST['id_Cessao'] : false;

if ($comm) {
    $data = date("Y/m/d H:i:s");

    // Preparar a query para a recusa ou aceite
    if ($comm == 'recusaCessaoDireitoSeguradora') {
        // Atualizar a cessão
        $sql = "UPDATE CDBB SET status = 3, dateCancel = ?, d_Aceite_Cancelamento_Seguradora = ? WHERE id = ?";
        $stmt = odbc_prepare($db, $sql);
        odbc_execute($stmt, array($data, $data, $id_Cessao));
    } else {
        // Atualizar a cessão
        $sql = "UPDATE CDBB SET status = 2, d_Aceite_Seguradora = ? WHERE id = ?";
        $stmt = odbc_prepare($db, $sql);
        odbc_execute($stmt, array($data, $id_Cessao));
    }
}

// Log Certificado Digital
$id_Parametro = '10040';
require_once("../logCertificado.php");

$location = "Location: ../cessao/Cessao.php?comm=emiteCessaoDireitoSeguradora";
header($location);
?>
