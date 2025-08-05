<?php

require_once("../rolePrefix.php");
require_once("../../pdfConf.php");

include_once("../../../gerar_pdf/MPDF45/mpdf.php");
require_once("../../dbOpen.php");


function ymd2dmy($d)
{
	if (preg_match("@([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})@", $d, $v)) {
		return "$v[3]/$v[2]/$v[1]";
	}
	return $d;
}

//Alterado por Tiago V N - Elumini - 01/03/2007
if (!function_exists('getEndDate')) {
	function getEndDate($d, $n, $c = 0)
	{
		global $idDVE, $db, $idInform;

		// Preparar e executar consultas seguras para evitar SQL Injection
		$query1 = "SELECT num FROM DVE WHERE id = ?";
		$stmt1 = odbc_prepare($db, $query1);
		odbc_execute($stmt1, [$idDVE]);
		$num = odbc_result($stmt1, 1);

		$query2 = "SELECT startValidity FROM Inform WHERE id = ?";
		$stmt2 = odbc_prepare($db, $query2);
		odbc_execute($stmt2, [$idInform]);
		$start = ymd2dmy(odbc_result($stmt2, 1));

		$query3 = "SELECT max(num) FROM DVE WHERE idInform = ?";
		$stmt3 = odbc_prepare($db, $query3);
		odbc_execute($stmt3, [$idInform]);
		$num_dves = odbc_result($stmt3, 1);

		if (preg_match("@([0-9]{2})/([0-9]{2})/([0-9]{4})@", $start, $v)) {
			$dia_inicial = $v[1];
		}

		if ($num != $num_dves) {
			if (preg_match("@([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})@", $d, $v)) {
				return date("d/m/Y", mktime(0, 0, 0, $v[2] + $n, $c, $v[3]));
			} else if (preg_match("@([0-9]{1,2})/([0-9]{1,2})/([0-9]{2})@", $d, $v)) {
				return date("d/m/Y", mktime(0, 0, 0, $v[2] + $n, $c, $v[3] + 2000));
			}
		} else {
			$query4 = "SELECT endValidity FROM Inform WHERE id = ?";
			$stmt4 = odbc_prepare($db, $query4);
			odbc_execute($stmt4, [$idInform]);
			$end = odbc_result($stmt4, 1);
			return ymd2dmy($end);
		}
	}
}


function dataconvert($dt)
{
	// leitura das datas
	$dia = date('d');
	$mes = date('m');
	$ano = date('Y');
	$semana = date('w');


	// configuração mes

	switch ($mes) {

		case 1:
			$mes = "Janeiro";
			break;
		case 2:
			$mes = "Fevereiro";
			break;
		case 3:
			$mes = "Março";
			break;
		case 4:
			$mes = "Abril";
			break;
		case 5:
			$mes = "Maio";
			break;
		case 6:
			$mes = "Junho";
			break;
		case 7:
			$mes = "Julho";
			break;
		case 8:
			$mes = "Agosto";
			break;
		case 9:
			$mes = "Setembro";
			break;
		case 10:
			$mes = "Outubro";
			break;
		case 11:
			$mes = "Novembro";
			break;
		case 12:
			$mes = "Dezembro";
			break;

	}


	// configuração semana

	switch ($semana) {

		case 0:
			$semana = "Domingo";
			break;
		case 1:
			$semana = "Segunda Feira";
			break;
		case 2:
			$semana = "Terça Feira";
			break;
		case 3:
			$semana = "Quarta Feira";
			break;
		case 4:
			$semana = "Quinta Feira";
			break;
		case 5:
			$semana = "Sexta Feira";
			break;
		case 6:
			$semana = "Sábado";
			break;

	}

	$data = $dia . ' de ' . $mes . ' de ' . $ano;
	return $data;
}

$idInform = $_REQUEST['idInform'];
$idDVE = $_REQUEST['idDVE'];
$noval = $_REQUEST['noval'];

$sqlEmp = "SELECT Nome, CNPJ,	Endereco,	Complemento, CEP, Cidade,	Estado,	Cod_Area,	Telefone,	Bairro, Fax, HomePage
							FROM Empresa 
							WHERE i_Empresa = 1";
$resEmp = odbc_exec($db, $sqlEmp);
$dadosEmp = odbc_fetch_array($resEmp);

