<?php

require ("../../../dbOpen.php");

header("Content-Type: text/html; charset=ISO-8859-1", true);

$i_Produto = strtoupper(substr($_GET['i_Produto'], 0, 30));

if ($i_Produto != '') {
    // Prepara a consulta parametrizada
    $cQry = "SELECT MO.i_Moeda, MO.Nome 
             FROM Moeda MO
             INNER JOIN Produto_Moeda PM ON PM.i_Moeda = MO.i_Moeda
             WHERE MO.Situacao = 1 AND PM.i_Produto = ?";
    
    $stmt = odbc_prepare($db, $cQry);
    odbc_execute($stmt, [$i_Produto]);

    echo "|Selecione uma moeda,";
    $virgula = "";

    while (odbc_fetch_row($stmt)) {
        $i_Moeda = odbc_result($stmt, "i_Moeda");
        $nome = ($i_Moeda == 2) ? 'Dólar' : odbc_result($stmt, "Nome");

        echo $virgula . $i_Moeda . "|" . $nome;
        $virgula = ",";
    }

    // Libera o statement
    odbc_free_result($stmt);

} else {
    echo "|Nenhum Registro Encontrado";
}

// Libera a conexão ODBC
odbc_close($db);

?>
