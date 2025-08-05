<?php 

$idInform    = $_REQUEST["idInform"];
$name_query  = $_REQUEST["name_query"];
$action      = $_REQUEST["action"];
$submit      = $_REQUEST["submit"];
$pais        = $_REQUEST["pais"];

if ($action) {
    if ($action == 1) {
        $field = 'endosso';
    } else if ($action == 2) {
        $field = 'calcPA';
    }

    $val = $_REQUEST["val"];
    $idImporter = $_REQUEST["idImporter"];

    $updateQuery = "UPDATE Importer SET $field = ? WHERE id = ?";
    $stmt = odbc_prepare($db, $updateQuery);
    odbc_execute($stmt, [$val, $idImporter]);
}

if ($submit) {
    $query = 
        "SELECT Importer.name, Importer.limCredit, Importer.idCountry, 
                Cast(Country.code as varchar) + Right('000000' + Importer.c_Coface_Imp, 6), 
                Country.name, Importer.id, Country.code,
                ch.creditTemp, ch.limTemp, Importer.state, Importer.endosso,
                Importer.idTwin, Importer.hold, Importer.calcPA, Importer.idAprov, Importer.validityDate
        FROM Importer
        JOIN Country ON Importer.idCountry = Country.id
        JOIN Inform ON Importer.idInform = Inform.id
        JOIN ChangeCredit ch ON ch.id = (
            SELECT MAX(cc.id) 
            FROM ChangeCredit cc 
            WHERE cc.idImporter = Importer.id
        )
        WHERE Inform.id = ?"
        . ($name_query ? " AND Importer.name LIKE ?" : '')
        . ($pais != 0 ? " AND Country.id = ?" : '')
        . " ORDER BY Importer.name";

    $params = [$idInform];
    if ($name_query) {
        $params[] = '%' . strtoupper($name_query) . '%';
    }
    if ($pais != 0) {
        $params[] = $pais;
    }

    $stmt = odbc_prepare($db, $query);
    odbc_execute($stmt, $params);
}

$xQuery = "SELECT idAnt FROM Inform WHERE id = ?";
$stmt = odbc_prepare($db, $xQuery);
odbc_execute($stmt, [$idInform]);
$idAnt = odbc_result($stmt, 1);

if ($idAnt) {
    $xQuery = "SELECT * FROM Inform WHERE id = ? AND state = 10";
    $stmt = odbc_prepare($db, $xQuery);
    odbc_execute($stmt, [$idAnt]);

    if (odbc_fetch_row($stmt)) {
        $is_renov = 1;
    }
} else {
    $statusQuery = "SELECT state FROM Inform WHERE id = ?";
    $stmt = odbc_prepare($db, $statusQuery);
    odbc_execute($stmt, [$idInform]);
    $status = odbc_result($stmt, 1);

    $xQuery = "SELECT * FROM Inform WHERE idAnt = ?";
    $stmt = odbc_prepare($db, $xQuery);
    odbc_execute($stmt, [$idInform]);

    if (odbc_fetch_row($stmt)) {
        if ($status == 10) {
            $tem_renov = 1;
        }
    }
}

?>