$compEmp = $dadosEmp['Complemento'] ? ' - ' . $dadosEmp['Complemento'] : '';
$cepEmp = $dadosEmp['CEP'] ? ' - Cep: ' . $dadosEmp['CEP'] : '';
$cidEmp = $dadosEmp['Cidade'] ? ' - ' . $dadosEmp['Cidade'] : '';
$estEmp = $dadosEmp['Estado'] ? ', ' . $dadosEmp['Estado'] : '';
$telEmp = $dadosEmp['Telefone'] ? ' Tel.: ' . $dadosEmp['Telefone'] : '';
$faxEmp = $dadosEmp['Fax'] ? ' Fax: ' . $dadosEmp['Fax'] : '';

$enderecoEmp = $dadosEmp['Endereco'] . $compEmp . $cepEmp . $cidEmp . $estEmp . $telEmp . $faxEmp;
$siteEmp = $dadosEmp['HomePage'];
$nomeEmp = $dadosEmp['Nome'];

$sqlquery = "SELECT P.Nome AS Produto, SP.Descricao AS SubProduto, SP.c_SUSEP 
	FROM Inform Inf
	INNER JOIN Produto P ON
		P.i_Produto = Inf.i_Produto
	INNER JOIN Empresa_Produto EP ON
		EP.i_Produto = P.i_Produto
	INNER JOIN Empresa E ON
		E.i_Empresa = EP.i_Empresa
	INNER JOIN Sub_Produto SP ON
		SP.i_Produto = Inf.i_Produto
		AND SP.i_Sub_Produto = Inf.i_Sub_Produto
	WHERE Inf.id = ?";

$rsSqlquery = odbc_prepare($db, $sqlquery);
odbc_execute($rsSqlquery, [$idInform]);

$dadosQuery = odbc_fetch_array($rsSqlquery);

$c_SUSEP = $dadosQuery['c_SUSEP'] ?? '';

odbc_free_result($rsSqlquery);

$totalace1 = $totalace2 = $totalproex1 = $totalproex2 = $v_Vista_Prazo = $v_Coligadas = 0;

$qry1 = "SELECT i.c_Coface_Imp, i.name, c.name, dt.embDate, dt.fatura,
                 dt.vencDate, dt.totalEmbarcado, dt.proex, dt.ace, idImporter
          FROM DVE d
          JOIN DVEDetails dt ON d.id = dt.idDVE
          LEFT JOIN Importer i ON dt.idImporter = i.id
          LEFT JOIN Country c ON dt.idCountry = c.id
          WHERE d.id = ? AND dt.modalidade = 1 AND dt.state = 1
          ORDER BY dt.embDate";

$cur = odbc_prepare($db, $qry1);
odbc_execute($cur, [$idDVE]);


while (odbc_fetch_row($cur)) {
	$v_Vista_Prazo += odbc_result($cur, 7);
	$totalproex1 += odbc_result($cur, 8);
	$totalace1 += odbc_result($cur, 9);
}
odbc_free_result($cur);
$qry2 = "SELECT i.c_Coface_Imp, i.name, c.name, dt.embDate, dt.fatura,
                 dt.vencDate, dt.totalEmbarcado, dt.proex, dt.ace, idImporter
          FROM DVE d
          JOIN DVEDetails dt ON d.id = dt.idDVE
          LEFT JOIN Importer i ON dt.idImporter = i.id
          LEFT JOIN Country c ON dt.idCountry = c.id
          WHERE d.id = ? AND dt.modalidade = 2 AND dt.state = 1
          ORDER BY dt.embDate";

$cur = odbc_prepare($db, $qry2);
odbc_execute($cur, [$idDVE]);


while (odbc_fetch_row($cur)) {
	$v_Coligadas += odbc_result($cur, 7);
	$totalproex2 += odbc_result($cur, 8);
	$totalace2 += odbc_result($cur, 9);
}
$sql = "SELECT name, i_Seg, endValidity, startValidity, prodUnit, Ga, currency, n_Apolice, 
               IsNull(periodMaxCred, 180) AS PeriodCred
        FROM Inform
        WHERE id = ?";

$x = odbc_prepare($db, $sql);
odbc_execute($x, [$idInform]);


