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
}

$key = session_id(). time();
$x = odbc_exec($db,
	       "select inf.name, inf.contact, inf.i_Seg, inf.ocupationContact, inf.city,
		r2.name, inf.prodUnit, inf.contrat
                from Inform inf 
                      JOIN Region r2 ON (r2.id = inf.idRegion)
                where inf.id=$idInform");
$name = odbc_result($x, 1);
$contact = odbc_result($x, 2);
$iSeg = odbc_result($x, 3);
$ocupation = odbc_result($x, 4);
$city = odbc_result($x, 5);
$uf = odbc_result($x, 6);
$prod = odbc_result($x, 7);
$contrat = odbc_result($x, 8);

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
$h->put("key", $pdfDir. $key. "cartaJurosMora.pdf"); // arquivo a ser gerado
$h->put("dir", $pdfDir);
$h->put("empresa", $name. '');
$h->put("contato", $contact. '');
$h->put("cargo", $ocupation. '');
$h->put("cidade", $city. '');
$h->put("uf", $uf. '');
$h->put("proposta", $contrat. '');

$pdf = new Java("carta4", $h);
$loc = '/siex/src/download/'.$key.'cartaJurosMora.pdf';
$pdf->generate();
echo "<HTML><HEAD> 
      <META HTTP-EQUIV=\"refresh\" content=\"0;URL=$loc\"> 
      <TITLE></TITLE> 
      </HEAD></html>";

?>
