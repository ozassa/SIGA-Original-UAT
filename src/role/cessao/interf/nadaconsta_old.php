<?php 

require_once("../rolePrefix.php");

require_once("../../pdfConf.php");

if(! function_exists('getStrDate')){
  function getStrDate($str){
    $row = explode('-', $str);
    $ret = $row[2]. "/". $row[1] ."/". $row[0];
    if ($ret == '//')
      return '';
    return $ret;
  }
}

if($role["bancoBB"]){
  $query = "
       SELECT DISTINCT imp.name, c.name as countryName, imp.limCredit, imp.id
	FROM Inform inf
          JOIN UsersNurim us ON (us.idUser = $userID)
          JOIN Nurim nu ON (nu.id = us.idNurim)
          JOIN Agencia ag ON (ag.idNurim = nu.id)
          JOIN CDBB cd ON (cd.idAgencia = ag.id)
          JOIN Importer imp ON (imp.idInform = inf.id)
          JOIN Country c ON (c.id = imp.idCountry)
          JOIN CDBBDetails cdd ON (cdd.idCDBB = cd.id and cdd.idImporter = imp.id)
	WHERE inf.id = $idInform AND cd.status = (2) AND imp.state <> 7 
	AND imp.state <> 8 AND imp.state <> 9
        ORDER BY imp.name";
}else if($role["bancoParc"]){
  $query = "
       SELECT DISTINCT imp.name, c.name as countryName, imp.limCredit, imp.id
	FROM Inform inf
          JOIN Banco bc ON (bc.idUser = $userID)
          JOIN Agencia ag ON (ag.idBanco = bc.id)
          JOIN CDParc cd ON (cd.idAgencia = ag.id)
          JOIN Importer imp ON (imp.idInform = inf.id)
          JOIN Country c ON (c.id = imp.idCountry)
          JOIN CDParcDetails cdd ON (cdd.idCDParc = cd.id and cdd.idImporter = imp.id)
	WHERE inf.id = $idInform AND cd.status = (2) AND imp.state <> 7 
	AND imp.state <> 8 AND imp.state <> 9
        ORDER BY imp.name";
} else {
  $query = "
       SELECT DISTINCT imp.name, c.name as countryName, imp.limCredit, imp.id
	FROM Inform inf
          JOIN Banco bc ON (bc.idUser = $userID)
          JOIN CDOB cd ON (cd.idBanco = bc.id)
          JOIN Importer imp ON (imp.idInform = inf.id)
          JOIN Country c ON (c.id = imp.idCountry)
          JOIN CDOBDetails cdd ON (cdd.idCDOB = cd.id and cdd.idImporter = imp.id)
	WHERE inf.id = $idInform AND cd.status = (2) AND imp.state <> 7 
	AND imp.state <> 8 AND imp.state <> 9	
        ORDER BY imp.name";
}
//$query = "SELECT imp.name, c.name as countryName, limCredit, imp.id
//          FROM Importer imp
//            JOIN Country c ON (c.id = imp.idCountry)
//            JOIN CDBBDetails cdd ON (cdd.idImporter = imp.id)
//            JOIN CDBB cdbb ON (cdbb.id = cdd.idCDBB)
//          WHERE cdbb.idInform = $idInform AND cdbb.status = 2
//          ORDER BY imp.name";


$cur = odbc_exec($db, $query);

