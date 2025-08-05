<?php

require_once("../rolePrefix.php");

header('Content-type: application/json');

$status       = isset($_POST["status"]) ? $_POST["status"] : '';
$nome         = isset($_POST["nome"]) ? $_POST["nome"] : ''; 
$executivo    = isset($_POST["executivo"]) ? $_POST["executivo"] : '';
$regiao       = isset($_POST["regiao"]) ? $_POST["regiao"] : '';
$dtinicio     = isset($_POST["dtinicio"]) ? $_POST["dtinicio"] : '';
$dtfim        = isset($_POST["dtfim"]) ? $_POST["dtfim"] : '';
$vcaex        = isset($_POST["vcaex"]) ? $_POST["vcaex"] : '';
$setatividade = isset($_POST["setatividade"]) ? $_POST["setatividade"] : '';

$params = [];
$query = "SELECT 
    inf.id, inf.name, u.name as user_name, inf.state, inf.nick, inf.idAnt, 
    inf.startValidity, inf.endValidity, inf.idUser, inf.idRegion, 
    inf.cnpj, inf.currency, inf.Ga, inf.contrat, inf.n_Apolice AS n_Apolice
    FROM Inform inf 
    LEFT JOIN Users u ON u.id = inf.idUser 
    INNER JOIN Moeda M ON M.i_Moeda = inf.currency
    INNER JOIN Campo_Item Situacao ON Situacao.i_Campo = 100 AND Situacao.i_Item = inf.state
    WHERE 1=1";

if ($status == "9") {
    $query .= " AND inf.id IN (
        SELECT MAX(inf.id) 
        FROM Inform inf 
        LEFT JOIN Users u ON u.id = inf.idUser 
        WHERE inf.state = 9 
        GROUP BY inf.cnpj
    )";
} elseif ($status == "12") {
    $query .= " AND inf.state NOT IN (9, 10, 6)";
} else {
    if (!empty($status)) {
        $query .= " AND inf.state = ?";
        $params[] = $status;
    }
}

if (!empty($nome)) {
    $query .= " AND UPPER(inf.name) LIKE ?";
    $params[] = "%" . strtoupper($nome) . "%";
}

if (!empty($executivo)) {
    $query .= " AND u.id = ?";
    $params[] = $executivo;
}

if (!empty($regiao)) {
    $query .= " AND inf.idRegion = ?";
    $params[] = $regiao;
}

if (!empty($dtinicio) && !empty($dtfim)) {
    $query .= " AND inf.startValidity BETWEEN ? AND ?";
    $params[] = date("Y-m-d", strtotime(str_replace("/", "-", $dtinicio)));
    $params[] = date("Y-m-d", strtotime(str_replace("/", "-", $dtfim)));
} elseif (!empty($dtinicio)) {
    $query .= " AND inf.startValidity >= ?";
    $params[] = date("Y-m-d", strtotime(str_replace("/", "-", $dtinicio)));
} elseif (!empty($dtfim)) {
    $query .= " AND inf.startValidity <= ?";
    $params[] = date("Y-m-d", strtotime(str_replace("/", "-", $dtfim)));
}

if (!empty($vcaex)) {
    if ($vcaex == "1") {
        $v1 = 1;
        $v2 = 10000000;
        $query .= " AND ((inf.prMin * CASE WHEN inf.warantyInterest = 1 THEN 1.04 ELSE 1 END) * 
                  ((1 + inf.txRise / 100) / inf.numParc) * inf.numParc) / 
                  (inf.txMin * (1 + inf.txRise / 100)) BETWEEN ? AND ?";
        $params[] = $v1;
        $params[] = $v2;
    } elseif ($vcaex == "2") {
        $v1 = 10000000;
        $v2 = 50000000;
        $query .= " AND ((inf.prMin * CASE WHEN inf.warantyInterest = 1 THEN 1.04 ELSE 1 END) * 
                  ((1 + inf.txRise / 100) / inf.numParc) * inf.numParc) / 
                  (inf.txMin * (1 + inf.txRise / 100)) BETWEEN ? AND ?";
        $params[] = $v1;
        $params[] = $v2;
    } elseif ($vcaex == "3") {
        $v1 = 50000000;
        $query .= " AND ((inf.prMin * CASE WHEN inf.warantyInterest = 1 THEN 1.04 ELSE 1 END) * 
                  ((1 + inf.txRise / 100) / inf.numParc) * inf.numParc) / 
                  (inf.txMin * (1 + inf.txRise / 100)) > ?";
        $params[] = $v1;
    }
}

if (!empty($setatividade)) {
    $query .= " AND inf.idSector = ?";
    $params[] = $setatividade;
}

$query .= " ORDER BY inf.currency, inf.contrat, inf.state, inf.n_Apolice DESC";

$cur = odbc_prepare($db, $query);
odbc_execute($cur, $params);

?>
