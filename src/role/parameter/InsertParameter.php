<?php

function formataValorSql($formataValorSql){
    $formataValorSql = str_replace('.', '', $formataValorSql);
    $formataValorSql = str_replace(',', '.', $formataValorSql);
    return $formataValorSql;
}

function Convert_Data_Pt_En($data){
    if (strstr($data, "/")){
        $d = explode("/", $data);
        $invert_data = "$d[2]-$d[1]-$d[0]";
        return $invert_data;
    } elseif (strstr($data, "-")){
        $d = explode("-", $data);
        $invert_data = "$d[2]/$d[1]/$d[0]";
        return $invert_data;
    }
}

$empresaID = (isset($_POST["empresaID"]) && ctype_digit($_POST["empresaID"])) ? (int)$_POST["empresaID"] : null;
$parametroID = (isset($_POST["id_par"]) && ctype_digit($_POST["id_par"])) ? (int)$_POST["id_par"] : null;

$numeroParametro = $_POST["numeroParametro"] ? $_POST["numeroParametro"] : null;
$valorParametro = $_POST["valorParametro"] ? formataValorSql($_POST["valorParametro"]) : "0";
$dataParametro = $_POST["dataParametro"] ? Convert_Data_Pt_En($_POST["dataParametro"]) : null;
$textoParametro = $_POST["textoParametro"] ?? "";

if (isset($parametroID)) {
    // EDITAR
    $sqlUp = "UPDATE Parametro_Empresa SET
                  n_Parametro = ?, 
                  v_Parametro = ?, 
                  d_Parametro = ?, 
                  t_Parametro = ?
              WHERE i_Parametro = ? AND i_Empresa = ?";
    $stmt = odbc_prepare($db, $sqlUp);
    odbc_execute($stmt, [$numeroParametro, $valorParametro, $dataParametro, $textoParametro, $parametroID, $empresaID]);
} else {
    // INSERIR
    $sqlIntoEmp = "INSERT INTO Parametro_Empresa (
                       i_Empresa, 
                       n_Parametro, 
                       v_Parametro, 
                       d_Parametro, 
                       t_Parametro
                   ) VALUES (?, ?, ?, ?, ?)";
    $stmt = odbc_prepare($db, $sqlIntoEmp);
    odbc_execute($stmt, [$empresaID, $numeroParametro, $valorParametro, $dataParametro, $textoParametro]);
}

odbc_close($db);

$location = "Location: " . $host . "src/role/parameter/ParameterSystem.php?comm=" . ($empresaID == 1 ? "coface" : "sbce");
header($location);
