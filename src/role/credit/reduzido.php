<?php

if(!$finish){
	$finish  = $_REQUEST['finish'];
}


if($finish){
  $notif->doneRole($idNotification, $db);
}else{
  $q = "SELECT contrat, name, idAnt, dateEmission FROM Inform where id = $idInform";
  $d = odbc_exec($db, $q);
  if(odbc_fetch_row($d)){
    $contrat = odbc_result($d, 1);
    $nameSegurado = odbc_result($d, 2);
    $idAnt = odbc_result($d, 3);
    $dateEmission = odbc_result($d, 4);
  }
  $x = odbc_exec($db, "select name from Importer where id=$idBuyer");
  $importer = odbc_result($x, 1);
}

?>