$Nome_Segurado = odbc_result($x, 1);
$d_Inicio_Vigencia = ymd2dmy(odbc_result($x, 4));
$d_Fim_Vigencia = ymd2dmy(odbc_result($x, 3));
$prod = odbc_result($x, 5);
$n_Apolice = sprintf("062%06d", odbc_result($x, 8));
$PeriodCred = odbc_result($x, 'PeriodCred');
$vigencia = $d_Inicio_Vigencia . " à " . $d_Fim_Vigencia;
$ga = odbc_result($x, "Ga");
$moeda = odbc_result($x, "currency");
$d_Envio = date('d/m/Y');

if ($moeda == "1") {
	$extMoeda = "Reais";
} else if ($moeda == "2") {
	$extMoeda = "Dólares Norte Americanos";
} else if ($moeda == "6") {
	$extMoeda = "Euros";
}

if (!$c_SUSEP) {
	if (($ga == "0") || ($ga == "")) {
		$c_SUSEP = "15.414005212/2005-89";
	} else {
		$c_SUSEP = "15.414004768/2004-08";
	}
}

if ($prod != 62) {
	$n_Apolice .= "/$prod";
}
odbc_free_result($x);
$query = "SELECT 
            D.inicio AS Inicio,
            D.periodo AS Periodo, 
            D.num AS Num, 
            D.total2 AS Total2, 
            D.sentDate AS SentDate,
            ISNULL(DateAdd(D, -1, D2.inicio), Inf.endValidity) AS Fim
        FROM 
            DVE D
        INNER JOIN Inform Inf ON
            Inf.id = D.idInform
        LEFT JOIN DVE D2 ON
            D2.idInform = D.idInform
            AND D2.num = D.num + 1
        WHERE 
            D.id = ?";

$r = odbc_prepare($db, $query);
odbc_execute($r, [$idDVE]);


if (odbc_fetch_row($r)) {
	$d_Inicio_Vigencia = ymd2dmy(odbc_result($r, "Inicio"));
	$d_Fim_Vigencia = ymd2dmy(odbc_result($r, "Fim"));
	$p = odbc_result($r, "Periodo");
	$num = odbc_result($r, "Num");
	$v_Antecipado_Credito = odbc_result($r, "Total2");
	$sentDate = odbc_result($r, "SentDate");
	$periodo_dve = $d_Inicio_Vigencia . " à " . $d_Fim_Vigencia . " (" . $num . "ª DVE)";
}

$v_Total = $v_Vista_Prazo + $v_Antecipado_Credito + $v_Coligadas;

$key = session_id() . time();

odbc_free_result($r);

