<?php 
if (!isset($_SESSION)) {
    session_start();
}

$idDetail = $_REQUEST['idDetail'];
$idDVE = $_REQUEST['idDVE'];
$userID = $_SESSION['userID'];

// Consulta para obter os detalhes do DVE
$querySelect = "SELECT * FROM DVEDetails WHERE id = ?";
$stmtSelect = odbc_prepare($db, $querySelect);
odbc_execute($stmtSelect, [$idDetail]);

if ($row = odbc_fetch_array($stmtSelect)) {
    $idImporter = $row['idImporter'];
    $idCountry = $row['idCountry'];
    $embDate = $row['embDate'];
    $vencDate = $row['vencDate'];
    $fatura = $row['fatura'];
    $totalEmbarcado = $row['totalEmbarcado'];
    $proex = $row['proex'];
    $ace = $row['ace'];
    $modalidade = $row['modalidade'];

    // Inserir na tabela ChangeDVE
    $queryInsert = "INSERT INTO ChangeDVE (idDVE, idImporter, idCountry, embDate, vencDate, fatura, totalEmbarcado, proex, ace, modalidade, idUser, tipo, date, idDetail) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 3, GETDATE(), ?)";
    $stmtInsert = odbc_prepare($db, $queryInsert);
    $insertSuccess = odbc_execute($stmtInsert, [
        $idDVE, $idImporter, $idCountry, $embDate, $vencDate, $fatura, $totalEmbarcado, $proex, $ace, $modalidade, $userID, $idDetail
    ]);

    // Atualizar estado em DVEDetails
    $queryUpdateDetails = "UPDATE DVEDetails SET state = 3 WHERE id = ?";
    $stmtUpdateDetails = odbc_prepare($db, $queryUpdateDetails);
    $updateDetailsSuccess = odbc_execute($stmtUpdateDetails, [$idDetail]);

    if (!$insertSuccess || !$updateDetailsSuccess) {
        $msg = 'Erro ao excluir embarque';
    } else {
        // Verificar o estado da DVE
        $queryState = "SELECT state FROM DVE WHERE id = ?";
        $stmtState = odbc_prepare($db, $queryState);
        odbc_execute($stmtState, [$idDVE]);

        if ($stateRow = odbc_fetch_array($stmtState)) {
            $state = $stateRow['state'];
            if ($state == 2) {
                // Atualizar estado da DVE
                $queryUpdateDVE = "UPDATE DVE SET state = 3 WHERE id = ?";
                $stmtUpdateDVE = odbc_prepare($db, $queryUpdateDVE);
                odbc_execute($stmtUpdateDVE, [$idDVE]);
            }
        }

        $registro = '';
        $show = 0;
        $idCountry = 0;
        $msg = 'Embarque excluído';
    }
} else {
    $msg = 'Detalhes do DVE não encontrados.';
}
odbc_free_result($stmtSelect);
odbc_free_result($stmtState);
?>
