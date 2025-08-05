<?php 
$i = 0;
$importers = "<ul>";


while(1){
  $i++;
  //$idImportador = ${'idBuyer'. $i};
  $idImportador = $_REQUEST['idBuyer'. $i]; //alterado Elias Vaz - Interaktiv em 17/04/2012
   
  if(! $idImportador){
    break;
  }
  
  //if (${'chkrem'. $i} == "on")     //if do objeto checkbox-chkrem----- alterado hicom em 28/04/2004
  
  if ($_REQUEST['chkrem'. $i] == "on") {
    $stmt = odbc_prepare($db, "SELECT i.name, c.name FROM Importer i JOIN Country c ON c.id=i.idCountry WHERE i.id = ?");
    odbc_execute($stmt, [$idImportador]);
    $importador = odbc_result($stmt, 1);
    $pais = odbc_result($stmt, 2);
    $importers .= "<li>$importador / $pais";

    $stmt = odbc_prepare($db, "UPDATE ImporterRem SET remDate = GETDATE(), state = 2 WHERE idImporter = ?");
    odbc_execute($stmt, [$idImportador]);

    $stmt = odbc_prepare($db, "SELECT c_Coface_Imp, state, hold FROM Importer WHERE id = ?");
    odbc_execute($stmt, [$idImportador]);
    $idCoface = "";
    if (odbc_fetch_row($stmt)) {
        $idTemp = odbc_result($stmt, 1);
        $state = odbc_result($stmt, 2);
        $hold = odbc_result($stmt, 3);
        if ($idTemp != "" && $idTemp != 0) {
            $idCoface = $idTemp;
        }
    }

    $stmt = odbc_prepare($db, "SELECT COUNT(*) FROM Inform WHERE id = ? AND idAnt IS NOT NULL");
    odbc_execute($stmt, [$idInform]);
    $count = 0;
    if (odbc_fetch_row($stmt)) {
        $count = odbc_result($stmt, 1);
    }

    $stmt = odbc_prepare($db, "SELECT idAnt FROM Inform WHERE id = ?");
    odbc_execute($stmt, [$idInform]);
    $idAnt = odbc_result($stmt, 1);

    if ($idCoface == "") {
        $stmt = odbc_prepare($db, "DELETE FROM ChangeCredit WHERE idImporter = ?");
        odbc_execute($stmt, [$idImportador]);

        $stmt = odbc_prepare($db, "DELETE FROM AnaliseImporter WHERE idImporter = ?");
        odbc_execute($stmt, [$idImportador]);

        $stmt = odbc_prepare($db, "DELETE FROM ImporterRem WHERE idImporter = ?");
        odbc_execute($stmt, [$idImportador]);

        $stmt = odbc_prepare($db, "DELETE FROM Importer WHERE id = ?");
        odbc_execute($stmt, [$idImportador]);
    } else {
        if ($count == 0) {
            $stmt = odbc_prepare($db, "UPDATE Importer SET state = 7 WHERE id = ?");
            odbc_execute($stmt, [$idImportador]);

            $stmt = odbc_prepare($db, "SELECT analysis FROM ChangeCredit WHERE idImporter = ? ORDER BY id DESC");
            odbc_execute($stmt, [$idImportador]);
            odbc_fetch_row($stmt);
            $analise = odbc_result($stmt, 1);

            $stmt = odbc_prepare($db, "INSERT INTO ChangeCredit (idImporter, userIdChangeCredit, state) VALUES (?, ?, 7)");
            odbc_execute($stmt, [$idImportador, $userID]);

            if ($state != 1 && $state != 3) {
                $stmt = odbc_prepare($db, "SELECT MAX(id) FROM ChangeCredit WHERE idImporter = ?");
                odbc_execute($stmt, [$idImportador]);
                $idcc = odbc_result($stmt, 1);

                $stmt = odbc_prepare($db, "UPDATE ChangeCredit SET analysis = ?, monitor = 0 WHERE id = ?");
                odbc_execute($stmt, [$analise, $idcc]);
            }
        } else {
            $new_state = 9;

            $stmt = odbc_prepare($db, "UPDATE Importer SET state = ? WHERE id = ?");
            odbc_execute($stmt, [$new_state, $idImportador]);

            $stmt = odbc_prepare($db, "SELECT analysis FROM ChangeCredit WHERE idImporter = ? ORDER BY id DESC");
            odbc_execute($stmt, [$idImportador]);
            odbc_fetch_row($stmt);
            $analise = odbc_result($stmt, 1);

            $stmt = odbc_prepare($db, "INSERT INTO ChangeCredit (idImporter, userIdChangeCredit, state, monitor) VALUES (?, ?, ?, 0)");
            odbc_execute($stmt, [$idImportador, $userID, $new_state]);

            if ($state != 3) {
                $stmt = odbc_prepare($db, "SELECT MAX(id) FROM ChangeCredit WHERE idImporter = ?");
                odbc_execute($stmt, [$idImportador]);
                $idcc = odbc_result($stmt, 1);

                $stmt = odbc_prepare($db, "UPDATE ChangeCredit SET analysis = ?, monitor = 0 WHERE id = ?");
                odbc_execute($stmt, [$analise, $idcc]);
            }
        }
    }

    if (!$stmt) {
        $msg = "Problemas na remoção do importador";
    }
}

   else {
        $stmt = odbc_prepare($db, "DELETE FROM ImporterRem WHERE idImporter = ?");
        $nao_exclui = odbc_execute($stmt, [$idImportador]);
        //echo "limei: " . "DELETE FROM ImporterRem WHERE idImporter=$idImportador";
    }
	 
}
$importers .= "</ul>";

//die();

$stmt = odbc_prepare($db, 
    "SELECT * FROM ImporterRem WHERE idImporter IN " .
    "(SELECT id FROM Importer WHERE idInform = ?) AND state = 1"
);
odbc_execute($stmt, [$idInform]);

if (!odbc_fetch_row($stmt)) { // se não tem mais importadores a remover, mata a notificação
    $notif->doneRole($idNotification, $db);
}

?>
