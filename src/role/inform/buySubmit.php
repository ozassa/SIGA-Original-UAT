<?php

if ($hc_cliente == "N" && ($_SESSION['pefil'] == 'C' || $_SESSION['pefil'] == 'B')) {
    $novo_estatus = "2";
} else {
    if ($_SESSION['pefil'] == 'C' || $_SESSION['pefil'] == 'B') {
        $novo_estatus = "2";
    } else {
        $novo_estatus = "3";
    }
}

// Prepara a query com parâmetros
$query = "UPDATE Inform SET buyersState = ? WHERE id = ?";
$stmt = odbc_prepare($db, $query);

// Define os parâmetros
$idInform = $_REQUEST['idInform'];

// Executa a query com parâmetros seguros
if (!odbc_execute($stmt, [$novo_estatus, $idInform])) {
    $msg = "Problemas na atualização da base de dados";
    $forward = "error";
}

?>
