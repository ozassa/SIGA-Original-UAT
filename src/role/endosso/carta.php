<?php header("Content-Type: application/pdf");

function typeString($tipo){
  switch($tipo){
  case 1: return 'Dados Cadastrais';
  case 2: return 'Natureza da Operação';
  case 3: return 'Parcela de Ajuste';
  case 4: return 'Prêmio Mínimo';
  }
  return "Tipo não definido: $tipo";
}

require_once("../rolePrefix.php");

$c = odbc_exec($db, "select idInform, tipo from Endosso where id=$idEndosso");
if(odbc_fetch_row($c)){
  $idInform = odbc_result($c, 1);
  $tipo = odbc_result($c, 2);
}

$c = odbc_exec($db, "select name, contact, ocupationContact, city, uf, emailContact, i_Seg from Inform where id=$idInform");
if(odbc_fetch_row($c)){
  $empresa = odbc_result($c, 1);
  $contato = odbc_result($c, 2);
  $cargo = odbc_result($c, 3);
  $cidade = odbc_result($c, 4);
  $uf = odbc_result($c, 5);
  $email = odbc_result($c, 6);
  $iSeg = odbc_result($c, 7);
}

if($iSeg == 0){
  $cc = odbc_exec($db, "select i_Seg from Inform where id=(select idAnt from Inform where id=$idInform)");
  $iSeg = odbc_result($cc, 1);
}

$r = odbc_exec($dbSisSeg, "select max(n_Apolice) from Apolice where i_Seg=$iSeg");
if(odbc_fetch_row($r)){
  $nApolice = odbc_result($r, 1);
}


//$pdfDir = "e:\\projects\\sbce\\siex\\src\\download\\";
require_once("../../pdfConf.php");
$key = time(). session_id();

$h = new Java('java.util.HashMap');
$h->put('empresa', $empresa. "");
$h->put('contato', $contato. "");
$h->put('cargo', $cargo. '');
$h->put('cidade', $cidade. '');
$h->put('uf', $uf. '');
$h->put('apolice', $nApolice);
$h->put('tipo', typeString($tipo));
$h->put('key', $pdfDir. $key. "carta12.pdf");

$pdf = new Java('Carta12', $h);
$pdf->generate();

header("Location: $root". "download/". $key. "carta12.pdf");

?>
