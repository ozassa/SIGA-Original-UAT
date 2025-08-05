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
                       endos.bornDate, endosD.name, endosD.address, endosD.city, endosD.cep, r1.description,
                       r2.name, inf.i_Seg, inf.prodUnit, inf.contrat, endosD.cnpj, r3.name, endosD.nameOld,
		       endosD.addressOld, endosD.cityOld, endosD.cepOld, endosD.cnpjOld, endosD.idRegionOld,
		       endos.codigo, endos.bornDate, inf.city, endosD.number, endosD.addresscomp, endosD.numberOld,
		       endosD.addresscompOld
                from Inform inf 
                      JOIN Endosso endos ON (endos.id = $idEndosso)
                      JOIN EndossoDados endosD ON (endosD.idEndosso = endos.id)
                      JOIN Region r1 ON (r1.id = endosD.idRegion)
                      JOIN Region r2 ON (r2.id = inf.idRegion)
                      JOIN Region r3 ON (r3.id = endosD.idRegionOld)
                where inf.id=$idInform");

$ufOld = odbc_result($x, 19);
$nameOld = odbc_result($x, 20);
$addressOld = odbc_result($x, 21);
$numberOldOld = odbc_result($x, 31);
$addresscompOld = odbc_result($x, 32);
$cityOld = odbc_result($x, 22);
$cepOld = odbc_result($x, 23);
$sub = substr($cepOld, 0, 5);
if(! preg_match("\.", $sub)){
  $sub = substr($sub, 0, 2). '.'. substr($sub, 2, 3);
}else{
  $inc = 1;
  $sub = substr($cepOld, 0, 6);
}
if(! preg_match("-", $cepOld)){
  $cepOld = "$sub-". substr($cepOld, 5);
}else{
  $cepOld = "$sub-". substr($cepOld, 6 + $inc);
}
$cnpjOld = arruma_cnpj(odbc_result($x, 24));

$codigo = odbc_result($x,26);
$dateEnv = odbc_result($x, 27);
list($ano, $mes, $dia) = split ('-', $dateEnv);
$endosso = $codigo."/".$ano;

$city = odbc_result($x, 28);
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
$uf = odbc_result($x, 14);
$cnpj = arruma_cnpj(odbc_result($x, 5));
$fim = ymd2dmy(odbc_result($x, 6));
$inicio = ymd2dmy(odbc_result($x, 7));
$bornDate = ymd2dmy(odbc_result($x, 8));
$newRazao = odbc_result($x, 9);
$newAddress = odbc_result($x, 10);
if (odbc_result($x, 29)!="") {
   $newNumber = ",". odbc_result($x, 29);
   $newNumber1 = odbc_result($x, 29);
}
$newAddresscomp = odbc_result($x, 30);
$newCity = odbc_result($x, 11);
$newCep = odbc_result($x, 12);
$sub = substr($newCep, 0, 5);
if(! preg_match("\.", $sub)){
  $sub = substr($sub, 0, 2). '.'. substr($sub, 2, 3);
}else{
  $inc = 1;
  $sub = substr($newCep, 0, 6);
}
if(! preg_match("-", $newCep)){
  $newCep = "$sub-". substr($newCep, 5);
}else{
  $newCep = "$sub-". substr($newCep, 6 + $inc);
}
$newRegion = odbc_result($x, 13);
$newCNPJ = arruma_cnpj(odbc_result($x, 18));
$iSeg = odbc_result($x, 15);
$prod = odbc_result($x, 16);
$contrat = odbc_result($x, 17);
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


   $cons = "Select a.idconsultor, a.razao, a.c_SUSEP
            from consultor a inner join Inform b on (b.idConsultor = a.idconsultor)
            where b.id = $idInform";
   $resultado = odbc_exec($db, $cons);

        if (odbc_fetch_row($resultado)){
            $corretora = odbc_result($resultado,'razao');
            $codSusepCorretora = odbc_result($resultado,'c_SUSEP');
        }else{
            $corretora = '';
            $codSusepCorretora = '';
        }




$h = new Java("java.util.HashMap");
$h->put("tit", "DADOS CADASTRAIS"); // Cabeçalho do endosso
$h->put("key", $pdfDir. $key. "endosso_dados.pdf"); // arquivo a ser gerado
$h->put("dir", $pdfDir);
$h->put("apolice", $apolice. '');
$h->put("proposta", $contrat. '');
$h->put("endosso", $endosso. '');
$h->put("vigencia", "$inicio a $fim");
$h->put("corretora", "".substr($corretora,0,68)."");
$h->put("codUsepCorretora", "$codSusepCorretora");

if ($newRazao == '') {
  $h->put("nomeCliente", $name. '');
}
else {
  $h->put("nomeCliente", $nameOld. '');
}
if ($newAddress == '') {
  $h->put("endCliente", $address. '');
  $h->put("cidCliente", $city. '');
  $h->put("ufCliente", substr($uf, 0, 2). '');
  $h->put("cepCliente", $cep. '');
}
else {
  $h->put("endCliente", $addressOld. '');
  $h->put("cidCliente", $cityOld. '');
  $h->put("ufCliente", substr($ufOld, 0, 2). '');
  $h->put("cepCliente", $cepOld. '');
}
if ($newCNPJ == '') {
  $h->put("cnpjCliente", $cnpj. '');
}
else {
  $h->put("cnpjCliente", $cnpjOld. '');
}
if($newRazao != ''){
  $h->put("novaRazaoSocial", $newRazao. '');
}
if($newAddress != ''){
  $h->put("novoEndereco", $newAddress. '');
  $h->put("novoNumber", $newNumber.'');
  $h->put("novoAddresscomp", $newAddresscomp. '');
  $h->put("novaCidade", $newCity. '');
  $h->put("novoCep", $newCep. '');
  $h->put("novaRegiao", $newRegion. '');
}
$h->put("data", $bornDate. '');
if($newCNPJ != ''){
  $h->put("novoCNPJ", $newCNPJ. '');
}
$h->put("susep", $susep. '');
$h->put("cp", $cp. '');

$pdf = new Java("EndDadosCadast", $h);
$loc = '/siex/src/download/'.$key.'endosso_dados.pdf';
$pdf->generate();
echo "<HTML><HEAD> 
      <META HTTP-EQUIV=\"refresh\" content=\"0;URL=$loc\"> 
      <TITLE></TITLE> 
      </HEAD></html>";
?>