if ($cur) {
	$opt = [
		'mode' => 'win-1252',
		'tempDir' => sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'mpdf',
		'format' => 'A4-L',
		'margin_left' => 20,
		'margin_right' => 15,
		'margin_top' => 40,
		'margin_bottom' => 25,
		'margin_header' => 10,
		'margin_footer' => 10
	];

	$mpdf = new \Mpdf\Mpdf($opt);
	//$mpdf=new mPDF('win-1252','A4','','',10,10,35,25,10,10); 
	$html = ob_get_clean();
	// $mpdf->useOnlyCoreFonts = true;    // false is default
	//$mpdf->SetProtection(array('print'));
	$mpdf->SetTitle("Ficha de Aprocacao");
	$mpdf->SetAuthor($nomeEmp);
	$mpdf->SetWatermarkText(""); // fundo marca dágua
	$mpdf->showWatermarkText = true;
	$mpdf->watermark_font = 'DejaVuSansCondensed';
	$mpdf->watermarkTextAlpha = 0.1;
	$mpdf->SetDisplayMode('fullpage');

	// Endereço do logotipo
	$logo = '../../images/logo.jpg';

	// Início do arquivo montando primeiro o CSS	   
	$html = '<html>
							<head>
								<style>
									body {font-family: Arial, Helvetica, sans-serif; font-size: 12pt;}
									p {margin: 0pt;}
									table.bordasimples {border-collapse: collapse;}
									table.bordasimples tr td {border:2px solid #000000;}
									ol {counter-reset: item; font-weight:bold;}
									li {display: block; font-family: Arial, Helvetica, sans-serif; font-size: 12pt; text-align:justify;}
									li:before {content: counters(item, "."); counter-increment: item;}
									ul {list-style-type: none; font-weight:normal;} 
									ul li {padding: 3px 0px;color: #000000;text-align:justify;}
									#redondo {padding:60px; border:3px #000000 solid; border-radius:15px; -moz-border-radius:15px; -webkit-border-radius:15px;} 
									div.rounded {border:1mm solid #000000; background-color: #FFFFFF; border-radius: 3mm / 3mm; background-clip: border-box; padding: 1em;}
									#cobtexto {font-family: Arial, Helvetica, sans-serif; font-size:12pt; text-align:justify;}
									#sublinhado {font-family: Arial, Helvetica, sans-serif; font-size:12pt; text-align:justify; font-weight:bold; text-decoration:underline;}
									#disclame {font-family: Arial, Helvetica, sans-serif; font-size:7pt; text-align:right;}
								</style>
							</head>
							<body>

								<!--mpdf
								<htmlpageheader name="myheader">
									<table width="100%">
									  <tr>
									    <td colspan = "4"  style="text-align:right;">
											  <img src="' . $logo . '" width="230" height="75"/>
									    </td>						
									  </tr>	
									  <tr>
									    <td colspan = "4" style="text-align: center;">
											<span style="font-weight: bold; font-size: 14pt;">DVE - Declaração de Volume de Exportações</span>
									    </td>
									  </tr>
									</table>
								</htmlpageheader>
					
								<htmlpagefooter name="myfooter">
									<table width="100%" border="0">
									 	<tr>
										 	<td width="24%" style="text-align:center; font-size: 9pt;">Proc. SUSEP n.º: ' . $c_SUSEP . '</td>
										 	<td width="52%" style="text-align:center; font-size: 8pt;">
											 	Página {PAGENO} de {nb}
											</td>
											<td width="24%" style="text-align:right; font-size: 9pt;">Versão 05/2015</td>
										</tr>
									</table>									
								</htmlpagefooter>
					
								<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
								<sethtmlpagefooter name="myfooter" value="on" />
								mpdf-->
					
								<div align="left"><b>Segurado: </b>' . $Nome_Segurado . '</div>
								<div align="left"><b>Apólice: </b>' . $n_Apolice . '</div>
		            <div align="left"><b>Vigência: </b>' . $vigencia . '</div>
								<div align="left"><b>Período de Declaração: </b>' . $periodo_dve . '</div>
								<div align="left"><b>Data de Envio: </b>' . $d_Envio . '</div>
								<br>
					
								<table width="100%" style="border:solid 1px #000;" cellspacing="0" cellpadding="3">
								  <tr>
								    <td width="100%" colspan="4" style="border:solid 1px #000; text-align:center"><strong>Total exportado no período em ' . $extMoeda . '</strong></td>
								  </tr>
								  <tr>
										<td width="25%" colspan="1" style="border:solid 1px #000; text-align:center;"><strong>À Vista e/ou Cobrança a Prazo(Sujeito a cobertura de seguro)</strong></td>
									 	<td width="25%" colspan="1" style="border:solid 1px #000; text-align:center;"><strong>Antecipado e/ou Carta de Crédito</strong></td>
									 	<td width="25%" colspan="1" style="border:solid 1px #000; text-align:center;"><strong>Vendas via Coligadas</strong></td>
									 	<td width="25%" colspan="1" style="border:solid 1px #000; text-align:center;"><strong>Valor Total</strong></td>
								  </tr>	
								  <tr>
										<td width="25%" colspan="1" style="border:solid 1px #000; text-align:center;">' . number_format($v_Vista_Prazo ?? 0, 2, ',', '.') . '</td>
									 	<td width="25%" colspan="1" style="border:solid 1px #000; text-align:center;">' . number_format($v_Antecipado_Credito ?? 0, 2, ',', '.') . '</td>
									 	<td width="25%" colspan="1" style="border:solid 1px #000; text-align:center;">' . number_format($v_Coligadas ?? 0, 2, ',', '.') . '</td>
									 	<td width="25%" colspan="1" style="border:solid 1px #000; text-align:center;">' . number_format($v_Total ?? 0, 2, ',', '.') . '</td>
								  </tr>
								</table>
								
								<br>
								<br>

								<table width="100%" style="border:solid 1px #000;" cellspacing="0" cellpadding="3">
						  		<tr>
						     		<td width="10%" rowspan="2" style="border:solid 1px #000; font-size: 10pt;"><strong>Nº SBCE</strong></td>
										<td width="25%" rowspan="2" style="border:solid 1px #000; font-size: 10pt;"><strong>Comprador</strong></td>
										<td width="10%" rowspan="2" style="border:solid 1px #000; font-size: 10pt;"><strong>Pa&iacute;s</strong></td>
										<td width="10%" rowspan="2" style="border:solid 1px #000; font-size: 10pt;"><strong>Data Embarque</strong></td>
										<td width="10%" rowspan="2" style="border:solid 1px #000; font-size: 10pt;"><strong>Nº da Fatura</strong></td>	
										<td width="10%" rowspan="2" style="border:solid 1px #000; font-size: 10pt;"><strong>Data Vencimento</strong></td>	
										<td width="10%" rowspan="2" style="border:solid 1px #000; font-size: 10pt;"><strong>Valor Embarcado</strong></td>
										<td width="15%" colspan="2" rowspan="1" style="border:solid 1px #000; font-size: 10pt; padding: 0">
								 		<table width="100%" style="width:400px; padding:0">
								    	<tr>
									   		<td width="200px" style="text-align:center; font-size: 10pt;"><strong>Valor Financiado</strong></td>
											</tr>
								 		</table>								 
						  		</tr>
									<tr>
										<td width="15%" colspan="2" rowspan="1" style="border-left:solid 1px #000; font-size: 10pt; padding: 0">
									 		<table width="200px" style="padding:0" cellspacing="0">
									    	<tr style="padding:0">
										 			<td width="50%" style="text-align:center; border:1px solid #000; font-size: 10pt; width: 100px;"><strong>PROEX</strong></td>
										 			<td width="50%" style="text-align:center;border:1px solid #000; font-size: 10pt; width: 100px;"><strong>ACE</strong></td>
									    	<tr>
									 		</table>
								 		</td>
								 	</tr>';

	$i = 1;
	$V_Embarque_Total = 0;
	$V_PROEX_Total = 0;
	$V_ACE_Total = 0;

	odbc_free_result($cur);

	$qry1 = "SELECT i.c_Coface_Imp, i.name, c.name, dt.embDate, dt.fatura,
                 dt.vencDate, dt.totalEmbarcado, dt.proex, dt.ace, idImporter
          FROM DVE d
          JOIN DVEDetails dt ON d.id = dt.idDVE
          LEFT JOIN Importer i ON dt.idImporter = i.id
          LEFT JOIN Country c ON dt.idCountry = c.id
          WHERE d.id = ? AND dt.modalidade = 1 AND dt.state = 1
          ORDER BY dt.embDate";

	$cur = odbc_prepare($db, $qry1);
	odbc_execute($cur, [$idDVE]);

	while (odbc_fetch_row($cur)) {
		$val_proex = odbc_result($cur, "proex");
		$val_ace = odbc_result($cur, "ace");

		$V_Embarque_Total += odbc_result($cur, "totalEmbarcado");
		$V_PROEX_Total += $val_proex;
		$V_ACE_Total += $val_ace;

		$html .= '
											<tr>	
												<td style="border:solid 1px #000; font-size:10pt;">' . odbc_result($cur, 1) . '</td>
												<td style="border:solid 1px #000; font-size:10pt;">' . trim(odbc_result($cur, 2)) . '</td>
												<td style="border:solid 1px #000; font-size:10pt;">' . trim(odbc_result($cur, 3)) . '</td>
												<td style="border:solid 1px #000; font-size:10pt;">' . ymd2dmy(odbc_result($cur, 4)) . '</td>
												<td style="border:solid 1px #000; font-size:10pt;">' . odbc_result($cur, 5) . '</td>
												<td style="border:solid 1px #000; font-size:10pt;">' . ymd2dmy(odbc_result($cur, 6)) . '</td>
												<td style="border:solid 1px #000; font-size:10pt; text-align:right;">' . number_format(odbc_result($cur, "totalEmbarcado"), 2, ',', '.') . '</td>	
												<td style="border:solid 1px #000; font-size:10pt; text-align:right; width: 100px;">' . number_format($val_proex, 2, ',', '.') . '</td>	
												<td style="border:solid 1px #000; font-size:10pt; text-align:right; width: 100px;">' . number_format($val_ace, 2, ',', '.') . '</td>
											</tr>';

		$i++;
	}

	odbc_free_result($cur);

	$qry2 = "SELECT i.c_Coface_Imp, i.name, c.name, dt.embDate, dt.fatura,
                 dt.vencDate, dt.totalEmbarcado, dt.proex, dt.ace, idImporter
          FROM DVE d
          JOIN DVEDetails dt ON d.id = dt.idDVE
          LEFT JOIN Importer i ON dt.idImporter = i.id
          LEFT JOIN Country c ON dt.idCountry = c.id
          WHERE d.id = ? AND dt.modalidade = 2 AND dt.state = 1
          ORDER BY dt.embDate";

	$cur = odbc_prepare($db, $qry2);
	//error_log("chamou o bagulho");
	odbc_execute($cur, [$idDVE]);

	while (odbc_fetch_row($cur)) {
		//error_log("while while while");
		$val_proex = odbc_result($cur, "proex");
		$val_ace = odbc_result($cur, "ace");

		$V_Embarque_Total += odbc_result($cur, "totalEmbarcado");
		$V_PROEX_Total += $val_proex;
		$V_ACE_Total += $val_ace;

		$html .= '
											<tr>	
												<td style="border:solid 1px #000; font-size:10pt; ">' . odbc_result($cur, 1) . '</td>
												<td style="border:solid 1px #000; font-size:10pt;">' . trim(odbc_result($cur, 2)) . '</td>
												<td style="border:solid 1px #000; font-size:10pt;">' . trim(odbc_result($cur, 3)) . '</td>
												<td style="border:solid 1px #000; font-size:10pt;">' . ymd2dmy(odbc_result($cur, 4)) . '</td>
												<td style="border:solid 1px #000; font-size:10pt; ">' . odbc_result($cur, 5) . '</td>
												<td style="border:solid 1px #000; font-size:10pt; ">' . ymd2dmy(odbc_result($cur, 6)) . '</td>
												<td style="border:solid 1px #000; font-size:10pt; text-align:right;">' . number_format(odbc_result($cur, "totalEmbarcado"), 2, ',', '.') . '</td>	
												<td style="border:solid 1px #000; font-size:10pt; text-align:right; width: 100px;">' . number_format($val_proex, 2, ',', '.') . '</td>	
												<td style="border:solid 1px #000; font-size:10pt; text-align:right; width: 100px;">' . number_format($val_ace, 2, ',', '.') . '</td>	
											</tr>';
	}

	$html .= '
									<tr>	
									  <td colspan="6" style="border:solid 1px #000; font-size:10pt; text-align:center;"><strong>TOTAL</strong></td>
									  <td colspan="1" style="border:solid 1px #000; font-size:10pt; text-align:right;"><strong>' . number_format($V_Embarque_Total, 2, ',', '.') . '</strong></td>	
									  <td colspan="1" style="border:solid 1px #000; font-size:10pt; text-align:right;"><strong>' . number_format($V_PROEX_Total, 2, ',', '.') . '</strong></td>	
									  <td colspan="1" style="border:solid 1px #000; font-size:10pt; text-align:right;"><strong>' . number_format($V_ACE_Total, 2, ',', '.') . '</strong></td>
									</tr>
								</table>

								<div style="clear=both">&nbsp;</div>

								<div id="cobtexto">
									Declaramos que as informações constantes neste documento são completas e verdadeiras e assumimos, sob as penas contratuais e legais, a responsabilidade 
									por sua exatidão.<br><br>
									De acordo com as Condições Gerais e Módulo de Faturamento da Apólice de Seguro de Crédito à Exportação, a presente Declaração de Volume de Exportações 
									deve abranger a totalidade das exportações realizadas pelo Segurado aos importadores cobertos pela presente apólice.
								</div>
							</body>
						</html>';
}
odbc_free_result($cur);
$html = mb_convert_encoding($html, 'UTF-8', 'ISO-8859-1');
$mpdf->allow_charset_conversion = true;
$mpdf->charset_in = 'UTF-8';
$mpdf->WriteHTML($html);

// $mpdf->Output($pdfDir.$key.'DVE.pdf','F'); 

$mpdf->Output();

// $url_pdf = $host.'src/download/'.$key.'DVE.pdf';

// echo "<HTML><HEAD><META HTTP-EQUIV=\"refresh\" content=\"0;URL=$loc\"><TITLE></TITLE></HEAD></HTML>";
?>