<?php // converte a data de yyyy-mm-dd para dd/mm/yyyy
if(! function_exists('ymd2dmy')){
  function ymd2dmy($d){
    if(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $d, $v)){
      return "$v[3]/$v[2]/$v[1]";
    }
    return $d;
  }
}

// converte a data de  dd/mm/yyyy para yyyy-mm-dd 00:00:00.000
if(! function_exists('dmy2ymd')){
  function dmy2ymd($d){
    global $msg;
    if(preg_match("/([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $d, $v)){
      return "$v[3]-$v[2]-$v[1] 00:00:00.000";
    }else if(preg_match("/([0-9]{1,2})/([0-9]{1,2})/([0-9]{2})", $d, $v)){
      return ($v[3] + 2000). "-$v[2]-$v[1] 00:00:00.000";
    }else{
      $msg = 'Data em formato inválido (deve ser dd/mm/yyyy ou dd/mm/yy)';
      return '';
    }
  }
}

if(! function_exists('getTimeStamp')){
  function getTimeStamp($date){
    if(preg_match('/^([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})/', $date, $res)){
      return mktime(0, 0, 0, $res[2], $res[1], $res[3]);
    }else if(preg_match('/^([0-9]{1,2})/([0-9]{1,2})/([0-9]{2})/', $date, $res)){
      return mktime(0, 0, 0, $res[2], $res[1], $res[3] + 2000);
    }
  }
}

if(! function_exists('getEndDate')){
  function getEndDate($d, $n, $c = 0){
    global $idDVE, $db, $idInform;
    $num = odbc_result(odbc_exec($db, "select num from DVE where id=$idDVE"), 1);
    if($num != 12){
      if(preg_match("/([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})/", $d, $v)){
	//return date("d/m/Y", mktime(0, 0, 0, $v[2] + $n, $v[1] - 1 + $c, $v[3]));
	return date("d/m/Y", mktime(0, 0, 0, $v[2] + $n, $c, $v[3]));
      }else if(preg_match("/([0-9]{1,2})/([0-9]{1,2})/([0-9]{2})/", $d, $v)){
	//return date("d/m/Y", mktime(0, 0, 0, $v[2] + $n, $v[1] - 1 + $c, $v[3] + 2000));
	return date("d/m/Y", mktime(0, 0, 0, $v[2] + $n, $c, $v[3] + 2000));
      }
    }else{
      $end = odbc_result(odbc_exec($db, "select endValidity from Inform where id=$idInform"), 1);
      return dmy2ymd($end);
    }
  }
}

$msg = '';

// verifica se tem alguma dve q nao foi enviada
$x = odbc_exec($db, "select * from DVE where idInform=$idInform and state<>2");
if(odbc_fetch_row($x)){
  $msg = "Este cliente ainda não enviou todas as DVE's";
}else{
  $c = odbc_exec($db, "select i_Seg, idAnt, nProp, txRise, warantyInterest from Inform where id=$idInform");
  if(odbc_fetch_row($c)){
    $idSeg = odbc_result($c, 1);
    $idAnt = odbc_result($c, 2);
    $nProp = odbc_result($c, 3);
    $txRise = odbc_result($c, 4);
    $juros = odbc_result($c, 5);
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
 
    // Ve se já tem PA pra esse informe
    $query = "SELECT * FROM Endosso WHERE idInform=$idInform and tipo = 3";
 
 // $query = "SELECT d.id, d.inicio, d.periodo FROM Endosso e JOIN Inform inf on e.idInform=inf.id
 //                 JOIN DVE d ON d.idInform=inf.id WHERE inf.id=$idInform";
  
  $x = odbc_exec($db, $query);
  echo "<pre>$query </pre>";
  if(odbc_fetch_row($x)){
    $idDVE = odbc_result($x, 1);
    $inicio = ymd2dmy(odbc_result($x, 2));
    echo "Inicio: $inicio";
    $periodo = odbc_result($x, 3);
    $fim = getTimeStamp(getEndDate($inicio, $periodo));
    $inicio = getTimeStamp($inicio);
  }

  $total = 0;

 // $query = "SELECT dt.idImporter, dt.totalEmbarcado FROM Endosso e JOIN Inform inf on e.idInform=inf.id
 //                 JOIN DVE d ON d.idInform=inf.id JOIN DVEDetails dt ON dt.idDVE=d.id
 //                 JOIN Importer imp ON imp.id=dt.idImporter
 //                 WHERE inf.id=$idInform and e.id=$idEndosso";
   
    $query = "SELECT sum(dt.totalEmbarcado), d.num FROM Inform i JOIN DVE d ON d.idInform=i.id 
                  JOIN DVEDetails dt ON dt.idDVE=d.id
                  JOIN Importer imp ON imp.id=dt.idImporter
                  WHERE i.id=$idInform group by d.num";
  $x = odbc_exec($db, $query);
  echo "<pre>$query</pre>";
  echo "<pre>DVE - Total </pre> ";
  while(odbc_fetch_row($x)){
   // $idImporter = odbc_result($x, 1);
    $numDVE = odbc_result($x, 2);
    $embarcado = odbc_result($x, 1);
    $totalEmbarcado += $embarcado;
    echo "<pre>$numDVE - $embarcado </pre>";
  
    $y = odbc_exec($db,
		   "SELECT ch.credit, ch.creditDate FROM ChangeCredit ch JOIN Importer i ON i.id=ch.idImporter
                    WHERE i.calcPA=1
                    ORDER BY ch.creditDate DESC");
    while(odbc_fetch_row($y)){
      $credito = odbc_result($y, 1);
      if(! $credito){
	$credito = 0;
      }
      $creditDate = getTimeStamp(ymd2dmy(odbc_result($y, 2)));
      if($inicio <= $creditDate && $creditDate <= $fim){
	$total += min($embarcado, $credito);
	break;
      }
    
    }
   
  }
  echo "<pre>Total Embarcado = $totalEmbarcado</pre>";

  // pega as parcelas q já foram pagas
  $query = "select sum(v_Documento) from PagRec where i_Seg=$idSeg and n_Prop=$nProp
                  and t_Doc in (1,2) and s_Pagamento=2  ";
  //echo "$query";
  $c = odbc_exec($dbSisSeg, $query);
  
       $valor_pago = odbc_result($c, 1);
     echo "Total Pago = $valor_pago";
     //$total = $total * ($juros ? 1.04 : 1) * (1 + $txRise);
  
  if($totalEmbarcado > $valor_pago){ // aqui tem que gerar a parcela de ajuste
    $v_Parcela = sprintf("%.2f", $totalEmbarcado - $valor_pago);
    echo "<pre>Valor Parcela = $v_Parcela </pre>" 
    $c = odbc_exec($dbSisSeg,
		   "select i_Parcela from PagRec where i_Seg=$idSeg and
                    n_Prop=$nProp and t_Doc in (1,2) and s_Pagamento=2
                    order by d_Vencimento desc");

    // cria a nova parcela
    if(odbc_fetch_row($c)){
      $x = odbc_exec($dbSisSeg, "select * from Parcela where i_Parcela=". odbc_result($c, 1));
      $c_Coface = odbc_result($x, 'c_Coface');
      $n_Ramo = odbc_result($x, 'n_Ramo');
      $n_Sucursal = odbc_result($x, 'n_Sucursal');
      $i_Seg = odbc_result($x, 'i_Seg');
      $t_parcela = odbc_result($x, 't_parcela');
      $n_Prop = odbc_result($x, 'n_Prop');
      $n_Apolice = odbc_result($x, 'n_Apolice');
      $n_Endosso = odbc_result($x, 'n_Endosso');
      $d_Venc = date('Y-m-d 00:00:00.000', mktime(0, 0, 0, date('m'), date('d') + 15, date('Y')));
      $n_Seq_Parcela = odbc_result($x, 'n_Seq_Parcela');
      $n_Moeda = odbc_result($x, 'n_Moeda');
      $d_Parcela = odbc_result($x, 'd_Parcela');
      $s_Parcela = odbc_result($x, 's_Parcela');
      $v_IOF = sprintf("%.2f", odbc_result($x, 'v_IOF'));
      $i_BC = odbc_result($x, 'i_BC');
      $i_PR = odbc_result($x, 'i_PR');
      $v_Extenso = odbc_result($x, 'v_Extenso');
      $x_CBR = odbc_result($x, 'x_CBR');

      $x = odbc_exec($dbSisSeg,
		     "insert into Parcela (c_Coface, n_Ramo, n_Sucursal, i_Seg, t_parcela, n_Prop,
                      n_Apolice, n_Endosso, d_Venc, n_Seq_Parcela, n_Moeda, d_Parcela, s_Parcela,
                      v_IOF, i_BC, d_Pagamento, i_PR, v_Extenso, x_CBR, d_Cancelamento, v_Parcela)
                      values ($c_Coface, $n_Ramo, $n_Sucursal, $i_Seg, $t_parcela, $n_Prop, $n_Apolice,
                      $n_Endosso, '$d_Venc', $n_Seq_Parcela, $n_Moeda,'$d_Parcela', $s_Parcela,
                      $v_IOF, '$i_BC', NULL, '$i_PR', '$v_Extenso', '$x_CBR', NULL, $v_Parcela)");
      if(! $x){
	$msg = 'Erro ao criar parcela';
	$ok = false;
      }else{
	$msg = 'Parcela criada';
      }
    }
  }else{
    $msg = 'Não foi necessário criar a parcela de ajuste';
  }
}
?>
