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

//Alterado por Tiago V N - Elumini - 23/02/2006
$y = odbc_exec($db, "select * from Inform where id = '$idInform'");
$ga = odbc_result($y, "Ga");

if (($ga=="0") || ($ga=="")){
     $susep = "15.414005212/2005-89";
     $cp    = "CP/RC/06-01";
}else{
     $susep = "15.414004768/2004-08";
     $cp    = "CP/GA/07-01";
}

$key = session_id(). time();
$x = odbc_exec($db,
	       "select inf.name, inf.address, inf.cep, inf.uf, inf.cnpj, inf.endValidity, inf.startValidity,
		endos.bornDate, endosN.natureza, s.description, inf.i_Seg, inf.prodUnit, inf.contrat,
		endos.codigo, endos.bornDate, inf.city, r.name
                from Inform inf 
                      JOIN Endosso endos ON (endos.id = $idEndosso)
                      JOIN EndossoNatureza endosN ON (endosN.idEndosso = endos.id)
                      JOIN Sector s ON (s.id = endosN.idSector)
		      JOIN Region r ON (r.id = inf.idRegion)
                where inf.id=$idInform");

$codigo = odbc_result($x,14);
$dateEnv = odbc_result($x, 15);
list($ano, $mes, $dia) = split ('-', $dateEnv);
$endosso = $codigo."/".$ano;

$city = odbc_result($x, 16);
$name = odbc_result($x, 1);
$address = odbc_result($x, 2);
$cep = odbc_result($x, 3);
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
$uf = odbc_result($x, 17);
$cnpj = arruma_cnpj(odbc_result($x, 5));
$fim = ymd2dmy(odbc_result($x, 6));
$inicio = ymd2dmy(odbc_result($x, 7));
$bornDate = ymd2dmy(odbc_result($x, 8));
$new_natureza = odbc_result($x, 9);
$new_sector = odbc_result($x, 10);

$iSeg = odbc_result($x, 11);
$prod = odbc_result($x, 12);
$contrat = odbc_result($x, 13);
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
$h->put("tit", "NATUREZA DA OPERAÇÃO"); // Cabeçalho do endosso
$h->put("key", $pdfDir. $key. "endosso_operacao.pdf"); // arquivo a ser gerado
$h->put("dir", $pdfDir);
$h->put("apolice", $apolice. '');
$h->put("proposta", $contrat. '');
$h->put("endosso", $endosso. '');
$h->put("vigencia", "Das 0h de $inicio até às 24h de $fim");
$h->put("nomeCliente", $name. '');
$h->put("endCliente", $address. '');
$h->put("cidCliente", $city. '');
$h->put("ufCliente", substr($uf, 0, 2). '');
$h->put("cepCliente", $cep. '');
$h->put("cnpjCliente", $cnpj. '');
$h->put("novoSetor", $new_sector. '');
$h->put("novoProduto", $new_natureza. '');
$h->put("data", $bornDate. '');
$h->put("susep", $susep. '');
$h->put("cp", $cp. '');


$pdf = new Java("EndNatOper", $h);
$loc = '/siex/src/download/'.$key.'endosso_operacao.pdf';
$pdf->generate();
echo "<HTML><HEAD> 
      <META HTTP-EQUIV=\"refresh\" content=\"0;URL=$loc\"> 
      <TITLE></TITLE> 
      </HEAD></html>";

?>
