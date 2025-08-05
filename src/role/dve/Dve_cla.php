<?php //Alterado HiCom mes 04
//Alterado HiCom 19/10/04 (Gustavo - adicionei um item de menu para Parcela de Ajuste)

// converte a data de yyyy-mm-dd para dd/mm/yyyy
if(! function_exists('ymd2dmy')){
  function ymd2dmy($d){
    if(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $d, $v)){
      return "$v[3]/$v[2]/$v[1]";
    }
    return $d;
  }
}

// converte a data de yyyy-mm-dd para dd/mm/yy
if(! function_exists('ymd2dmy2')){
  function ymd2dmy2($d){
    if(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $d, $v)){
      return "$v[3]/$v[2]/". sprintf("%02d", ($v[1] - 2000));
    }
    return $d;
  }
}

// converte a data de  dd/mm/yyyy para yyyy-mm-dd 00:00:00.000
if(! function_exists('dmy2ymd')){
  function dmy2ymd($d){
    global $msg;
    if(preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})/", $d, $v)){
      return "$v[3]-$v[2]-$v[1] 00:00:00.000";
    }else if(preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2})/", $d, $v)){
      return ($v[3] + 2000). "-$v[2]-$v[1] 00:00:00.000";
    }else{
      $msg = 'Data em formato inválido (deve ser dd/mm/yyyy ou dd/mm/yy): '. $d;
      return '';
    }
  }
}

if(! function_exists('getTimeStamp')){
  function getTimeStamp($date){
    if(preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})/', $date, $res)){
      return mktime(0, 0, 0, $res[2], $res[1], $res[3]);
    }else if(preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2})/', $date, $res)){
      return mktime(0, 0, 0, $res[2], $res[1], $res[3] + 2000);
    }
  }
}

if(! function_exists('check_dates')){
  function check_dates($embarque, $vencimento){
    // verifica se as datas estao corretas
    global $idDVE, $msg, $db;

    if(! $embarque){
      $msg = "A data de embarque é obrigatória";
      return false;
    }
    if(! $vencimento){
      $msg = "A data de vencimento é obrigatória";
      return false;
    }

    $emb = getTimeStamp($embarque);
    $venc = getTimeStamp($vencimento);
    if($emb > time()){
      $msg = "A data de embarque não pode ser maior que a data atual.";
      return false;
    }
    if($emb > $venc){
      $msg = "A data de vencimento deve ser posterior a data de embarque";
      return false;
    }
    if($venc > $emb + (180 * 24 * 3600)){
      $msg = "A data de vencimento deve ser, no máximo, 180 dias após o embarque";
      return false;
    }

    $r = odbc_exec($db, "select inicio, periodo from DVE where id=$idDVE");
    if(! odbc_fetch_row($r)){
      $msg = "DVE inexistente: $idDVE";
      return false;
    }
    $inicio = odbc_result($r, 1);
    $periodo = odbc_result($r, 2);
    $time_inicio = getTimeStamp(ymd2dmy($inicio));
    $time_fim = getTimeStamp(getEndDate(ymd2dmy($inicio), $periodo));

    if(!($time_inicio <= $emb && $emb <= $time_fim)){
      $msg = "A data de embarque deve estar dentro do período de referência da DVE";
      return false;
    }

    return true;
  }
}

if(! function_exists('correct')){
  function correct($d){
    // corrige o formato da data
    if(preg_match("/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2})$/", $d, $v)){
      if($v[3] >= 70){
	$ano = 1900 + $v[3];
      }else{
	$ano = 2000 + $v[3];
      }
      return "$v[1]/$v[2]/$ano";
    }
    return $d;
  }
}

if(! function_exists('getEndDate')){
  function getEndDate($d, $n, $c = 0){
    global $idDVE, $db, $idInform;
    $num = odbc_result(odbc_exec($db, "select num from DVE where id=$idDVE"), 1);
    $start = ymd2dmy(odbc_result(odbc_exec($db, "select startValidity from Inform where id=$idInform"), 1));
    $num_dves = (odbc_result(odbc_exec($db, "select max(num) from DVE where idInform = $idInform"), 1));
    if(preg_match("/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/", $start, $v)){
      $dia_inicial = $v[1];
    }
    /*if($dia_inicial == 1){
      $num_dves = 12;
    }else{
      $num_dves = 13;
    }  */

    if($num != $num_dves){
      if(preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})/", $d, $v)){
	//return date("d/m/Y", mktime(0, 0, 0, $v[2] + $n, $v[1] - 1 + $c, $v[3]));
	return date("d/m/Y", mktime(0, 0, 0, $v[2] + $n, $c, $v[3]));
      }else if(preg_match("/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2})/", $d, $v)){
	//return date("d/m/Y", mktime(0, 0, 0, $v[2] + $n, $v[1] - 1 + $c, $v[3] + 2000));
	return date("d/m/Y", mktime(0, 0, 0, $v[2] + $n, $c, $v[3] + 2000));
      }
    }else{
      $end = odbc_result(odbc_exec($db, "select endValidity from Inform where id=$idInform"), 1);
      return ymd2dmy($end);
    }
  }
}