function ymd2dmy($d){
  if(preg_match("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $d, $v)){
    return "$v[3]/$v[2]/$v[1]";
  }
  return $d;
}

function arruma_cnpj($c){
  if(strlen($c) == 14 && preg_match("([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{4})([0-9]{2})", $c, $v)){
    return "$v[1].$v[2].$v[3]/$v[4]-$v[5]";
  }
  return $c;
}

function getEndDate($d, $n, $c = 0){
  global $idDVE, $db, $idInform;
  $num = odbc_result(odbc_exec($db, "select num from DVE where id=$idDVE"), 1);
  if($num != 12){
    if(preg_match("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $d, $v)){
      return date("d/m/Y", mktime(0, 0, 0, $v[2] + $n, $c, $v[3]));
    }else if(preg_match("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2})", $d, $v)){
      return date("d/m/Y", mktime(0, 0, 0, $v[2] + $n, $c, $v[3] + 2000));
    }
  }else{
    $end = odbc_result(odbc_exec($db, "select endValidity from Inform where id=$idInform"), 1);
    return ymd2dmy($end);
  }
}

if(! function_exists('getTimeStamp')){
  function getTimeStamp($date){
    if(preg_match('^([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})', $date, $res)){
      return mktime(0, 0, 0, $res[2], $res[1], $res[3]);
    }else if(preg_match('^([0-9]{1,2})/([0-9]{1,2})/([0-9]{2})', $date, $res)){
      return mktime(0, 0, 0, $res[2], $res[1], $res[3] + 2000);
    }
  }
}







//$list = new Java("java.util.ArrayList");


while (odbc_fetch_row($cur)) {
  $idImporter = odbc_result($cur,4);
  //$list_aux = new Java('java.util.ArrayList');
  //$list_aux->add(trim(odbc_result($cur, 1)));
  //$list_aux->add(trim(strtoupper(odbc_result($cur, 2))));
  $x = odbc_exec($db, "select credit, creditTemp, limTemp from ChangeCredit where idImporter=$idImporter order by id desc");
  //$list_aux->add(trim(odbc_result($x, 1) / 1000));
  
  
	  
	  $hc_creditTemp_imp = odbc_result($x, 2);
	  $hc_limTemp_imp = odbc_result($x, 3);
	  $hc_credit_imp = odbc_result($x, 1);
	  
	  
	  //$hc_creditTemp_imp = odbc_result($y, 1);
	  //$hc_limTemp_imp = odbc_result($y, 2);
	  //$hc_credit_imp = odbc_result($y, 3);
	  
	  if ($hc_limTemp_imp) 
	  {
	     
		 $hc_creditTemp_imp = number_format($hc_creditTemp_imp/1000, 0, ",", ".") . " até: " . getStrDate(substr($hc_limTemp_imp, 0, 10));
		 
		 //if(getTimeStamp(getStrDate(substr($hc_limTemp_imp, 0, 10))) >= time())
		 //{
		 
		 //}
		 //else
		 //{
		 
		 //}
	  }
	  else
	  {
	     $hc_creditTemp_imp = number_format(0, 0, ",", ".");
	  }
	  

  //$list_aux->add($hc_creditTemp_imp);
	  
	  
      // fim busca dados de crédito	  
  
  
  
  
  
  
  
  
  //$list->add($list_aux);
}

$x = odbc_exec($db,
	       "select inf.name, inf.address, inf.city, inf.uf, inf.cnpj, inf.ie,
                       inf.dateEmission, inf.i_Seg, inf.endValidity, inf.percCoverage,
                       inf.startValidity, inf.contrat, inf.prodUnit, inf.cep, inf.nProp,
		       r.name, inf.policyKey
                from Inform inf
		      JOIN Region r ON (r.id = inf.idRegion)
                where inf.id=$idInform");
$segurado = odbc_result($x, 1);
$address = odbc_result($x, 2);
$city = odbc_result($x, 3);
$estado = odbc_result($x, 16);
$cnpj = arruma_cnpj(odbc_result($x, 5));

$key = odbc_result($x, 17);
if(! $key){
  $key = session_id(). time();
  odbc_exec($db, "update Inform set policyKey='$key' where id=$idInform");
}

$ie = odbc_result($x, 6);
$ie = preg_match("^[0-9]+$", $ie) ? number_format($ie, 0, '', '.') : $ie;

$emissao = ymd2dmy(odbc_result($x, 7));
$iSeg = odbc_result($x, 8);
$final = ymd2dmy(odbc_result($x, 9));
$cobertura = (int) odbc_result($x, 10);
$cobertura .= "% (". $numberExtensive->extensive($cobertura, 3). " por cento)";
$inicio_vig = ymd2dmy(odbc_result($x, 11));
$contrat = odbc_result($x, 12);
$prod = odbc_result($x, 13);
$cep = odbc_result($x, 14);
$nProp = odbc_result($x, 15);

$y = odbc_exec($dbSisSeg, "select max(n_Apolice) from Apolice where i_Seg=$iSeg");
if(odbc_fetch_row($y)){
  $apolice = sprintf("062%06d", odbc_result($y, 1));
  if($prod != 62){
    $apolice .= "/$prod";
  }
  if(! $nProp) {
    $x = odbc_exec($dbSisSeg,
		   "select n_Prop from Proposta where i_Seg=$iSeg order by n_Prop desc");
    if(odbc_fetch_row($x)){
      $nProp = odbc_result($x, 1);
    }
  }
}
$proposta = "$contrat/$nProp";

// verifica se tem algum deficit financeiro
$x = odbc_exec($dbSisSeg,
	       "select t_Doc, v_Documento from PagRec
                where i_Seg=$iSeg and n_Prop=$nProp and t_Doc in (2, 3)
                and s_Pagamento=1 and d_Vencimento < getdate()");
if(odbc_fetch_row($x)){
  $financeiro = 'Pendente';
  $financeiro_texto = '';
  do{
    $tdoc = odbc_result($x, 1);
    $valor = ($tdoc == 2 ? 'US$ ' : 'R$ ').number_format(odbc_result($x, 2), 2, ',', '.');
    if($tdoc == 2){
      if($financeiro_texto){
	$financeiro_texto .= "\nParcela de Prêmio no valor de $valor";
      }else{
	$financeiro_texto .= "Parcela de Prêmio no valor de $valor";
      }
    }else if($tdoc == 3){
      if($financeiro_texto){
	$financeiro_texto .= "\nParcela de Análise e Monitoramento no valor de $valor";
      }else{
	$financeiro_texto .= "Parcela de Análise e Monitoramento no valor de $valor";
      }
    }
  }while(odbc_fetch_row($x));
}else{
  $financeiro = 'OK';
  $financeiro_texto = 'Não há pendências até o presente momento';
}

// verifica as DVE's
$dia = date('d');
$ano = date('Y');
if($dia <= 15){
  $mes = date('m') - 2;
}else{
  $mes = date('m');
}
$x = odbc_exec($db, // verifica se tem alguma que ainda nao foi enviada e esta vencida
	       "select inicio, periodo, state, id from DVE where idInform=$idInform and (state=1 or state=3)");
if(odbc_fetch_row($x)){
  $dve_texto = '';
  $dve = 'Pendente';
  do{
    $inicio = ymd2dmy(odbc_result($x, 1));
    $state = odbc_result($x, 3);
    $idDVE = odbc_result($x, 4);
    $fim = getEndDate($inicio, odbc_result($x, 2));
    $fim_stamp = getTimeStamp($fim) + (15 * 24 * 3600); // fim do periodo + 15 dias
    if(time() > $fim_stamp){
      $dve_pendente = 1;
      if($dve_texto){
	$dve_texto .= " e ($inicio à $fim)";
      }else{
	$dve_texto = "($inicio à $fim)";
      }
    }
  }while(odbc_fetch_row($x));
  $dve_texto = "Declaração período(s) $dve_texto";
}

if(! $dve_pendente){
  $dve = 'OK';
  $dve_texto = 'Não há pendências até o presente momento';
}

/*

$h = new Java("java.util.HashMap");
$h->put("key", $pdfDir. $key. "DecRegBB.pdf"); // arquivo a ser gerado
$h->put("dir", $pdfDir);
$h->put("apolice", $apolice. '');
$h->put("proposta", $proposta. '');
$h->put("vigencia", "de $inicio_vig até $final");
$h->put("segurado", trim($segurado). '');
$h->put("endereco", $address. '');
$h->put("cidade", $city. '');
$h->put("estado", substr($estado, 0, 2). '');
$h->put("cep", $cep. '');
$h->put("cnpj", $cnpj. '');
$h->put("inscricao_estadual", $ie. '');
$h->put("list", $list);
$h->put("cadastro", "OK"); // ARRUMAR
$h->put("cadastro_texto", "Não há pendências até o presente momento"); // ARRUMAR
$h->put("dve", $dve);
$h->put("dve_texto", $dve_texto);
$h->put("financeiro", $financeiro);
$h->put("financeiro_texto", $financeiro_texto);
$h->put("emissao", date('H:i \h\s \d\o \d\i\a d/m/Y'));

$pdf = new Java("DecReg", $h);
$loc = '/siex/src/download/'.$key.'DecRegBB.pdf';
$pdf->generate();

*/


?>
