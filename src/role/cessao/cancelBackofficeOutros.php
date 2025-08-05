<?php 
$cur = odbc_exec($db, "SELECT codigo, idInform FROM CDOB WHERE id = $idCDBB");
odbc_fetch_row($cur);
$codigo = odbc_result($cur, 1);
$idInform = odbc_result($cur, 2);
$ano = date ('Y');
$c = $codigo. "/". $ano;

$x = odbc_exec($db, "select startValidity from Inform where startValidity >= getdate() - 30 and id=$idInform");
if(odbc_fetch_row($x)){
  $data = "'". odbc_result($x, 1). "'";
}else{
  $data = 'getdate()';
}

$x = odbc_exec($db, "select name from Inform where id=$idInform");
$n = odbc_result($x, 1);

$q = "UPDATE CDOB SET status=4, dateCancel=$data WHERE id=$idCDBB";
$cur = odbc_exec($db, $q);
$notif->cdbbCancela($userID, $idCDBB, $idInform, $db, $n, $c, 'OB');
?>
