<?php // Criado Hicom (Gustavo) 28/12/04 - Alteração do processo de solicitação de cobertura para juros de mora

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

//Alterado por Tiago V N - Elumini - 23/02/2006
$y = odbc_exec($db, "select * from Inform where id = '$idInform'");
$ga = odbc_result($y, "Ga");

if (($ga=="0") || ($ga=="")){
     $susep = "15.414005218/2005-89";
     $cp    = "CP/RC/06-01";
}else{
     $susep = "15.414004768/2004-08";
     $cp    = "CP/GA/07-01";
}

$key = session_id(). time();
$x = odbc_exec($db,
	       "select		inf.name, inf.address, inf.cep, inf.uf, inf.cnpj, inf.endValidity,
			 				inf.startValidity, inf.prMin, inf.txMin, inf.numParc, inf.tarifDate,
							inf.i_Seg, inf.prodUnit, inf.contrat, inf.city, inf.nProp, inf.policyKey, r.name
           from 		Inform inf JOIN Region r ON (r.id = inf.idRegion)
           where 		inf.id=$idInform");

$codigo = $n_Endosso;

$i_Seg = odbc_result($x, "i_Seg");
$n_Prop = odbc_result($x, "nProp");
$policyKey = odbc_result($x, "policyKey");

if($policyKey != ""){
	$pFile = $pdfDir. $policyKey. "EndJurMora.pdf";
   if(file_exists($pFile)){
		$loc = '/siex/src/download/'.$policyKey.'EndJurMora.pdf';
		echo "<HTML><HEAD>
      		<META HTTP-EQUIV=\"refresh\" content=\"0;URL=$loc\">
		      <TITLE></TITLE>
		      </HEAD></html>";
	}
	else {

	$y = odbc_exec($dbSisSeg,
	       "select		p.v_Parcela, p.d_Parcela, p.d_Venc
           from 		Parcela p
           where 		p.i_Seg = $i_Seg
							and p.n_Prop = $n_Prop
							and p.n_Endosso = $n_Endosso ");

	$dateEnv = odbc_result($y, "d_Parcela");
	$bornDate = ymd2dmy(odbc_result($y, "d_Parcela"));
	$valor = odbc_result($y, "v_Parcela");
	$vencimento = odbc_result($y, "d_Venc");

	list($ano, $mes, $dia) = split ('-', $dateEnv);
	$endosso = $codigo."/".$ano;

	$city = odbc_result($x, "city");
	$name = odbc_result($x, "name");
	$address = odbc_result($x, "address");
	$cep = odbc_result($x, "cep");
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
	$uf = odbc_result($x, "rname");
	$cnpj = arruma_cnpj(odbc_result($x, "cnpj"));
	$fim = ymd2dmy(odbc_result($x, "endValidity"));
	$inicio = ymd2dmy(odbc_result($x, "startValidity"));
	$taxa = odbc_result($x, "txMin");
	$numParcelas = 1;
	$prod = odbc_result($x, "prodUnit");
	$contrat = odbc_result($x, "contrat");

	$valorFormatado = number_format($valor, 2, '.', '');
	$valorExtenso = $numberExtensive->extensive($valorFormatado, 2);
	$valor = "US$ ".number_format($valor, 2, ',', '.')." ($valorExtenso)";

	$y = odbc_exec($dbSisSeg, "select max(n_Apolice) from Apolice where i_Seg=$i_Seg");
	if(odbc_fetch_row($y)){
	  $apolice = sprintf("062%06d", odbc_result($y, 1));
	  if($prod != 62){
	    $apolice .= "/$prod";
	  }
	}
	$z = odbc_exec($dbSisSeg, "select max(n_Prop) from Proposta where i_Seg=$i_Seg");
	$prop =  odbc_result($z, 1);
	$contrat .= "/$prop";

	$h = new Java("java.util.HashMap");
	$h->put("tit", "JUROS DE MORA"); // Cabeçalho do endosso
	$h->put("key", $pdfDir. $policyKey. "EndJurMora.pdf"); // arquivo a ser gerado
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
	$h->put("valor", $valor. '');
	$h->put("taxa", number_format($taxa, 3, ',', '.'). '');
	$h->put("numParcelas", $numParcelas. '');
	$h->put("vencimento", ymd2dmy($vencimento). '');
	$h->put("data", $bornDate. '');
    $h->put("susep", $susep. '');
    $h->put("cp", $cp. '');

	$pdf = new Java("EndPremMin", $h);
	$loc = '/siex/src/download/'.$policyKey.'EndJurMora.pdf';
	$pdf->generate();
	echo "<HTML><HEAD>
	      <META HTTP-EQUIV=\"refresh\" content=\"0;URL=$loc\">
	      <TITLE></TITLE>
	      </HEAD></html>";
	}
}

?>
