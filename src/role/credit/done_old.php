<?

$idInform = $field->getField("idInform");
$enviarTarif = $field->getField("enviarTarif");

$msg = "Erro no envio de notifição à TARIFAÇÃO.";

if($comm == 'goTarif'){
  $enviarTarif = 1;
  $goTarif = 1;
}else{
  $count = 0;
  $curCount  = odbc_exec($db,
			 "SELECT DISTINCT COUNT(*) FROM Importer ".
			 "WHERE idInform = $idInform AND (state=4 OR state=3)");
  if (odbc_fetch_row ($curCount)) {
    $count = odbc_result($curCount, 1);
  }
}


if ($count == 0 || $goTarif == 1) {
  $cur = odbc_exec($db, "SELECT name, state FROM Inform WHERE id = $idInform");
  if (odbc_fetch_row ($cur)) {
    $name = odbc_result($cur, 1);
    $state = odbc_result($cur, 2);

    if ($state >= 4){
      $comm = "goOk";
    }else{
      $comm = "goTarif";
    }

    if ($comm == "goTarif" && $enviarTarif == 1) {
      $r = $notif->newTarif($userID, $name, $idInform, $db);
      $o = odbc_exec($db, "UPDATE Inform SET state = 4 WHERE id = $idInform");
      $x = odbc_exec($db, "select id, fim from AnaliseInform where idInform=$idInform");
      if(odbc_fetch_row($x)){
	  $id = odbc_result($x, 1);
	  $fim = odbc_result($fim, 2);
	  if(! $fim){
	    odbc_exec($db, "update AnaliseInform set fim=getdate() where id=$id");
	  }
     } $msg = "Sucesso no envio de notificação à TARIFAÇÃO.";
    } else $msg = "Sucesso na atualização dos limites";
  }
} else $msg = "Alteração realizada com sucesso";

?>
