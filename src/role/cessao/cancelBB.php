<?php

$idCDBB = $_REQUEST['idCDBB'];
$idInform = $_REQUEST['idInform'];
$tipo = $_REQUEST['tipo'];
$userID = $_SESSION['userID'];



$qry = "SELECT a.id, a.name, c.login 
        FROM Role a
        INNER JOIN UserRole b ON b.idRole = a.id
        INNER JOIN Users c ON c.id = b.idUser
        WHERE c.id = ? AND c.perfil = ?
        ORDER BY a.name, c.login";

$cur = odbc_prepare($db, $qry);

odbc_execute($cur, [$_SESSION['userID'], $_SESSION['pefil']]);

$x = 0;
$role = [];

while (odbc_fetch_row($cur)) {
  $x++;
  $name = odbc_result($cur, 'name');
  $id = odbc_result($cur, 'id');
  $role[$name] = $id . '<br>';
}

//print $idCDBB.'<br>'.$idInform.'<br>'.$tipo.'<br>'.$userID.'<br>Role: '. $role["bancoBB"];
//break;

//Alterado HiCom mes 04

odbc_free_result($qry);

$qry = "SELECT startValidity 
        FROM Inform 
        WHERE startValidity >= GETDATE() - 30 
          AND id = ?";

$x = odbc_prepare($db, $qry);

odbc_execute($x, [$idInform]);



if (odbc_fetch_row($x)) {
  $data = "'" . odbc_result($x, 1) . "'";
} else {
  $data = 'getdate()';
}

odbc_free_result($x);

if ($role["bancoBB"]) {
  $q = "UPDATE CDBB SET status = 4, dateCancel = ?, dt_cancel_true = GETDATE() WHERE id = ?";
  $cur = odbc_prepare($db, $q);
  odbc_execute($cur, [$data, $idCDBB]);

  $query = "
        SELECT cdbb.codigo, cdbb.dateClient, inf.name, inf.id
        FROM CDBB cdbb
        JOIN Inform inf ON (inf.id = cdbb.idInform)
        WHERE cdbb.id = ?";
  $cur = odbc_prepare($db, $query);
  odbc_execute($cur, [$idCDBB]);

  $codigo = odbc_result($cur, 1);
  $dateEnv = odbc_result($cur, 2);
  $infName = odbc_result($cur, 3);
  $idInform = odbc_result($cur, 4);

  list($ano, $mes, $dia) = explode('-', $dateEnv);
  $c = $codigo . "/" . $ano;
  $tipo = "BB";

} else if ($role["bancoParc"]) {
  $q = "UPDATE CDParc SET status = 4, dateCancel = ?, dt_cancel_true = GETDATE() WHERE id = ?";
  $cur = odbc_prepare($db, $q);
  odbc_execute($cur, [$data, $idCDBB]);

  $query = "
        SELECT cdbb.codigo, cdbb.dateClient, inf.name, inf.id
        FROM CDParc cdbb
        JOIN Inform inf ON (inf.id = cdbb.idInform)
        WHERE cdbb.id = ?";
  $cur = odbc_prepare($db, $query);
  odbc_execute($cur, [$idCDBB]);

  $codigo = odbc_result($cur, 1);
  $dateEnv = odbc_result($cur, 2);
  $infName = odbc_result($cur, 3);
  $idInform = odbc_result($cur, 4);

  list($ano, $mes, $dia) = explode('-', $dateEnv);
  $c = $codigo . "/" . $ano;
  $tipo = "Parc";

} else {
  $q = "UPDATE CDOB SET status = 4, dateCancel = ?, dt_cancel_true = GETDATE() WHERE id = ?";
  $cur = odbc_prepare($db, $q);
  odbc_execute($cur, [$data, $idCDBB]);

  $query = "
        SELECT cdbb.codigo, cdbb.dateClient, inf.name, inf.id
        FROM CDOB cdbb
        JOIN Inform inf ON (inf.id = cdbb.idInform)
        WHERE cdbb.id = ?";
  $cur = odbc_prepare($db, $query);
  odbc_execute($cur, [$idCDBB]);

  $codigo = odbc_result($cur, 1);
  $dateEnv = odbc_result($cur, 2);
  $infName = odbc_result($cur, 3);
  $idInform = odbc_result($cur, 4);

  list($ano, $mes, $dia) = explode('-', $dateEnv);
  $c = $codigo . "/" . $ano;
  $tipo = "OB";
}


$r = $notif->cdbbCancela($userID, $idCDBB, $idInform, $db, $infName, $c, $tipo);
if (!$r) {
  $msg = "problemas na criação da notificação";
  $ok = false;
}
?>