<?php 

$query = "Select name, state, endValidity, startValidity, codProd, idAnt, tipoDve from Inform where id = $idInform";
$cur = odbc_exec($db, $query);
if(odbc_fetch_row($cur)){
  $nameCl = odbc_result($cur, 1);
  $statusCl = odbc_result($cur, 2);
  $endValidity = odbc_result($cur, 3);
  $startValidity = odbc_result($cur, 4);
  $codProd = odbc_result($cur, 5);
  $idAnt = odbc_result($cur, 6);
  $tipoDve = odbc_result($cur, 7);
}

$r = odbc_exec($db, "select id from Inform where idAnt=$idInform AND state <> 9");
if(odbc_fetch_row($r)){
  $idRenovacao = odbc_result($r, 1);
  $possui_renovacao = 1;
}

// $dves = odbc_exec($db, "select * from DVE where idInform=$idInform and state=2 order by num");

?>
