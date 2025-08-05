<?php // encerra a notificacao atual
$notif->doneRole($idNotification, $db);

// cria outra, só q para o usuario endosso
$name = odbc_result(odbc_exec($db, "select name from Inform where id=$idInform"), 1);
if($notif->newEndossoPrMin($idInform, $name, $db, $idEndosso, $idPremio, 17)){
  $msg = "Proposta enviada";
}
?>
