<?php

// Primeira consulta
$sqlPro = "SELECT i_Item AS Id_Processo, Descricao_Item AS Processo 
            FROM Campo_Item 
            WHERE i_Campo = ? AND Situacao = ? 
            ORDER BY Descricao_Item";
$stmtPro = odbc_prepare($db, $sqlPro);
odbc_execute($stmtPro, [1200, 0]);

$dados_pro = [];
while (odbc_fetch_row($stmtPro)) {
    $Id_Processo = odbc_result($stmtPro, "Id_Processo");
    $Processo = odbc_result($stmtPro, "Processo");

    $dados_pro[] = [
        "Id_Processo" => $Id_Processo,
        "Processo"    => $Processo
    ];
}

// Segunda consulta
$sqlUser = "SELECT DISTINCT U.id AS id_Usuario, U.name AS Usuario 
            FROM Users U 
            INNER JOIN Log_Certificado_Digital LCD ON LCD.i_Usuario = U.id 
            ORDER BY U.name";
$stmtUser = odbc_prepare($db, $sqlUser);
odbc_execute($stmtUser);

$dados_user = [];
while (odbc_fetch_row($stmtUser)) {
    $id_Usuario = odbc_result($stmtUser, "id_Usuario");
    $Usuario = odbc_result($stmtUser, "Usuario");

    $dados_user[] = [
        "id_Usuario" => $id_Usuario,
        "Usuario"    => $Usuario
    ];
}

// Terceira consulta
$dados = [];
$processoID = "NULL";
$usuarioID = "NULL";
if (isset($_POST['buscar'])) {
    $processoID = (isset($_POST['processoID']) && ctype_digit($_POST['processoID'])) ? (int)$_POST['processoID'] : null;
    $usuarioID = (isset($_POST['usuarioID']) && ctype_digit($_POST['usuarioID'])) ? (int)$_POST['usuarioID'] : null;

    $sqlProc = "EXEC SPR_BB_LOG_CERTIFICADO_DIGITAL 200, ?, ?, NULL, NULL";
    $stmtProc = odbc_prepare($db, $sqlProc);
    odbc_execute($stmtProc, [$processoID, $usuarioID]);

    while (odbc_fetch_row($stmtProc)) {
        $i_Log = odbc_result($stmtProc, "i_Log");
        $Tipo_Processo = odbc_result($stmtProc, "Tipo_Processo");
        $Usuario = odbc_result($stmtProc, "Usuario");
        $d_Log = Convert_Data_Geral(substr(odbc_result($stmtProc, "d_Log"), 0, 10));

        $dados[] = [
            "i_Log"         => $i_Log,
            "Tipo_Processo" => $Tipo_Processo,
            "Usuario"       => $Usuario,
            "d_Log"         => $d_Log
        ];
    }
}

$title = "Consulta ao Log de Uso do Certificado Digital";
$content = "../log/interf/ViewLog.php";

?>
