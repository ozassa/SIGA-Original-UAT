<?php // converte a data de yyyy-mm-dd para dd/mm/yyyy
if(! function_exists('ymd2dmy')){
  function ymd2dmy($d){
    if(preg_match("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $d, $v)){
      return "$v[3]/$v[2]/$v[1]";
    }
    return $d;
  }
}

// converte a data de  dd/mm/yyyy para yyyy-mm-dd 00:00:00.000
if(! function_exists('dmy2ymd')){
  function dmy2ymd($d){
    global $msg;
    if(preg_match("/([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})/", $d, $v)){
      return "$v[3]-$v[2]-$v[1] 00:00:00.000";
    }else if(preg_match("/([0-9]{1,2})/([0-9]{1,2})/([0-9]{2})/", $d, $v)){
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

$x = odbc_exec($db,
	       "SELECT d.id, d.inicio, d.periodo FROM Endosso e JOIN Inform inf on e.idInform=inf.id
                JOIN DVE d ON d.idInform=inf.id
                WHERE inf.id=$idInform and e.id=$idEndosso");
if(odbc_fetch_row($x)){
  $idDVE = odbc_result($x, 1);
  $inicio = ymd2dmy(odbc_result($x, 2));
  $periodo = odbc_result($x, 3);
  $fim = getTimeStamp(getEndDate($inicio, $periodo));
  $inicio = getTimeStamp($inicio);
}

$total = 0;

$x = odbc_exec($db,
	       "SELECT dt.idImporter, dt.totalEmbarcado FROM Endosso e JOIN Inform inf on e.idInform=inf.id
                JOIN DVE d ON d.idInform=inf.id JOIN DVEDetails dt ON dt.idDVE=d.id
                JOIN Importer imp ON imp.id=dt.idImporter
                WHERE inf.id=$idInform and e.id=$idEndosso");
while(odbc_fetch_row($x)){
  $idImporter = odbc_result($x, 1);
  $embarcado = odbc_result($x, 2);
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

// pega as parcelas q já foram pagas
$c = odbc_exec($dbSisSeg,
	       "select sum(v_Documento) from PagRec where i_Seg=$idSeg and n_Prop=$nProp
                and t_Doc in (1,2) and s_Pagamento=2 order by d_Vencimento desc");
$valor_pago = odbc_result($c, 1);

if($total > $valor_pago){ // aqui tem que gerar a parcela de ajuste
  $valor = sprintf("%.2f", $total - $valor_pago);
}

$notif->doneRole($idNotification, $db);
?>
