<?php

// Primeira consulta para buscar regiões
$sql = "SELECT N.id, N.name As Regiao FROM Nurim N ORDER BY name";
$rsSql = odbc_exec($db, $sql);

$dados_sel = array();
while (odbc_fetch_row($rsSql)) {
    $idRegiao = odbc_result($rsSql, "id");
    $Regiao = odbc_result($rsSql, "Regiao");

    $dados_sel[] = array(
        "idRegiao" => $idRegiao,
        "Regiao"   => $Regiao
    );
}

$dados = array();
$regiaoID = "";
if (isset($_REQUEST['buscar'])) {
    $regiaoID = $_REQUEST['regiaoID'];

    // Usando consultas parametrizadas para evitar SQL Injection
    $sqlProc = "EXEC SPR_BB_Consulta_Agencias ?, ?";
    $stmtProc = odbc_prepare($db, $sqlProc);
    odbc_execute($stmtProc, array(1, $regiaoID));

    while (odbc_fetch_row($stmtProc)) {
        $Regiao = odbc_result($stmtProc, "Regiao");
        $Codigo = odbc_result($stmtProc, "Codigo");
        $Agencia = odbc_result($stmtProc, "Agencia");
        $Endereco = odbc_result($stmtProc, "Endereco");
        $Cidade = odbc_result($stmtProc, "Cidade");
        $UF = odbc_result($stmtProc, "UF");

        $dados[] = array(
            "Regiao"   => $Regiao,
            "Codigo"   => $Codigo,
            "Agencia"  => $Agencia,
            "Endereco" => $Endereco,
            "Cidade"   => $Cidade,
            "UF"       => $UF
        );
    }
}

$title = "Relação de Agências";
$content = "../agency/interf/ViewAgency.php";
?>
