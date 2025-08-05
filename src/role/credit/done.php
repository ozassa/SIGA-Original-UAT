<?php  
$idInform    = $_REQUEST["idInform"];
$enviarTarif = $_REQUEST["enviarTarif"];

$msg = "Erro no envio de notifi&ccedil;&atilde;o &agrave; TARIFA&Ccedil;&Atilde;O.";

if ($comm == 'goTarif') {
    $enviarTarif = 1;
    $goTarif = 1;
} else {
    $count = 0;

    $curCountQuery = "SELECT DISTINCT COUNT(*) FROM Importer WHERE idInform = ? AND (state=4 OR state=3)";
    $curCountStmt = odbc_prepare($db, $curCountQuery);
    odbc_execute($curCountStmt, [$idInform]);

    if (odbc_fetch_row($curCountStmt)) {
        $count = odbc_result($curCountStmt, 1);
    }
}

if ($count == 0 || $goTarif == 1) {
    $curQuery = "SELECT name, state FROM Inform WHERE id = ?";
    $curStmt = odbc_prepare($db, $curQuery);
    odbc_execute($curStmt, [$idInform]);

    if (odbc_fetch_row($curStmt)) {
        $name = odbc_result($curStmt, 1);
        $state = odbc_result($curStmt, 2);

        if ($state >= 4) {
            $comm = "goOk";
        } else {
            $comm = "goTarif";
        }

        if ($comm == "goTarif" && $enviarTarif == 1) {
            // Atualiza a situação do Informe para Tarifação
            $updateQuery = "UPDATE Inform SET state = 4 WHERE state <= 4 and id = ?";
            $updateStmt = odbc_prepare($db, $updateQuery);
            odbc_execute($updateStmt, [$idInform]);

            // Realiza a criação da notificação de tarifação
            $r = $notif->newTarif($userID, $name, $idInform, $db);

            $analiseQuery = "SELECT id, fim FROM AnaliseInform WHERE idInform = ?";
            $analiseStmt = odbc_prepare($db, $analiseQuery);
            odbc_execute($analiseStmt, [$idInform]);

            if (odbc_fetch_row($analiseStmt)) {
                $id = odbc_result($analiseStmt, 1);
                $fim = odbc_result($analiseStmt, 2);

                if (!$fim) {
                    $updateFimQuery = "UPDATE AnaliseInform SET fim = GETDATE() WHERE id = ?";
                    $updateFimStmt = odbc_prepare($db, $updateFimQuery);
                    odbc_execute($updateFimStmt, [$id]);
                }
            }

            $msg = "Sucesso no envio de notificação à TARIFAÇÃO. &nbsp;";
        } else {
            $msg = "Sucesso na atualização dos limites. &nbsp;";
        }
    }
} else {
    $msg = "Alteração realizada com sucesso. &nbsp;<br>";
}
?>
