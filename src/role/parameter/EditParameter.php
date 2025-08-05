<?php

if (!isset($_GET["id_par"])) {
    $location = "Location: " . $host . "src/role/parameter/ParameterSystem.php";
    header($location);
    exit;
}

$parametroID = $_GET["id_par"];
$empresaID = $_GET["id_emp"];

$sql = "SELECT P.i_Parametro, 
               P.Parametro,
               PE.i_Empresa, 
               PE.n_Parametro As Numero,
               PE.v_Parametro As Valor,
               PE.d_Parametro As Data,
               PE.t_Parametro As Texto
        FROM Parametro P
        INNER JOIN Parametro_Empresa PE ON PE.i_Parametro = P.i_Parametro
        WHERE P.i_Parametro = ? AND PE.i_Empresa = ?
        ORDER BY P.i_Parametro";

$stmt = odbc_prepare($db, $sql);
if ($stmt === false) {
    die("Erro ao preparar a consulta.");
}

$params = [$parametroID, $empresaID];
$result = odbc_execute($stmt, $params);
if ($result === false) {
    die("Erro ao executar a consulta.");
}

$i_Parametro = odbc_result($stmt, "i_Parametro");
$Parametro = odbc_result($stmt, "Parametro");
$Numero = odbc_result($stmt, "Numero");
$Valor = odbc_result($stmt, "Valor");
$Data = odbc_result($stmt, "Data");
$Texto = odbc_result($stmt, "Texto");

odbc_free_result($stmt);
odbc_close($db);

$title = "Editar Par&acirc;metro";
$content = "../parameter/interf/ViewEditParameter.php";
