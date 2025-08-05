<?php  require_once("../rolePrefix.php");
require_once("../../pdfConf.php");

$key = time(). session_id();
$prefix = $pdfDir. $key;

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

$x = odbc_exec($db,
	       "select inf.name, inf.address, inf.cep, r.name, inf.cnpj, inf.endValidity,
		          inf.startValidity, inf.i_Seg, inf.prodUnit, inf.Ga
           from Inform inf JOIN Region r on (r.id = inf.idRegion)
           where inf.id=$idInform");
$name = odbc_result($x, 1);
$address = odbc_result($x, 2);
$cep = odbc_result($x, 3);
$uf = odbc_result($x, 4);
$cnpj = arruma_cnpj(odbc_result($x, 5));
$endValidity = ymd2dmy(odbc_result($x, 6));
$startValidity = ymd2dmy(odbc_result($x, 7));
$iSeg = odbc_result($x, 8);
$prod = odbc_result($x, 9);
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


if(! file_exists($prefix. "PropJuros.pdf")){
  $h = new Java ('java.util.HashMap');

  $h->put('key', $prefix. "PropJuros.pdf");
  $h->put('dir', $pdfDir. "");
  $h->put('apolice', $apolice. "");
  $h->put('segurado', trim($name). "");
  $h->put('cnpj', $cnpj. "");
  $h->put('vigencia', "$startValidity e $endValidity");
  $h->put('susep', $susep);
  $h->put('cp', $cp);
  
  $prop = new Java ('PropJuros', $h);
  $loc = '/siex/src/download/'.$key.'PropJuros.pdf';

  if($prop == null){
    die("<h1>condjuros null</h1>");
  }else{
    $prop->generate();
    echo "<HTML><HEAD>
         <META HTTP-EQUIV=\"refresh\" content=\"0;URL=$loc\">
         <TITLE></TITLE>
         </HEAD></html>";
  }
}

?>
