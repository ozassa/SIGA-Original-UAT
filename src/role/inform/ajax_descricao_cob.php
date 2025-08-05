<?php 
require_once ("../rolePrefix.php");

header('Content-type: text/json');

$sql  = "SELECT * FROM Campo_Item CI WHERE CI.i_Campo = 110 AND CI.i_Item = ?";
$cur = odbc_prepare($db, $sql);

if ($cur) {
    $params = array($_POST['regraCobrancaID']);
    $executed = odbc_execute($cur, $params);

    if ($executed) {
        $itens = array();
        $valor = utf8_encode(odbc_result($cur, 'Texto_Item'));

        echo json_encode(array("msg" => $valor));
    } else {
        echo json_encode(array("error" => "Erro ao executar a consulta."));
    }
} else {
    echo json_encode(array("error" => "Erro ao preparar a consulta."));
}
?>
