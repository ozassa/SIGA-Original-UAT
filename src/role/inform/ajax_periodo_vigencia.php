<?php
    require_once ("../rolePrefix.php");

    header('Content-type: text/json');

    $sqlVig = "SELECT Inf.id, Min(PV.d_Vigencia_Inicial) AS Ini_Vigencia, Max(PV.d_Vigencia_Final) AS Fim_Vigencia 
               FROM Inform Inf 
               INNER JOIN Periodo_Vigencia PV ON PV.i_Inform = Inf.id 
               WHERE Inf.id = ? 
               GROUP BY Inf.id";

    $stmt = odbc_prepare($db, $sqlVig);
    $params = [$_POST['idInform']];
    if (odbc_execute($stmt, $params)) {
        $Ini_Vigencia = date("d/m/Y", strtotime(odbc_result($stmt, 'Ini_Vigencia')));
        $Fim_Vigencia = date("d/m/Y", strtotime(odbc_result($stmt, 'Fim_Vigencia')));

        echo json_encode(array("Ini_Vigencia" => $Ini_Vigencia, "Fim_Vigencia" => $Fim_Vigencia));
    } else {
        echo json_encode(array("error" => "Erro ao executar a consulta."));
    }

    odbc_free_result($stmt);
?>
