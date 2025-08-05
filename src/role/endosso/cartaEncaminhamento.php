<?php require_once("../rolePrefix.php");
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
	       "select inf.name, inf.contact, inf.i_Seg, endos.tipo, inf.ocupationContact, inf.city,
		r2.name, inf.prodUnit, inf.Ga
                from Inform inf 
                      JOIN Endosso endos ON (endos.id = $idEndosso)
                      JOIN Region r2 ON (r2.id = inf.idRegion)
                where inf.id=$idInform");
setlocale (LC_TIME, "pt_BR");
$date = strftime ("%d de %B de %Y");
$name = odbc_result($x, 1);
$contact = odbc_result($x, 2);
$ocupation = odbc_result($x, 5);
$city = odbc_result($x, 6);
$uf = odbc_result($x, 7);
$type = odbc_result($x, 4);
$iSeg = odbc_result($x, 3);
$prod = odbc_result($x, 8);
$ga = odbc_result($x, "Ga");
if (($ga=="0") || ($ga=="")){
    $susep = "15.414005212/2005-89";
    $cp    = "CP/RC/06-01";
}else{
    $susep = "15.414004768/2004-08";
    $cp    = "CP/GA/07-01";
}

$y = odbc_exec($dbSisSeg, "select max(n_Apolice) from Apolice where i_Seg=$iSeg");
if(odbc_fetch_row($y)){
  $apolice = sprintf("062%06d", odbc_result($y, 1));
  if($prod != 62){
    $apolice .= "/$prod";
  }
}
// $ano = odbc_result($x, );
// $numero = odbc_result($x, );

switch($type){
  case 1: $type = "Dados Cadastrais"; break;
  case 2: $type = "Natureza da Operação"; break;
  case 3: $type = "Parcela de Ajuste"; break;
  case 4: $type = "Prêmio Mínimo"; break;
}

$h = new Java("java.util.HashMap");
$h->put("key", $pdfDir. $key. "cartaEncaminhamento.pdf"); // arquivo a ser gerado
$h->put("dir", $pdfDir);
$h->put("apolice", $apolice. '');
$h->put("ano", "AA");
$h->put("numero", "BB");
$h->put("empresa", $name. '');
$h->put("contato", $contact. '');
$h->put("cargo", $ocupation. '');
//$h->put("cidade", "Belo Horizonte");
$h->put("cidade", $city. '');
$h->put("uf", $uf. '');
$h->put("data", $date. '');
$h->put("tipo", $type. '');
$h->put("susep", $susep. '');
$h->put("cp", $cp. '');

$pdf = new Java("carta3", $h);
$loc = '/siex/src/download/'.$key.'cartaEncaminhamento.pdf';
$pdf->generate();
echo "<HTML><HEAD> 
      <META HTTP-EQUIV=\"refresh\" content=\"0;URL=$loc\"> 
      <TITLE></TITLE> 
      </HEAD></html>";

?>
