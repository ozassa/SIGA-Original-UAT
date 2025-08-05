<?php // gera endosso, carta de encaminhamento e as parcelas
odbc_autocommit($db, false);
odbc_autocommit($dbSisSeg, false);
$ok = true;

// parcelas: verifica se houve mudança no valor do premio
if($premio_min != $premio_min_old){
  $mudou_premio = 1;
  $c = odbc_exec($db, "select i_Seg, idAnt, nProp, contrat from Inform where id=$idInform");
  if(odbc_fetch_row($c)){
    $idSeg = odbc_result($c, 1);
    $idAnt = odbc_result($c, 2);
    $nProp = odbc_result($c, 3);
    $contrat = odbc_result($c, 4);
  }
  if(! $idSeg){
    $idSeg = odbc_result(odbc_exec($db, "select i_Seg from Inform where id=$idAnt"), 1);
  }
  if(! $nProp){
    $x = odbc_exec($dbSisSeg,
		   "select n_Prop from Proposta where i_Seg=$idSeg order by n_Prop desc");
    if(odbc_fetch_row($x)){
      $nProp = odbc_result($x, 1);
    }
  }

  if($premio_min > $premio_min_old){ // aumento no valor do premio
    $diferenca = $premio_min - $premio_min_old;

    // ve se ainda tem parcelas a pagar
    $c = odbc_exec($dbSisSeg,
		   "select count(*) from Parcela where i_Seg=$idSeg and d_Venc>getdate() and t_parcela=2");
    if(odbc_fetch_row($c)){
      $parcelas_restantes = odbc_result($c, 1);
    }

    if($parcelas_restantes > 0){ // se tiver N parcelas a pagar, cria mais N parcelas novas
      $valor = $diferenca / $parcelas_restantes; // valor das novas parcelas
      $x = odbc_exec($dbSisSeg,
		     "select * from Parcela where i_Seg=$idSeg and d_Venc>getdate() and t_parcela=2");
    }else{ // se nao tiver mais parcelas a pagar, cria apenas uma parcela
      $valor = $diferenca;
      $x = odbc_exec($dbSisSeg,
		     "select max(i_Parcela) from Parcela where i_Seg=$idSeg and n_Prop=$nProp");
      $idParc = odbc_result($x, 1);
      $x = odbc_exec($dbSisSeg, "select * from Parcela where i_Parcela=$idParc");
      $vencimento = date('Y-m-d 00:00:00.000', mktime(0, 0, 0, date('m'), date('d') + 15, date('Y')));
    }

    // cria as novas parcelas
    while(odbc_fetch_row($x)){
      $p = array();

      $p[c_Coface] = $contrat;
      $p[n_Ramo] = odbc_result($x, 'n_Ramo');
      $p[n_Sucursal] = odbc_result($x, 'n_Sucursal');
      $p[i_Seg] = odbc_result($x, 'i_Seg');
      $p[t_parcela] = odbc_result($x, 't_parcela');
      $p[n_Prop] = odbc_result($x, 'n_Prop');
      $p[n_Apolice] = odbc_result($x, 'n_Apolice');
      $p[n_Endosso] = odbc_result($x, 'n_Endosso');
      if($vencimento){
	$p[d_Venc] = $vencimento;
      }else{
	$p[d_Venc] = odbc_result($x, 'd_Venc');
      }
      $p[n_Seq_Parcela] = odbc_result($x, 'n_Seq_Parcela');
      $p[n_Moeda] = odbc_result($x, 'n_Moeda');
      $p[s_Parcela] = odbc_result($x, 's_Parcela');
      $p[v_IOF] = sprintf("%.2f", odbc_result($x, 'v_IOF'));
      $p[i_BC] = odbc_result($x, 'i_BC');
      $p[i_PR] = odbc_result($x, 'i_PR');
      $p[v_Extenso] = odbc_result($x, 'v_Extenso');
      $p[x_CBR] = odbc_result($x, 'x_CBR');
      $p[v_Parcela] = sprintf("%.2f", $valor); // valor das novas parcelas

      $x = odbc_exec($dbSisSeg,
		     "insert into Parcela (c_Coface, n_Ramo, n_Sucursal, i_Seg, t_parcela, n_Prop,
                      n_Apolice, n_Endosso, d_Venc, n_Seq_Parcela, n_Moeda, d_Parcela, s_Parcela,
                      v_IOF, i_BC, d_Pagamento, i_PR, v_Extenso, x_CBR, d_Cancelamento, v_Parcela)
                      values ($p[c_Coface], $p[n_Ramo], $p[n_Sucursal], $p[i_Seg], $p[t_parcela], $p[n_Prop],
                      $p[n_Apolice], $p[n_Endosso], '$p[d_Venc]', $p[n_Seq_Parcela], $p[n_Moeda], getdate(),
                      $p[s_Parcela], $p[v_IOF], '$p[i_BC]', NULL, '$p[i_PR]', '$p[v_Extenso]', '$p[x_CBR]',
                      NULL, $p[v_Parcela])");
      if(! $x){
	$msg = 'Erro ao criar parcela'. odbc_errormsg();
	$ok = false;
      }else{
	$y = odbc_exec($dbSisSeg,
		       "select max(i_Parcela) from Parcela where i_Seg=$p[i_Seg] and n_Prop=$p[n_Prop] and n_Endosso=$p[n_Endosso]");
	$p[i_Parcela] = odbc_result($y, 1);
	$parcelas[] = $p;
      }
    } // while
  }else{ // diminuicao no valor do premio
    $diferenca = $premio_min_old - $premio_min;
    $valor_cancelado = 0;

    // pega as parcelas q ainda nao foram pagas
    $c = odbc_exec($dbSisSeg,
		   "select v_Documento, i_Parcela from PagRec where i_Seg=$idSeg and n_Prop=$nProp
                    and t_Doc in (1,2) and s_Pagamento=1 order by d_Vencimento desc");
    while(odbc_fetch_row($c) && ($valor_cancelado < $diferenca)){ // cancela as nao pagas ate o valor for maior ou igual a diferenca
      $valor_cancelado += odbc_result($c, 1);
      $idParcela = odbc_result($c, 2);
      $x = odbc_exec($dbSisSeg, "update PagRec set s_Pagamento=3 where i_Parcela=$idParcela");
      $z = odbc_exec($dbSisSeg, "update Parcela set s_Parcela=3 where i_Parcela=$idParcela");
      if(! $x || ! $z){
	$ok = false;
	$msg = 'Erro ao atualizar parcelas';
	break;
      }
    }

    if($valor_cancelado > $diferenca && $idParcela){ // gerar uma parcela a mais
      $p = array();
      $p[v_Parcela] = sprintf("%.2f", $valor_cancelado - $diferenca);

      $x = odbc_exec($dbSisSeg, "select * from Parcela where i_Parcela=$idParcela");
      $p[c_Coface] = odbc_result($x, 'c_Coface');
      $p[n_Ramo] = odbc_result($x, 'n_Ramo');
      $p[n_Sucursal] = odbc_result($x, 'n_Sucursal');
      $p[i_Seg] = odbc_result($x, 'i_Seg');
      $p[t_parcela] = odbc_result($x, 't_parcela');
      $p[n_Prop] = odbc_result($x, 'n_Prop');
      $p[n_Apolice] = odbc_result($x, 'n_Apolice');
      $p[n_Endosso] = odbc_result($x, 'n_Endosso');
      $p[d_Venc] = date('Y-m-d 00:00:00.000', mktime(0, 0, 0, date('m'), date('d') + 15, date('Y')));
      $p[n_Seq_Parcela] = odbc_result($x, 'n_Seq_Parcela');
      $p[n_Moeda] = odbc_result($x, 'n_Moeda');
      $p[s_Parcela] = odbc_result($x, 's_Parcela');
      $p[v_IOF] = sprintf("%.2f", odbc_result($x, 'v_IOF'));
      $p[i_BC] = odbc_result($x, 'i_BC');
      $p[d_Pagamento] = odbc_result($x, 'd_Pagamento');
      $p[i_PR] = odbc_result($x, 'i_PR');
      $p[v_Extenso] = odbc_result($x, 'v_Extenso');
      $p[x_CBR] = odbc_result($x, 'x_CBR');
      $p[d_Cancelamento] = odbc_result($x, 'd_Cancelamento');
      $x = odbc_exec($dbSisSeg,
		     "insert into Parcela (c_Coface, n_Ramo, n_Sucursal, i_Seg, t_parcela, n_Prop,
                      n_Apolice, n_Endosso, d_Venc, n_Seq_Parcela, n_Moeda, d_Parcela, s_Parcela,
                      v_IOF, i_BC, d_Pagamento, i_PR, v_Extenso, x_CBR, d_Cancelamento, v_Parcela)
                      values ($p[c_Coface], $p[n_Ramo], $p[n_Sucursal], $p[i_Seg], $p[t_parcela], $p[n_Prop], $p[n_Apolice],
                      $p[n_Endosso], '$p[d_Venc]', $p[n_Seq_Parcela], $p[n_Moeda], getdate(), $p[s_Parcela],
                      $p[v_IOF], '$p[i_BC]', NULL, '$p[i_PR]', '$p[v_Extenso]', '$p[x_CBR]', NULL, $p[v_Parcela])");
      if(! $x){
	$ok = false;
	$msg = 'Erro ao criar parcela';
      }else{
	$y = odbc_exec($dbSisSeg,
		       "select max(i_Parcela) from Parcela where i_Seg=$p[i_Seg] and n_Prop=$p[n_Prop] and n_Endosso=$p[n_Endosso]");
	$p[i_Parcela] = odbc_result($y, 1);
	$parcelas[] = $p;
      }
    }
  }
}

$query = "update Endosso set state=2 where id=$idEndosso";
$c = odbc_exec($db, $query);
if(! $c){
  $msg = "Erro ao mudar status do endosso";
  $ok = false;
  return;
}else{
  $query =
     "update Inform set txRise='0'".
     (isset($premio_min) ? ", prMin=$premio_min" : '').
     (isset($tx_min) ? ", txMin=". ($tx_min / 100) : "").
     (isset($new_sector) ? ", idSector=$new_sector" : "").
     (isset($new_natureza) ? ", products='$new_natureza'" : "").
     " where id=$idInform";
  $c = odbc_exec($db, $query);
  if($new_sector || $new_natureza){
    $mudou_natureza = 1;
  }
}

$query1 = "update Endosso set dateEmission = getdate() where id=$idEndosso";
$c1 = odbc_exec($db, $query1);

$r = $notif->doneRole($idNotification, $db);
if($ok && $c && $r){
  odbc_commit($db);
  odbc_commit($dbSisSeg);
}else{
  $msg = "Erro ao atualizar informe";
  odbc_rollback($db);
  odbc_rollback($dbSisSeg);
}

odbc_autocommit($db, true);
odbc_autocommit($dbSisSeg, true);
?>
