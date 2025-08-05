<?php 

$q = "UPDATE Sinistro SET status = 2 WHERE id = $idSinistro";
$cur = odbc_exec($db, $q);

if (!$cur) {
  $msg = "Problemas na atualização da base";
} else {
  $var = odbc_exec($db, "SELECT Inform.name, Inform.i_Seg, Inform.idAnt, Importer.name FROM Inform, Importer Where Inform.id = $idInform AND Importer.id = $idImporter");
  $name = odbc_result($var, 1);
  $i_Seg = odbc_result($var, 2);
  $nameI = odbc_result($var, 4);

  $r = $notif->doneRole($idNotification, $db);
  if ($r){
    $r = $notif->newSinistro($idInform, $name, $idInform, $db, $idImporter, $idSinistro, $nameI);
    if (!$r) {
      $msg = "problemas na criação da notificação";
      $ok = FALSE;
    }

    $q = "INSERT INTO SinistroObs (idSinistro, name, date, obs) VALUES ($idSinistro, '$user->name', getdate(),  'Aviso Sinistro')";
    $obs = odbc_exec($db, $q);
  }
}
?>
