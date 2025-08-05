<?php // gera endosso, carta de encaminhamento e as parcelas

// parcelas: verifica se houve mudança no valor do premio
if($premio_min != $premio_min_old){
  $c = odbc_exec($db, "select i_Seg, idAnt from Inform where id=$idInform");
  if(odbc_fetch_row($c)){
    $idSeg = odbc_result($c, 1);
    $idAnt = odbc_result($c, 2);
  }
  if(! $idSeg){
    $idSeg = odbc_result(odbc_exec($db, "select i_Seg from Inform where id=$idAnt"), 1);
  }

  // calcula o numero de parcelas que ainda nao venceram
  $parcelas_restantes = 0;
  $c = odbc_exec($dbSisSeg,
		 "select v_Parcela from Parcela where i_Seg=$idSeg and d_Venc > getdate() and t_Parcela=2");
  while(odbc_fetch_row($c)){
    $valor = odbc_result($c, 1);
    $parcelas_restantes++;
  }

  if($premio_min > $premio_min_old){ // aumento no valor do premio
    $diferenca = $premio_min - $premio_min_old;
    $valor_novas = $diferenca / $parcelas_restantes; // valor das novas parcelas
  }else{ // diminuicao no valor do premio

  }
  $query = "update Inform set txRise='0', prMin=$premio_min, txMin=$tx_min  where id=$idInform";
  $c = odbc_exec($db, $query);
}

$query = "update Inform set idSector=$new_idSector, products=$new_natureza  where id=$idInform";
$c = odbc_exec($db, $query);

$query1 = "update Endosso set dateEmission = getdate() where id=$idEndosso";
$c1 = odbc_exec($db, $query1);

$r = $notif->doneRole($idNotification, $db);

?>
