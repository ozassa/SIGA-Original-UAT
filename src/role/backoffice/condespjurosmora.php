<?php
require_once("../rolePrefix.php");
require_once("../../pdfConf.php");

function ymd2dmy($d){
  if(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $d, $v)){
    return "$v[3]/$v[2]/$v[1]";
  }
  return $d;
}

function arruma_cnpj($c){
  if(strlen($c) == 14 && preg_match("/([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{4})([0-9]{2})/", $c, $v)){
    return "$v[1].$v[2].$v[3]/$v[4]-$v[5]";
  }
  return $c;
}

$key = session_id(). time();
$x = odbc_exec($db,
	       "select inf.name, inf.contact, inf.i_Seg, inf.ocupationContact, inf.city,
		r2.name, inf.prodUnit, inf.contrat, inf.address, inf.cnpj, inf.ie,
                inf.startValidity, inf.endValidity, inf.policyKey, inf.Ga
                from Inform inf JOIN Region r2 ON (r2.id = inf.idRegion)
                where inf.id=$idInform");
$name = odbc_result($x, 1);
$contact = odbc_result($x, 2);
$iSeg = odbc_result($x, 3);
$ocupation = odbc_result($x, 4);
$city = odbc_result($x, 5);
$uf = odbc_result($x, 6);
$prod = odbc_result($x, 7);
$contrat = odbc_result($x, 8);
$address = odbc_result($x, 9);
$cnpj = arruma_cnpj(odbc_result($x, 10));
$ie = odbc_result($x, 11);
$startValidity = ymd2dmy(odbc_result($x, 12));
$endValidity = ymd2dmy(odbc_result($x, 13));
$ga = odbc_result($x, "Ga");
if (($ga=="0") || ($ga=="")){
    $susep = "15.414005218/2005-89";
    $cp    = "CP/RC/06-01";
}else{
    $susep = "15.414004768/2004-08";
    $cp    = "CP/GA/07-01";
}

//Alterado por Tiago V N - Elumini - 27/12/2005
$key     = odbc_result($x, 14);

$y = odbc_exec($dbSisSeg, "select max(n_Apolice) from Apolice where i_Seg=$iSeg");
if(odbc_fetch_row($y)){
  $apolice = sprintf("062%06d", odbc_result($y, 1));
  if($prod != 62){
    $apolice .= "/$prod";
  }
}

$z = odbc_exec($dbSisSeg, "select max(n_Prop) from Proposta where i_Seg=$iSeg");
$prop =  odbc_result($z, 1);
$contrat .= "/$prop";


$h = new Java("java.util.HashMap");
//Alterado por Tiago V N - Elumini - 27/12/2005
//$h->put("key", $pdfDir. $key. "condEspJurosMora.pdf"); // arquivo a ser gerado
$h->put("key", $pdfDir. $key. "CondJuros.pdf"); // arquivo a ser gerado
$h->put("dir", $pdfDir);
$h->put("apolice", $apolice);
$h->put("segurado", $name);
$h->put("endereco", $address);
$h->put("cidade", $city);
$h->put("estado", $uf);
$h->put("cnpj", $cnpj);
$h->put("inscricao_estadual", $ie);
$h->put("startDate", $startValidity);
$h->put("finalDate", $endValidity);
$h->put("susep", $susep. '');
$h->put("cp", $cp. '');

$pdf = new Java("CondEspJurosMora", $h);
//$loc = '/siex/src/download/'.$key.'condEspJurosMora.pdf';
$loc = '/siex/src/download/'.$key.'CondJuros.pdf';
$pdf->generate();
echo "<HTML><HEAD> 
      <META HTTP-EQUIV=\"refresh\" content=\"0;URL=$loc\"> 
      <TITLE></TITLE> 
      </HEAD></html>";
?>
