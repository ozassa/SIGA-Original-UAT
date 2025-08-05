<?php  //HICOM Alterado

$ok = true;
$forward = "error";
odbc_autocommit ($db, false);

$cur=odbc_exec(
	       $db,
	       "SELECT generalState, volState, segState, financState, buyersState,
                       lostState, idRegion, name, idAnt, prMax, prMin, sentOffer
                FROM Inform WHERE id = $idInform"
	       );
odbc_fetch_row($cur);

$idRegion = odbc_result($cur,7);
$name = odbc_result($cur,8);
$idAnt = odbc_result($cur, 9);
$prMax = odbc_result($cur, 10);
$prMin = odbc_result($cur, 11);
$sentOffer = odbc_result($cur, 12);

if($prMax > 0 || $prMin > 0 || $sentOffer){
  $reestudo = 1;
}

$test = 3;
$i = 1;
for (;$i <= 6; $i++) {
  if (odbc_result($cur,$i) != $test) $ok = false;
}

if ($i == 1){
  $ok = false;
}
$r = false;

if (!$ok){
  $forward = "error";
}else{
  if($mot == "Recusar"){
    $r = odbc_exec($db, "insert into TransactionLog (idUser, description) values ($userID, 'Informe recusado [$name]')");
  }

  if ($field->getField("mot") == "Aceitar") {
    // insere o segurado e os importadores no SisSeg
    $idSegurado = 0;
    if ($sisseg && !$idAnt){ // se nao for renovacao
      require_once("segSisSeg.php");
    }else if($idAnt && ! $idSegurado){ // se for renovacao
      $idSegurado = odbc_result(odbc_exec($db, "select i_Seg from Inform where id=$idAnt"), 1);
    }
    $r = false;

    if ($ok) {
      $r = odbc_exec($db,
		     "  UPDATE Inform SET".
		     "    idUser = ". $userID. ",".
		     ($idSegurado ? "    i_Seg = $idSegurado," : '').  //id_Seg
		     "   state = 3 ".
		     "  WHERE id = $idInform");
      $x = odbc_exec($db, "select id from AnaliseInform where idInform=$idInform");
      if(! odbc_fetch_row($x)){
	odbc_exec($db, "insert into AnaliseInform (idInform, inicio) values ($idInform, getdate())");
      }
      if (!$r){
	$msg = "problemas na atualização do informe";
      } else {
	//HICOM Alterado por GPC 28/04/2004 - incluir o not in.....  e hold = 0
	$x = odbc_exec($db, "select imp.* from Importer imp where imp.hold=0 and imp.idInform=$idInform and imp.state=1 and  (imp.id NOT IN (SELECT idImporter FROM ImporterRem WHERE idImporter = imp.id))");
	
	
	
	if(odbc_fetch_row($x)){
	  //echo "Envia para credito<BR>";
	  $r = $notif->newCredit($userID, $name, $idInform, $db, 12);
	  if (!$r){
	    $msg = "A notificação já foi enviada para a área de crédito. Clique em voltar para retornar.";
	  }
	}else if($reestudo){
	  //echo "Envia para Tarifacao<BR>";
	  $r = $notif->newTarif($userID, $name, $idInform, $db);
	  $r = odbc_exec($db, "UPDATE Inform SET state = 4 WHERE id = $idInform");
	}
      }
    }
  } else if ($mot == "OK") {
    $r = true;
  } else {
    $r = odbc_exec($db,
		   "UPDATE Inform SET".
		   "  state = 9".
		   "  WHERE id = $idInform");
    $x = odbc_exec($db, "select id, fim from AnaliseInform where idInform=$idInform");
    if(odbc_fetch_row($x)){
      $id = odbc_result($x, 1);
      $fim = odbc_result($x, 2);
      if(! $fim){
	odbc_exec($db, "update AnaliseInform set fim=getdate() where id=$id");
      }
    }
  }

  if ($r) {
    if ($notif->doneRole($idNotification, $db)) {
      odbc_commit ($db);
      if(! $msg){
	if ($mot == 'OK')
	  $msg = "Informe disponível para alterações do cliente";
	else if ($mot == 'Aceitar')
	  $msg = 'Informe aceito';
	else $msg = "Processo Cancelado";
      }
      $forward = "success";
    } else {
      $msg = "Problemas na desativação da notificação";
      odbc_rollback ($db);
    }
  }
}
odbc_autocommit ($db, true); 

?>