if(! function_exists('conserta')){
  function conserta($n){
    $s = ereg_replace(',00', '', $n);
    $s = ereg_replace('\.', '', $s);
    return $s;
  }
}

require_once("../rolePrefix.php");

if($comm == 'view' || $comm == 'open'){
  require_once("viewDve.php");
  if($flag == 1){
    require_once('includeImporter.php');
    $title = 'Incluir importador';
    $content = '../dve/interf/IncludeImporter.php';
  }else if($flag == 2){
    if($client){
      require_once("../client/query.php");
      require_once("../client/verifyAnt.php");
      $content = "../client/interf/ViewClient.php";
    }else{
      require_once("../notification/BoxInput.php");
      $title = "Notifica&ccedil;&otilde;es";
      $content = "../notification/interf/ViewBox.php";
    }
  }else{
    $title = "DVE - Declaração do Volume de Exportações<br>Riscos Comerciais - Exportações até 180 dias";
    $content = "../dve/interf/view.php";
  }

}else if($comm == 'done'){
  require_once('done.php');
  $title = "Notifica&ccedil;&otilde;es";
  $content = "../notification/interf/ViewBox.php";
  
//-------------------------------------------------------------------------------  

}
else if($comm == 'entregavenciada')
{
  //require('comite.php');
  $title = 'DVE com entrega vencida';
  $content = "../dve/interf/DveVencida.php";  

 
//-------------------------------------------------------------------------------  
}
else if($comm == 'entregavenciadaok')
{
  //require('comite.php');
  $hc_r = $notif->doneRole ($idNotification, $db);
  $title = "Notifica&ccedil;&otilde;es";
  $content = "../notification/interf/ViewBox.php";

 
//-------------------------------------------------------------------------------   
  
}else if($comm == 'comite'){
  require_once('comite.php');
  $title = 'Comitê de Cancelamento';
  $content = "../dve/interf/Comite.php";

}else if($comm == 'include'){
  require_once('includeImporter.php');
  require_once('input.php');
  $title = "DVE - Declaração do Volume de Exportações<br>Riscos Comerciais - Exportações até 180 dias";
  $content = '../dve/interf/Input.php';

}else if($comm == 'exclude'){
  require_once('excludeImporter.php');
  require_once('input.php');
  $title = "DVE - Declaração do Volume de Exportações<br>Riscos Comerciais - Exportações até 180 dias";
  $content = '../dve/interf/Input.php';

}else if($comm == 'modalidade'){
  $title = "DVE - Declaração do Volume de Exportações<br>Riscos Comerciais - Exportações até 180 dias";
  if($modalidade == 3){
    require_once("embarcado.php");
    $content = '../dve/interf/Embarcado.php';
  }else if($modalidade == 1 || $modalidade == 2){
    require_once('input.php');
    $content = '../dve/interf/Input.php';
  }

}else if($comm == 'salvatotal'){
  require_once('salvatotal.php');
  require_once('embarcado.php');
  $content = '../dve/interf/Embarcado.php';

}else if($comm == 'send'){
  require_once('sendDVE.php');
  $link = $root. "role/client/Client.php?comm=open&idInform=$idInform&msg=". urlencode($msg);
  echo "<script language=javascript> top.location = '$link'; </script>";

}else if($comm == 'editImporter'){
  require_once('editImporter.php');
  require_once('input.php');
  $title = "DVE - Declaração do Volume de Exportações<br>Riscos Comerciais - Exportações até 180 dias";
  $content = '../dve/interf/Input.php';
}else if($comm == 'consultadve'){
  //require('editImporter.php');
  //require('input.php');
  $title = "DVE - Consultas";
  $content = '../dve/interf/consultadve.php';


// Gustavo - 19/10/04
}else if($comm == 'consultaPa'){
  $title = "Parcela de Ajuste - Consulta";
  $content = '../dve/interf/consultaPa.php';

}else if($comm == 'calculaPa'){
  $title   = "Parcela de Ajuste - Cálculo";
  $content = '../dve/interf/calculaPa.php';

}else if($comm == 'calculaPaDet'){
  $title = "Parcela de Ajuste - Detalhe";
  $content = '../dve/interf/calculaPaDet.php';

}else if($comm == 'atualizaDetPa'){
  $title = "Parcela de Ajuste - Detalhe";
  require_once("../dve/atualizaDetPa.php");
  $content = '../dve/interf/calculaPaDet.php';

}else if($comm == 'atualizaDetPaOK'){
  $title = "Parcela de Ajuste - Cálculo";
  require_once("../dve/atualizaDetPa.php");
  $content = '../dve/interf/calculaPa.php';

}else if($comm == 'parcelaPA'){
  $title = "Parcela de Ajuste - Faturamento";
  $content = '../dve/interf/parcelaPa.php';
}else if($comm == 'exibeDve'){
  $title = "Declaração de Volume de Exportações";
  $content = '../dve/interf/exibeDve.php';
}else if($comm == 'exibeDveDet'){
  $title = "Declaração de Volume de Exportações - Detalhamento";
  $content = '../dve/interf/exibeDveDet.php';
}

// Gustavo - 19/10/04 FIM


if($client){
  require_once("../../../home.php");
}else{
  require_once("../../../home.php");
}

?>
