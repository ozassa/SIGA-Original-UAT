<?php
// verifica se esta é a DVE mais antiga que deve ser enviada
$x = odbc_exec($db, "select num, state from DVE where id=$idDVE");
$num = odbc_result($x, 1);
$state = odbc_result($x, 2);
if($state == 2){
  $msg = 'Esta DVE já foi enviada';
}else{
  $x = odbc_exec($db, "select min(num) from DVE where idInform=$idInform and state=1"); // a mais antiga q nao foi enviada
  $min = odbc_result($x, 1);

  if($num != $min && $state == 1){ // se nao for, nao envia
    $msg = "Primeiro você deve enviar a DVE mais antiga que ainda estiver devendo";
  }else{
    $name = odbc_result(odbc_exec($db, "select name from Inform where id=$idInform"), 1);
    if(! odbc_exec($db, "update DVE set state=2, sentDate=getdate() where id=$idDVE")){
      $msg = "Erro ao enviar DVE";
    }else{
      if(! $notif->newDVE($userID, $idDVE, $idInform, $name, $db)){
      echo "Erro ao enviar notificação para DVE";
      }else $msg = 'DVE enviada';
    }
  }
}
?>
