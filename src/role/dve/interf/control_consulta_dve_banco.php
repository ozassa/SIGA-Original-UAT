<?php

// converte a data de dd/mm/yyyy para yyyy-mm-dd
if (!function_exists('dmy2ymd')) {
    function dmy2ymd($d) {
        global $msg;
        if (preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})/", $d, $v)) {
            return "$v[3]-$v[2]-$v[1]";
        } elseif (preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2})/", $d, $v)) {
            return ($v[3] + 2000) . "-$v[2]-$v[1]";
        } else {
            $msg = 'Data em formato inválido (deve ser dd/mm/yyyy ou dd/mm/yy): ' . $d;
            return '';
        }
    }
}

$int = false;
$CodDVE = isset($_REQUEST['CodDVE']) ? $_REQUEST['CodDVE'] : false;

if ($CodDVE) {

    foreach ($CodDVE as $key => $value) {
        $int = true;
        $idDVED = $value;

        $DataLiq = isset($_REQUEST['DataLiq'][$key]) ? dmy2ymd($_REQUEST['DataLiq'][$key]) : false;
        $DataProrrogacao = isset($_REQUEST['DataProrrogacao'][$key]) ? dmy2ymd($_REQUEST['DataProrrogacao'][$key]) : false;
        $Num_Operacao = isset($_REQUEST['Num_Operacao'][$key]) ? $_REQUEST['Num_Operacao'][$key] : false;

        $v_Pago = isset($_REQUEST['v_Pago'][$key]) ? $_REQUEST['v_Pago'][$key] : false;
        $v_Pago = str_replace('.', '', $v_Pago);
        $v_Pago = str_replace(',', '.', $v_Pago);

        $query = "UPDATE DVEDetails SET 
                      DataPagamento = ?, 
                      DataProrrogacao = ?, 
                      n_Operacao = ?, 
                      v_Pago = ? 
                  WHERE id = ?";

        $stmt = odbc_prepare($db, $query);
        $result = odbc_execute($stmt, [
            $DataLiq ?: null,
            $DataProrrogacao ?: null,
            $Num_Operacao,
            $v_Pago ?: 0,
            $idDVED
        ]);
    }
}

if ($int) {
    $msg = $result ? "Alteração realizada com sucesso." : "Nenhum registro foi alterado.";
} else {
    $msg = "Nenhum registro foi alterado.";
}

$idInsured = isset($_REQUEST['idInsured']) ? $_REQUEST['idInsured'] : 0;
$Comprador = isset($_REQUEST['Comprador']) ? $_REQUEST['Comprador'] : 0;
$Num_CRS = isset($_REQUEST['Num_CRS']) ? $_REQUEST['Num_CRS'] : 0;
$n_Apolice = isset($_REQUEST['n_Apolice']) ? $_REQUEST['n_Apolice'] : 0;
$SituacaoDVE = isset($_REQUEST['SituacaoDVE']) ? $_REQUEST['SituacaoDVE'] : 0;
$Fatura = isset($_REQUEST['Fatura']) ? $_REQUEST['Fatura'] : 0;

if (isset($_REQUEST['idInform'])) {
    $idInform = $_REQUEST['idInform'];
    
    $location = "Location: https://siga.coface.com/src/role/dve/Dve.php?comm=DVEConsulta&msg=".$msg."&idInform=".$idInform."&idInsured=".$idInsured."&Comprador=".$Comprador."&Num_CRS=".$Num_CRS."&n_Apolice=".$n_Apolice."&SituacaoDVE=".$SituacaoDVE."&Fatura=".$Fatura;
} else {
    $location = "Location: https://siga.coface.com/src/role/dve/Dve.php?comm=DVEConsulta&msg=".$msg."&idInsured=".$idInsured."&Comprador=".$Comprador."&Num_CRS=".$Num_CRS."&n_Apolice=".$n_Apolice."&SituacaoDVE=".$SituacaoDVE."&Fatura=".$Fatura;
}

header($location);
die;
