<?php $cur = odbc_exec($db, "select idInform, state, inicio, periodo, num, sentDate from DVE where id=$idDVE");
$idInform = odbc_result($cur, 1);
$state = odbc_result($cur, 2);
$inicio = ymd2dmy(odbc_result($cur, 3));
$fim = getEndDate($inicio, odbc_result($cur, 4));
$num = odbc_result($cur, 5);
$sentDate = ymd2dmy(odbc_result($cur, 6));

$cur = odbc_exec($db, "select i_Seg, startValidity, endValidity, name, idAnt from Inform where id=$idInform");
if(odbc_fetch_row($cur)){
  $idSeg = odbc_result($cur, 1);
  $start = ymd2dmy(odbc_result($cur, 2));
  $end   = ymd2dmy(odbc_result($cur, 3));
  $name  = odbc_result($cur, 4);
  $idAnt = odbc_result($cur, 5);
  if(! $idSeg){
    $idSeg = odbc_result(odbc_exec($db, "select i_Seg from Inform where id=$idAnt"), 1);
  }
  $cc = odbc_exec($dbSisSeg, "select n_Apolice from Apolice where i_Seg=$idSeg order by n_Apolice desc");
  odbc_fetch_row($cc);
  $apolice = odbc_result($cc, 1);
}

odbc_autocommit($db, false);
$ok = true;

if($dve_action == 'include_comment'){
  if(! trim($texto)){
    $msg = 'Comentário inválido';
  }else{
    $cur = odbc_exec($db, "insert into DVEComment (idDVE, texto) values ($idDVE, '$texto')");
    if(! $cur){
      $msg = 'Erro ao incluir comentário';
      $ok = false;
    }else{
      $msg = 'Comentário incluído';
    }
  }
}else if($dve_action == 'cancel'){
  $c = odbc_exec($db, "update Inform set state=12 where id=$idInform");
  if(! $c){
    $ok = false;
    $msg = "Erro ao cancelar apólice";
  }else{
    $msg = "Apólice cancelada";
  }
  $x = odbc_exec($db, "select id, fim from AnaliseInform where idInform=$idInform");
  if(odbc_fetch_row($x)){
    $id = odbc_result($x, 1);
    $fim = odbc_result($fim, 2);
    if(! $fim){
      odbc_exec($db, "update AnaliseInform set fim=getdate() where id=$id");
    }
  }
  $notif->doneRole($idNotification, $db);
}else if($dve_action == 'reactivate'){
  $c = odbc_exec($db, "update Inform set state=10 where id=$idInform");
  if(! $c){
    $ok = false;
    $msg = "Erro ao reativar apólice";
  }else{
    $msg = "Apólice reativada";
  }
  $x = odbc_exec($db, "select id, fim from AnaliseInform where idInform=$idInform");
  if(odbc_fetch_row($x)){
    $id = odbc_result($x, 1);
    $fim = odbc_result($fim, 2);
    if(! $fim){
      odbc_exec($db, "update AnaliseInform set fim=getdate() where id=$id");
    }
  }
  $notif->doneRole($idNotification, $db);
}

if($ok){
  odbc_commit($db);
}else{
  odbc_rollback($db);
}

odbc_autocommit($db, true);

$num_comments = odbc_result(odbc_exec($db, "select count(*) from DVEComment where idDVE=$idDVE"), 1);
$cur = odbc_exec($db, "select data, texto from DVEComment where idDVE=$idDVE order by data");
?>
