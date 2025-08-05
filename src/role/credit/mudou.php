<?php 


if($done){
  $notif->doneRole($idNotification, $db);
  return;
}

$cur = odbc_exec($db, "select name, c_Coface_Imp from Importer where id=$idBuyer");
$importer = odbc_result($cur, 1);

$x = odbc_exec($db, "select name, contrat from Inform where id=$idInform");
$nameExpo = odbc_result($x, 1);
$ciExpo = odbc_result($x, 2);
?>
