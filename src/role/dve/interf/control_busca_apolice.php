<?php

require ("../../../dbOpen.php");
$idInsured = $_GET['idInsured'];
$userID = $_GET['codBanco'];

if ($idInsured != '') {
    $sql = "SELECT DISTINCT Inf.id, Inf.n_Apolice 
            FROM UsersNurim UN
            INNER JOIN Agencia A ON A.idNurim = UN.idNurim
            INNER JOIN CDBB CB ON CB.idAgencia = A.id
            INNER JOIN Inform Inf ON Inf.id = CB.idInform
            INNER JOIN DVE DVE ON DVE.idInform = Inf.id
            WHERE Inf.state IN (10, 11) AND CB.status = 2 AND DVE.state = 2 
            AND Inf.idInsured = ? AND UN.idUser = ?";
    
    $stmt = odbc_prepare($db, $sql);
    
    if ($stmt && odbc_execute($stmt, [$idInsured, $userID])) {
        echo "|Selecione...,";
        $virgula = "";
        
        while (odbc_fetch_row($stmt)) {
            echo $virgula . odbc_result($stmt, "id") . "|" . odbc_result($stmt, "n_Apolice");
            $virgula = ",";
        }
    } else {
        echo "|Nenhum Registro Encontrado";
    }
} else {
    echo "|Nenhum Registro Encontrado";
}

?>
