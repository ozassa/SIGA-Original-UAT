<?php function ymd2dmy($d){
  if(preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $d, $v)){
    return "$v[3]/$v[2]/$v[1]";
  }
  return $d;
}

require_once("../rolePrefix.php");
require_once("../../pdfConf.php");

$ano = date("y");
$key = time(). session_id();
$prefix = $pdfDir. $key;

$x = odbc_exec($db,
	       "select startValidity, endValidity, warantyInterest, txRise,
                cnpj, address, cep, name, contrat, currency, nProp, i_Seg, prodUnit, prMin, Ga
                from Inform where id=$idInform");
$start = ymd2dmy(odbc_result($x, 1));
$finish = ymd2dmy(odbc_result($x, 2));
$interest = odbc_result($x, 3);
$txRise = odbc_result($x, 4);
$iSeg = odbc_result($x, 12);
$cnpj = odbc_result($x, 5);
$end = odbc_result($x, 6);
$cep = odbc_result($x, 7);
$name = odbc_result($x, 8);
$prop = odbc_result($x, 11);
$contrat = odbc_result($x, 9);
$currency = odbc_result($x, 10);

$prod = odbc_result($x, 13);
$prMin = odbc_result($x, 14);

$ga = odbc_result($x, "Ga");
if (($ga=="0") || ($ga=="")){
    $susep = "15.414005212/2005-89";
    $cp    = "CP/RC/05-01";
}else{
    $susep = "15.414004768/2004-08";
    $cp    = "CP/GA/05-01";
}

$cnpj = substr($cnpj, 0, 2). ".".
        substr($cnpj, 2, 3). ".".
        substr($cnpj, 5, 3). "/".
        substr($cnpj, 8, 4). "-".
        substr($cnpj, 12);
$pr = $prMin * ($interest == 1 ? 1.04 : 1) * (1 + $txRise);

$y = odbc_exec($dbSisSeg, "select max(n_Apolice) from Apolice where i_Seg=$iSeg");
if(odbc_fetch_row($y)){
  $apolice = sprintf("062%06d", odbc_result($y, 1));
  if($prod != 62){
    $apolice .= "/$prod";
  }
}

if(! $prop){
  $z = odbc_exec($dbSisSeg, "select max(n_Prop) from Proposta where i_Seg=$iSeg");
  $prop =  odbc_result($z, 1);
}
$contrat .= "/$prop";

$h = new Java('java.util.HashMap');

// arruma o cep pro formato maluco q só a sbce é capaz de inventar: (00.000-000) -> na boa, alguem ja viu cep com '.' (ponto)??????
$sub = substr($cep, 0, 5);
if(! preg_match("\.", $sub)){
  $sub = substr($sub, 0, 2). '.'. substr($sub, 2, 3);
}else{
  $inc = 1;
  $sub = substr($cep, 0, 6);
}
if(! preg_match("-", $cep)){
  $cep = "$sub-". substr($cep, 5);
}else{
  $cep = "$sub-". substr($cep, 6 + $inc);
}


$h->put('key', $prefix. "ParcelaAjuste$parcela.pdf");
$h->put('dir', $pdfDir. "");
$h->put('numPar', $parcela. "");
$h->put('fatNum', "$ano/$idParcela");
$h->put('apoNum', $apolice. "");
$h->put('endNum', $endosso);
$h->put('proNum', $contrat. "");
$h->put('lugSeg', "$end");
$h->put('cepSeg', "$cep");
$h->put('nomSeg', $name. "");
$h->put('cnpjSeg', $cnpj. "");
$h->put('valPar', ($currency == 1 ? 'R$' : 'US$'). number_format($parc, 2, ',', '.'). "");
$h->put('valParExt', $numberExtensive->extensive(number_format($parc, 2, '.', ''), $currency). "");
$h->put('valPre', ($currency == 1 ? 'R$' : 'US$'). number_format($pr, 2, ',', '.'). "");
$h->put('valPreExt', $numberExtensive->extensive(number_format($pr, 2, '.', ''), $currency). "");
$h->put('partPre', "");
$h->put('dataVenc', ymd2dmy($dataVenc));
$h->put('partPreExt', "");
$h->put('segundavia', "0");
$h->put('vigApo', "$start à $finish");
$h->put('numPar', $parcela. "");
$h->put('numPre', $num_parcelas. "");
$h->put('susep', $susep. "");
$h->put('cp', $cp. "");

$prop = new Java('JavaParc', $h);

$loc = '/siex/src/download/'.$key."ParcelaAjuste$parcela.pdf";
$prop->generate();
echo "<HTML><HEAD> 
      <META HTTP-EQUIV=\"refresh\" content=\"0;URL=$loc\"> 
      <TITLE></TITLE> 
      </HEAD></html>";
?>
