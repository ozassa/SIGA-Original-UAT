<?php  require_once ("../rolePrefix.php");

// alterado Hicom (Gustavo) - 06/01/05 - pequena correção em uma atribuição (estava: $fim = odbc_result($fim, 2) - passei para: $fim = odbc_result($x, 2)
// alterado Hicom (Gustavo) - 10/01/05 - inclusão do usuário q enviou a proposta no informe
// alterado Hicom (Gustavo) - 10/01/05 - se o usuário que envia não é o executivo responsável, vai cópia p/ o usuário e p/ o exec. resp.
// alterado Hicom (Gustavo) - 18/01/05 - oferta tb vai para o email do contato
// Alterado Hicom (Gustavo) - 19/01/05 - login não é mais um e-mail obrigatoriamente, sendo assim
// o destinatário do e-mail foi alterado
// Alterado Hicom (Gustavo) - 24/01/05 - adicionei uma proteção para não enviar e-mails caso não exista uma proposta no Sisseg

$dEmi = date ("d/m/Y", mktime (0, 0, 0, date("m"), date("d"), date("Y")));

$dateEmission = dmy2ymd($dEmi);

$idInform = 5998;

// converte uma data de '31/12/2002' para '2002-12-31 00:00:00.000'
function dmy2ymd($d){
  if(preg_match("/([0-9]{2})/([0-9]{2})/([0-9]{4})/", $d, $v)){
    return "$v[3]-$v[2]-$v[1] 00:00:00.000";
  }
  return "";
}

$r = odbc_exec($db, "select state, idAnt, emailContact from Inform where id=$idInform");
if(odbc_fetch_row($r)){
  $status = odbc_result($r, 1);
  $idAnt = odbc_result($r, 1);
  $emailContact = odbc_result($r, 3);
}

// testa se existe proposta no Sisseg
$propostaOk = false;
$cur_hc = odbc_exec($db, "select i_Seg, nProp from Inform where id = $idInform");
if(odbc_fetch_row($cur_hc)){
	$hc_i_Seg = odbc_result($cur_hc, "i_Seg");
	$hc_nProp = odbc_result($cur_hc, "nProp");
    if ($hc_i_Seg && $hc_nProp) {
		$cur_hc = odbc_exec($dbSisSeg, "select * from Proposta where n_Prop = $hc_nProp and i_Seg = $hc_i_Seg");
		if ($cur_hc)
			$propostaOk = true;
	}
}


		$idParcela = "15071";
		$q = ereg_replace("idParcelaSubs", "$idParcela", $q);

	    // gera o pdf da parcela, dessa vez com o numero correto
	    $key = odbc_result(odbc_exec($db, "select segundaVia from Inform where id=$idInform"), 1);
		echo $key."select segundaVia from Inform where id=$idInform";
	    require_once("../../pdfConf.php");
	    $prefix = $pdfDir. "$key";
	    $_SESSION[keyParc] = $prefix. "Parc.pdf";
	    $_SESSION[fatNum] = ereg_replace('/0', "/$idParcela", $_SESSION[fatNum]);
	    $segundavia = 0;
	    require_once("parcPdf.php");
	    $_SESSION[keyParc] = $prefix. "ParcSegVia.pdf";
	    $_SESSION[fatNum] = ereg_replace('/0', "/$idParcela", $_SESSION[fatNum]);
	    $segundavia = 1;
	    require_once("parcPdf.php");

break;
?>
