<?php
$debug = "";

$idNotification = $_REQUEST["idNotification"];
$nArr = count($_REQUEST['check']);
$check = $_REQUEST['check'];
$idBuyer = $check[0];
$userID = $_SESSION['userID'];
$totalcheck = $_REQUEST['totalcheck'];

for ($i = 0; $i < $nArr; $i++) {
    $idBuyer = $check[$i];

    $query = "SELECT inf.state, inf.idAnt, inf.name 
              FROM Inform inf 
              JOIN Importer imp ON (imp.idInform = inf.id) 
              WHERE imp.id = ?";
    $stmt = odbc_prepare($db, $query);
    odbc_execute($stmt, [$idBuyer]);

    if (odbc_fetch_row($stmt)) {
        $state = odbc_result($stmt, 1);
        $idAnt = odbc_result($stmt, 2);
    }

    $updateQuery = "UPDATE Importer 
                    SET state = 4 
                    WHERE idInform = ? AND state = 2 AND id = ?";
    $updateStmt = odbc_prepare($db, $updateQuery);
    odbc_execute($updateStmt, [$idInform, $idBuyer]);

    if (!empty($ren_idInform)) {
        $renUpdateQuery = "UPDATE Importer 
                           SET state = 4 
                           WHERE idInform = ? AND state = 2 AND id = ?";
        $renUpdateStmt = odbc_prepare($db, $renUpdateQuery);
        odbc_execute($renUpdateStmt, [$ren_idInform, $idBuyer]);
    }
}

$debug .= "passo 1 em clientchangecredit.php: $nArr - $totalcheck - " . count($check) . " - $idNotification<br>";

if ($nArr == $totalcheck) {
    $query = "UPDATE NotificationR
              SET state = ?, i_Usuario = ?, d_Encerramento = GETDATE()
              WHERE id = ?";
    $stmt = odbc_prepare($db, $query);
    odbc_execute($stmt, [2, $_SESSION["userID"], $idNotification]);

    $debug .= "passo 43 em clientchangecredit.php: procedimento para update da notificacao<br>";
}

$state = 1;
$hold = ' AND hold = 0';
$union = '';

if ($flag_renovacao) {
    $union = "UNION SELECT Importer.id, Importer.name, Country.name, Country.code, 
                     Importer.limCredit, Importer.c_Coface_Imp, Country.id AS cId, Importer.state 
              FROM Importer, Country 
              WHERE Importer.idInform = ? AND 
                    Importer.idCountry = Country.id AND 
                    Importer.state = 6 AND Importer.creditAut = 1";
}

$xquery = "SELECT Importer.id 
           FROM Importer, Country, ChangeCredit 
           WHERE Importer.idInform = ? AND 
                 Importer.idCountry = Country.id AND 
                 ChangeCredit.idImporter = Importer.id AND 
                 (Importer.state = '2') $hold AND 
                 ChangeCredit.creditSolic > 0";
$xstmt = odbc_prepare($db, $xquery);
odbc_execute($xstmt, [$idInform]);

if (!odbc_fetch_row($xstmt)) {
    $vazio1 = 1;
    $debug .= "passo 2 em clientchangecredit.php<br>";
} else {
    $vazio1 = 0;
    $debug .= "passo 3 em clientchangecredit.php<br>";
}

if ($idAnt > 0) {
    $zquery = "SELECT Importer.id 
               FROM Importer, Country 
               WHERE Importer.idInform = ? AND 
                     Importer.idCountry = Country.id AND 
                     (Importer.state = '2' OR Importer.state = ?) $hold AND 
                     Importer.creditAut = 1 $union
               ORDER BY Importer.id";
    $zstmt = odbc_prepare($db, $zquery);
    odbc_execute($zstmt, [$idInform, $state]);

    if (!odbc_fetch_row($zstmt)) {
        $vazio2 = 1;
        $debug .= "passo 4 em clientchangecredit.php<br>";
    }

    $vazio = ($vazio1 && $vazio2) ? 1 : 0;
    $debug .= "passo 5 em clientchangecredit.php - renovacao<br>";
} else {
    $vazio = $vazio1;
    $debug .= "passo 6 em clientchangecredit.php - novo<br>";
}
?>
