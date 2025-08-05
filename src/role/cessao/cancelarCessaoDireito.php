<?php
$comm = isset($_REQUEST['comm']) ? $_REQUEST['comm'] : false;
$idInform = isset($_REQUEST['idInform']) ? $_REQUEST['idInform'] : false;
$id_Cessao = isset($_REQUEST['id_Cessao']) ? $_REQUEST['id_Cessao'] : false;
$key_id = isset($_REQUEST['key_id']) ? $_REQUEST['key_id'] : false;

if ($comm) {
    $data = date("Y/m/d H:i:s");

    // Preparar a query usando parâmetros
    $sql = "UPDATE CDBB SET status = 4, d_Solic_Cancelamento = ?, Chave_Documento = ? WHERE id = ?";
    
    // Preparar a instrução
    $stmt = odbc_prepare($db, $sql);
    
    // Definir os parâmetros para a execução
    $params = array($data, $key_id, $id_Cessao);
    
    // Executar a query com os parâmetros
    $rsSql = odbc_execute($stmt, $params);
}

// Log Certificado Digital
$id_Parametro = '10080';
require_once("../logCertificado.php");

//require_once("distrato_pdf.php");

$location = "Location: ../cessao/Cessao.php?comm=cancelaCessaoDireito&idInform=" . urlencode($idInform);
header($location);
?>
