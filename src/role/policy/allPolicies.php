<?php
//require("segProp.php");

require_once("../../pdfConf.php");

$namecliente = isset($_POST['nameclient']) ? trim($_POST['nameclient']) : "";

$where = '';
$qry = "SELECT policyKey, name, id, segundaVia FROM Inform";
$params = [];

if ($namecliente != '') {
    $where = " WHERE upper(name) LIKE ?";
    $qry .= $where . " order by startValidity desc";
    $paramName = '%' . strtoupper($namecliente) . '%';
    $params[] = $paramName;
}

if (!empty($params)) {
    $stmt = odbc_prepare($db, $qry);
    $cur = odbc_execute($stmt, $params);
} else {
    $cur = odbc_exec($db, $qry);
}
?>
