<!--
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<title>SIEX &mdash; Sistema Integrado de Exporta&ccedil;&atilde;o &mdash; Coface</title>
</head>

<body>
-->
<?php

// error_reporting(E_WARNING);
if (!isset($_SESSION)) {
	session_start();
}

ini_set("pcre.backtrack_limit", "5000000");

require_once("../../../dbOpen.php");

function formata_string($modo, $doc)
{
	if ($modo == "CPF") {
		if ($doc == "" || $doc == null)
			return "000.000.000-00";
		$doc = preg_match("/[^0-9]/", "", $doc);
		$doc = str_pad($doc, 11, 0, STR_PAD_LEFT);
		if (strlen($doc) == 11)
			return $doc[0] . $doc[1] . $doc[2] . "." . $doc[3] . $doc[4] . $doc[5] . "." . $doc[6] . $doc[7] . $doc[8] . "-" . $doc[9] . $doc[10];
		else
			return false;
	}

	if ($modo == "CNPJ") {
		if ($doc == "" || $doc == null)
			return "00.000.000/00000-00";
		$doc = preg_match("/[^0-9]/", "", $doc);
		$doc = str_pad($doc, 14, 0, STR_PAD_LEFT);
		if (strlen($doc) == 14)
			return $doc[0] . $doc[1] . "." . $doc[2] . $doc[3] . $doc[4] . "." . $doc[5] . $doc[6] . $doc[7] . '/' . $doc[8] . $doc[9] . $doc[10] . $doc[11] . "-" . $doc[12] . $doc[13];
		else
			return false;
	}


	if ($modo == "IE") {
		if ($doc == "" || $doc == null)
			return "000.000.000.000";
		$doc = preg_match("/[^0-9]/", "", $doc);
		$doc = str_pad($doc, 12, 0, STR_PAD_LEFT);
		if (strlen($doc) == 12)
			return $doc[0] . $doc[1] . $doc[2] . "." . $doc[3] . $doc[4] . $doc[5] . "." . $doc[6] . $doc[7] . $doc[8] . "." . $doc[9] . $doc[10] . $doc[11];
		else
			return false;
	}

	if ($modo == "TEL") {
		if ($doc == "" || $doc == null)
			return "(00) 0000-0000";
		$doc = preg_match("/[^0-9]/", "", $doc);
		$doc = str_pad($doc, 10, 0, STR_PAD_LEFT);
		if (strlen($doc) == 10)
			return "(" . $doc[0] . $doc[1] . ") " . $doc[2] . $doc[3] . $doc[4] . $doc[5] . "-" . $doc[6] . $doc[7] . $doc[8] . $doc[9];
		else
			return false;
	}

	if ($modo == "CEP") {
		if ($doc == "" || $doc == null)
			return "00000-000";
		$doc = preg_match("/[^0-9]/", "", $doc);
		$doc = str_pad($doc, 8, 0, STR_PAD_LEFT);
		if (strlen($doc) == 8)
			return $doc[0] . $doc[1] . $doc[2] . $doc[3] . $doc[4] . '-' . $doc[5] . $doc[6] . $doc[7];
		else
			return false;
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

$idInform = $_REQUEST['inform'];

//print $_REQUEST['inform'];
$query = "SELECT * FROM Inform WHERE id = ?";
$curv = odbc_prepare($db, $query);
odbc_execute($curv, [$idInform]);

$nMoeda = odbc_result($curv, "currency");

odbc_free_result($curv);

if ($nMoeda == "1") {
	$extMoeda = "R\$";
} else if ($nMoeda == "2") {
	$extMoeda = "US\$";
} else {
	$extMoeda = "&euro;";
}

$sqlEmp = "SELECT Nome, CNPJ, Endereco, Complemento, CEP, Cidade, Estado, Cod_Area, Telefone, Bairro, Fax, HomePage
           FROM Empresa 
           WHERE i_Empresa = ?";

$resEmp = odbc_prepare($db, $sqlEmp);
odbc_execute($resEmp, [1]);

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

odbc_free_result($dadosEmp);

include_once("../../../../gerar_pdf/MPDF45/mpdf.php");

$opt = [
	'mode' => 'win-1252',
	'tempDir' => sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'mpdf',
	'format' => 'A4',
	'margin_left' => 20,
	'margin_right' => 15,
	'margin_top' => 48,
	'margin_bottom' => 25,
	'margin_header' => 10,
	'margin_footer' => 10
];

$mpdf = new \Mpdf\Mpdf($opt);
$html = ob_get_clean();
// $mpdf->useOnlyCoreFonts = true;    // false is default
//$mpdf->SetProtection(array('print'));
$mpdf->SetTitle("Proposta");
$mpdf->SetAuthor($nomeEmp);
$mpdf->SetWatermarkText(""); // fundo marca dágua
$mpdf->showWatermarkText = true;
$mpdf->watermark_font = 'DejaVuSansCondensed';
$mpdf->watermarkTextAlpha = 0.1;
$mpdf->SetDisplayMode('fullpage');




// Endereço do logotipo
$logo = '../../../../images/logo.jpg';


// Início do arquivo montando primeiro o CSS

$html = '<html>
				<head>
				<style>
				body {font-family: Arial, Helvetica, sans-serif;
					font-size: 12pt;
				}
				p {    margin: 0pt;
				}
					
				
				ol {counter-reset: item; font-weight:bold; }
                li {display: block; font-family: Arial, Helvetica, sans-serif; font-size: 12pt; text-align:justify}
                li:before {content: counters(item, "."); counter-increment: item; }
				
				ul			{list-style-type: none; font-weight:normal } 
				ul li		{padding: 3px 0px;color: #000000;text-align:justify} 

                div.rounded {
							border:0.5mm solid #000000; 
							background-color: #FFFFFF;
							border-radius: 2mm / 2mm;
							background-clip: border-box;
							padding: inherit;
				}
				div.rounded1 {
							border:0.5mm solid #000000; 
							background-color: #FFFFFF;
							border-radius: 2mm / 2mm;
							background-clip: border-box;
							padding: inherit;
				}
				
				#cobtexto  {font-family: Arial, Helvetica, sans-serif; font-size:12pt; text-align:justify;}
				#sublinhado {font-family: Arial, Helvetica, sans-serif; font-size:12pt; text-align:justify; font-weight:bold; text-decoration:underline;}
				#disclame  {font-family: Arial, Helvetica, sans-serif; font-size:7pt; text-align:right;}
				
				</style>
				
				</head>
				<body>
				<htmlpageheader name="myheader">';



$html .= ' <!--mpdf
						<htmlpageheader name="myheader">
						  <div style="text-align: right;">
							 <img src="' . $logo . '" widht ="80" height="40"/>
						  </div>
						  <br>
						  <div style="text-align: left;">
							  <span style="font-weight: bold; font-size: 12pt;">DADOS DO INFORME</span>
						  </div>	
						  <div style="border-top: 2px solid #000000; font-size: 10pt; text-align: center; padding-top: 3mm; ">
					</htmlpageheader>
					
					<htmlpagefooter name="myfooter">
							<div align="right">Página {PAGENO} de {nb}</div>
							
					</htmlpagefooter>
					
					
					
					<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
					<sethtmlpagefooter name="myfooter" value="on" />
					mpdf-->
					
					';

$qry = "select 
								a.comRisk,
								a.polRisk,
								a.name,
								a.address,
								a.city,
								a.uf,
								a.cep,
								a.tel,
								a.fax,
								a.email,
								a.contact,
								a.ocupationContact,
								a.emailContact,
								a.cnpj,
								a.ie,
								a.idSector,
								a.products,
								a.frameMed,
								a.hasGroup,
								a.exportMore,
								a.companyGroup,
								a.hasAssocCompanies,
								a.associatedCompanies,
								a.warantyExp,
								a.warantyFin,
								a.hasAnother,
								a.another,
								a.idRegion,
								a.warantyInterest,
								a.sameAddress,
								a.chargeAddress,
								a.chargeCity,
								a.chargeUf,
								a.chargeCep,
								a.generalState,
								a.addressNumber,
								a.chargeAddressNumber,
								a.addressComp,
								a.chargeAddressComp,
								a.pvigencia,
								a.i_Empresa,
								a.i_Produto,
								a.currency,
								a.Periodo_Vigencia,
								a.i_Gerente,
								b.Nome as NomeGerente,
								c.name as estado
								from Inform a 
								left join  Gerente_Comercial b on a.i_Gerente = b.i_Gerente
								left join Region c on a.idRegion = c.id							
								
								where a.id= ? ";
$res = odbc_prepare($db, $qry);
odbc_execute($res, [$idInform]);

$comandinhoTop = 'select description from Region where id  = ' . odbc_result($res, 'idRegion');

$html .= '<strong>1.1 DADOS CADASTRAIS</strong><br>';

while (odbc_fetch_row($res)) {
	$html .= ' 
					              <div class="rounded"> 											
								    <table width="100%" border="0" align="center" cellspacing="2">		
					                    <tr>
										    <td><strong>Per&iacute;odo de vig&ecirc;ncia</strong></td>
											<td>' . odbc_result($res, 'Periodo_Vigencia') . ' Meses</td>
										</tr>
										';

	$i_Produto = odbc_result($res, 'i_Produto');


	if ($i_Produto != '') {
		$query = "SELECT * FROM Produto WHERE Situacao = ? AND i_Produto = ?";
		$cury = odbc_prepare($db, $query);
		odbc_execute($cury, [1, $i_Produto]);

		$html .= '
										      <tr>
										        <td><strong>Tipo de Produto</strong></td><td>' . (odbc_result($cury, 'Nome')) . '</td>
											  </tr>';
		odbc_free_result($cury);
		$query = "SELECT * 
          FROM Moeda MO 
          INNER JOIN Produto_Moeda PM 
          ON PM.i_Moeda = MO.i_Moeda 
          WHERE MO.Situacao = ? AND PM.i_Produto = ?";

		$res3 = odbc_prepare($db, $query);
		odbc_execute($res3, [1, $i_Produto]);


		$html .= '
											<tr>
											   <td><strong>Tipo de Moeda</strong></td><td>' . (odbc_result($res3, 'Nome')) . '</td>
											</tr>
											
											<tr>
											   <td><strong>Cobertura para Juros Mora </strong></td><td>' . (odbc_result($res, 'warantyInterest') == 1 ? 'Sim' : 'N&atilde;o') . '</td>
											</tr>';
	}


	odbc_free_result($res3);

	$query = "SELECT * FROM Sector WHERE id = ?";
	$rs1 = odbc_prepare($db, $query);
	odbc_execute($rs1, [odbc_result($res, 'idSector')]);

	$html .= '
										<tr>
										   <td><strong>Natureza da Opera&ccedil;&atilde;o</strong></td><td>' . (odbc_result($rs1, 'description')) . '</td>
										</tr>
										
										<tr>
										   <td colspan="2" style="text-align:center"><strong>Dados da Empresa (Segurado)</strong></td>
										</tr>
										
										
										<tr>
										   <td><strong>Raz&atilde;o Social</strong></td><td>' . strtoupper(odbc_result($res, 'name')) . '</td>
										</tr>
										
										<tr>
										   <td><strong>Nome Fantasia</strong></td><td>' . strtoupper(odbc_result($res, 'name')) . '</td>
										</tr>
										
										<tr>
										   <td><strong>CNPJ</strong></td><td style="text-align:left">' . formata_string('CNPJ', odbc_result($res, 'cnpj')) . '</td>
										</tr>
										
										<tr>
										  <td><strong>Inscri&ccedil;&atilde;oo Estadual ou Municipal </strong></td><td style="text-align:left">' . formata_string('IE', odbc_result($res, 'ie')) . '</td>
										</tr>  												
													
										<tr>										
										   <td><strong>Endere&ccedil;o</strong></td><td>' . (odbc_result($res, 'address')) . ', ' . odbc_result($res, 'addressNumber') . '</td>
										</tr>		
										
                                        <tr>										
										   <td><strong>Complemento</strong></td><td>' . (odbc_result($res, 'addressComp')) . '</td>
										</tr>	
										
										<tr>
										   <td><strong>Cidade</strong></td><td>' . (odbc_result($res, 'city')) . '</td>
										</tr>
										
										<tr>
										   <td><strong>UF</strong></td><td>' . odbc_result($res, 'estado') . '</td>
										</tr>
										
										<tr>
										   <td><strong>Cep</strong></td><td>' . formata_string('CEP', odbc_result($res, 'cep')) . '</td>
										</tr>   
										
										<tr>
										   <td><strong>Regi&atilde;o </strong></td><td>' . odbc_result(odbc_exec($db, $comandinhoTop), 1) . '</td>
										</tr>     
										  					
																		
										<tr>
										   <td><strong>E-mail da Empresa</strong></td><td>' . odbc_result($res, 'email') . '</td>
										</tr>
										
										<tr>
										   <td><strong>Contato</strong></td><td>' . (odbc_result($res, 'contact')) . '</td>
										</tr>	
										
										<tr>
										   <td><strong>E-mail do Contato</strong></td><td>' . odbc_result($res, 'emailContact') . '</td>
										</tr>
											   
										<tr>
										   <td><strong>Cargo</strong></td><td>' . (odbc_result($res, 'emailContact')) . '</td>
										</tr>
										
										<tr>
										   <td><strong>Telefone (DDD)</strong></td><td>' . formata_string('TEL', odbc_result($res, 'tel')) . '</td>
										</tr>
										
										<tr>
										   <td><strong>Fax (DDD)</strong></td><td>' . (odbc_result($res, 'fax') ? formata_string('TEL', odbc_result($res, 'fax')) : 'N&atilde;o informado') . '</td>
										</tr>
										
										<tr>
										   <td colspan="2" style="text-align:center"><strong>Dados da cobran&ccedil;a</strong></td>
										</tr>
										
										<tr>																			
										 <td><strong>Utilizar Dados acima para cobran&ccedil;a?</strong></td><td>' . (odbc_result($res, 'sameAddress') == 1 ? 'Sim' : 'N&atilde;o') . '</td>
										</tr>';

	if (odbc_result($res, 'sameAddress') != 1) {
		$html .= '
											<tr>										
											   <td><strong>Endere&ccedil;o</strong></td><td>' . (odbc_result($res, 'chargeAddress')) . ', ' . odbc_result($res, 'chargeAddressNumber') . '</td>
											</tr>		
											
											<tr>										
											   <td><strong>Complemento</strong></td><td>' . (odbc_result($res, 'chargeAddressComp')) . '</td>
											</tr>	
											
											<tr>
											   <td><strong>Cidade</strong></td><td>' . (odbc_result($res, 'chargeCity')) . '</td>
											</tr>
											
											<tr>
											   <td><strong>UF</strong></td><td>' . odbc_result($res, 'chargeEstado') . '</td>
											</tr>
											
											<tr>
											   <td><strong>Cep</strong></td><td>' . formata_string('CEP', odbc_result($res, 'chargeCep')) . '</td>
											</tr>';
	}


	$html .= '
										
										<tr>
										   <td colspan="2" style="text-align:center"><strong>Atividade Comercial</strong></td>
										</tr>
										<tr>																			
													 <td><strong>Setor de Atividade</strong></td><td>' . (odbc_result($rs1, 'description')) . '</td>
													</tr>		
													
													<tr>
													   <td><strong>Principais Produtos</strong></td><td>' . (odbc_result($res, 'products')) . '</td>
													</tr>															
																												
													<tr>
													   <td><strong>Comercializa h&aacute; mais de 3 anos?</strong></td><td>' . (odbc_result($res, 'exportMore') ? 'Sim' : 'N&atilde;o') . '</td>
													</tr>
													
													<tr>
										              <td colspan="2" style="text-align:center"><strong>Informa&ccedil;&otilde;es Gerais</strong></td>
										           </tr>
													<tr>
													   <td><strong>Pertence a algum Grupo Internacional?</strong></td><td>' . (odbc_result($res, 'associatedCompanies') ? 'Sim' : 'N&atilde;o') . '</td>
													</tr>';

	if (odbc_result($res, 'associatedCompanies')) {
		$html .= '<tr>
											            <td colspan="2">' . (odbc_result($res, 'companyGroup')) . '
											           </td>
											         </tr>';
	}
	odbc_free_result($rs1);

	$html .= '
										
										<tr>
										   <td><strong>A Empresa Possui Companhias Associadas No Exterior?</strong></td><td>' . (odbc_result($res, 'hasAssocCompanies') ? 'Sim' : 'N&atilde;o') . '</td>
                                        </tr>';

	if (odbc_result($res, 'hasAssocCompanies')) {
		$html .= '
												   <tr>
													  <td colspan="2">' . (odbc_result($res, 'associatedCompanies')) . '
													  </td>
												  </tr>';
	}

	$html .= '
									    <tr>
										   <td colspan="2" style="text-align:center"><strong>Objetivos</strong></td>
                                        </tr>
										';


	if (odbc_result($res, 'warantyExp')) {
		$html .= '<tr><td><strong>Garantia &agrave; expota&ccedil;&atilde;o?</strong></td><td>Sim</td></tr>';
	}
	if (odbc_result($res, 'warantyFin')) {
		$html .= '<tr><td><strong>Garantia para financiamentos &agrave; exporta&ccedil;&atilde;o?</strong></td><td>Sim</td></tr>';
	}
	if (odbc_result($res, 'hasAnother')) {
		$html .= '<tr><td><strong>Outros Quais?</strong></td><td>' . (odbc_result($res, 'another')) . '</td></tr>';
	}

	$html .= '
										<tr>
										    <td><strong>Gerente comercial</td><td>' . odbc_result($res, 'NomeGerente') . '</strong></td>
										</tr>		
																																								
								  </table>
			                 
								  </div><br><BR><pagebreak />';

}



$query91 = "SELECT * 
            FROM Inform_Organizacao_Credito b 
            INNER JOIN Inform a ON a.id = b.i_Inform 
            WHERE a.id = ?";
$cur91 = odbc_prepare($db, $query91);
odbc_execute($cur91, [$idInform]);

if (odbc_fetch_row($cur91)) {
	$html .= '
										  <br>
										    <strong>1.2 ORGANIZA&Ccedil;&Atilde;O DO DEPARTAMENTO DE CR&Eacute;DITO</strong>	
											<div class="rounded"> 
											
											<table width="100%" border="0" align="center" cellspacing="2">											     										
												  <tr>
												    <td width="45%">Existe um departamento de gerenciamento de cr&eacute;dito? </td>
													 <td width="55%">' . (odbc_result($cur91, "Possui_Departamento_Credito") == 1 ? " Sim " : "N&atilde;o") . '</td>
												  </tr>
												  <tr>
												    <td width="45%">Existe um manual de procedimentos / pol&iacute;tica de cr&eacute;dito?</td>
													<td width="55%">' . (odbc_result($cur91, "Possui_Manual_Procedimento") == 1 ? " Sim " : "N&atilde;o") . '</td>
												  </tr>
												  
												  <tr> 
													<td  width="45%">Respons&aacute;vel Nome</td>
													<td width="55%">' . odbc_result($cur91, "Nome_Responsavel") . '</td>
												  </tr>
												  
												  <tr>
													<td width="45%">Cargo</td>
													<td width="55%">' . odbc_result($cur91, "Cargo_Responsavel") . '</td>
												  </tr>	
												  
												   <tr>
													<td width="45%">A quem se Reporta</td>
													<td width="55%">' . odbc_result($cur91, "Nome_Reporta") . '</td>
												  </tr>	
												  
												  <tr>
													<td width="45%">Cargo</td>
													<td width="55%">' . odbc_result($cur91, "Cargo_Reporta") . '</td>
												  </tr>	
												  
												  <tr>
													<td width="45%">Per&iacute;odo de  Cr&eacute;dito Normalmente Concedido em Dias</td>
													<td width="55%"> M&eacute;dio: ' . odbc_result($cur91, "Periodo_Credito_Medio") . ' e M&aacute;ximo: ' . odbc_result($cur91, "Periodo_Credito_Maximo") . '</td>
												  </tr>	
												  
												   <tr>
													<td width="45%">A Empresa possui uma &aacute;rea dedicada a Controles Internos e Preven&ccedil;&atilde;o &agrave; Lavagem de Dinheiro ?</td>
													<td width="55%">' . (odbc_result($cur91, "Possui_Area_Controle") == 1 ? 'Sim' : 'N&atilde;o') . '</td>
												  </tr>	
												  
												   <tr>
													<td width="45%">A Empresa tem algum s&oacute;cio/acionista ou seus respectivos familiares enquadrados como pessoa politicamente esposta - 
													PPE (Exerce ou exerceu nos &uacute;ltimos 5 anos cargos p&uacute;blicos), conforme circular SUSEP 380/08 ?</td>
													<td width="55%">' . (odbc_result($cur91, "Relacao_PPE") == 1 || odbc_result($cur91, "Relacao_PPE") == 2 ? 'Sim' : 'N&atilde;o') . '</td>
												  </tr>';

	if (odbc_result($cur91, "Relacao_PPE") == 1) {
		$html .= '
														  <tr>
															<td width="45%">Cargo PPE</td>
															<td width="55%">' . odbc_result($cur91, "Cargo_PPE") . '</td>
														  </tr>';
	} else if (odbc_result($cur91, "Relacao_PPE") == 2) {
		$html .= '
															  <tr>
																<td width="45%">Cargo PPE</td>
																<td width="55%">' . odbc_result($cur91, "Cargo_PPE") . '</td>
															 </tr>
															 <tr>
																<td width="45%">Parentesco PPE</td>
																<td width="55%">' . odbc_result($cur91, "Parentesco_PPE") . '</td>
															 </tr>	';
	}

	$html .= '	  								
																			
										  </table>
										  </div>
										  <br><br><BR><pagebreak />';

}
odbc_free_result($cur91);
$query = "SELECT * FROM Volume WHERE idInform = ?";
$curj = odbc_prepare($db, $query);
odbc_execute($curj, [$_REQUEST["inform"]]);

if (odbc_fetch_row($curj)) {
	$html .= '
										  <br>
										    <strong>1.3 PROJEÇÃO DE VENDAS</strong>	
											<div class="rounded"> 
											
											<table width="100%" border="0" align="center" cellspacing="2">											     										
												  <tr>
												    <td></td>
													<td style="text-align:right">Pagamento Antecipado</td>
													<td style="text-align:right">Venda Sujeita a Seguro (**)</td>
													<td style="text-align:right">Vendas para Companhias Associadas</td>
													<td style="text-align:right">Totais</td>
												  </tr>									
												  <tr>
													<td>Previs&atilde;o Pr&oacute;ximos 12 Meses</td>
													<td style="text-align:right">' . number_format(odbc_result($curj, "vol1") ?? 0, 2, ",", ".") . '</td>
													<td style="text-align:right">' . number_format(odbc_result($curj, "vol2") ?? 0, 2, ",", ".") . '</td>
													<td style="text-align:right">' . number_format(odbc_result($curj, "vol6") ?? 0, 2, ",", ".") . '</td>
													<td style="text-align:right">' . number_format(odbc_result($curj, "vol8") ?? 0, 2, ",", ".") . '</td>
												  </tr>
												
												 <tr>
													<td>N&ordm; de Compradores</td>
													<td style="text-align:right">' . number_format(odbc_result($curj, "vol9") ?? 0, 0, ".", "") . '</td>
													<td style="text-align:right">' . number_format(odbc_result($curj, "vol10") ?? 0, 0, ".", "") . '</td>
													<td style="text-align:right">' . number_format(odbc_result($curj, "vol14") ?? 0, 0, ".", "") . '</td>
													<td style="text-align:right">' . number_format(odbc_result($curj, "vol16") ?? 0, 0, ".", "") . '</td>
											   </tr>										
										  </table>
										  </div>
										  <br><br>
										  
										  <strong>1.4 HISTÓRICO DE VENDAS</strong>
										  
										  <div class="rounded"> 
										  <table width="100%" border="0" align="center" cellspacing="2">
										   											 
												  <tr>
													<td width="162">&nbsp;</td>
													<td>Pagamento Antecipado</td>
													<td>Venda Sujeita a Seguro (**)</td>
													<td>Vendas para Companhias Associadas</td>
													<td style="text-align:right">Totais</td>
												  </tr>
											  
												  <tr>
													<td width="162">Ano Corrente (*)</td>
													<td style="text-align:right">' . number_format(odbc_result($curj, "vol17") ?? 0, 2, ",", ".") . '</td>
													<td style="text-align:right">' . number_format(odbc_result($curj, "vol18") ?? 0, 2, ",", ".") . '</td>
													<td style="text-align:right">' . number_format(odbc_result($curj, "vol22") ?? 0, 2, ",", ".") . '</td>
													<td style="text-align:right">' . number_format(odbc_result($curj, "vol24") ?? 0, 2, ",", ".") . '</td>
												 </tr>
												  <tr>
													<td width="162">Ano Passado</td>
													<td style="text-align:right">' . number_format(odbc_result($curj, "vol25") ?? 0, 2, ",", ".") . '</td>
													<td style="text-align:right">' . number_format(odbc_result($curj, "vol26") ?? 0, 2, ",", ".") . '</td>
													<td style="text-align:right">' . number_format(odbc_result($curj, "vol30") ?? 0, 2, ",", ".") . '</td>
													<td style="text-align:right">' . number_format(odbc_result($curj, "vol32") ?? 0, 2, ",", ".") . '</td>
												  </tr>
												  <tr>
													<td width="162">Ano Retrasado</td>
													<td style="text-align:right">' . number_format(odbc_result($curj, "vol33") ?? 0, 2, ",", ".") . '</td>
													<td style="text-align:right">' . number_format(odbc_result($curj, "vol34") ?? 0, 2, ",", ".") . '</td>
													<td style="text-align:right">' . number_format(odbc_result($curj, "vol38") ?? 0, 2, ",", ".") . '</td>
													<td style="text-align:right">' . number_format(odbc_result($curj, "vol40") ?? 0, 2, ",", ".") . '</td>
												  </tr>
												  <tr>
													<td colspan="4">(*) N&uacute;mero de Meses</td>
													<td style="text-align:right">' . number_format(odbc_result($curj, "vol41") ?? 0, 0, "", "") . '</td>
												 </tr>
											
											
												 <tr>
													<td colspan="5">(**) Cobran&ccedil;a &agrave; vista, a prazo e carta de cr&eacute;dito n&atilde;o confirmada </td>
												 </tr>
											
										</table></div><pagebreak />';

}



$i_Produto = odbc_result($res, 'i_Produto');
odbc_free_result($curj);
odbc_free_result($res);
if ($i_Produto == 2) {
	$html .= '<br><strong>1.5 DISTRIBUIÇÃO DE VENDAS POR TIPO E CANAL</strong>';
} else {
	$html .= '<br><strong>1.5 DISTRIBUIÇÃO DE VENDAS POR CANAL E REGIÕES</strong>';
}

$qry = "SELECT Inf.i_Produto, Inf.id, VTC.i_Venda_Canal, VTC.Tipo_Venda, VTC.Descricao, 
               ISNULL(IVTC.v_Valor, 0) AS v_Valor
        FROM Inform Inf
        INNER JOIN Venda_Tipo_Canal VTC ON VTC.i_Produto = Inf.i_Produto
        LEFT JOIN Inform_Venda_Tipo_Canal IVTC ON IVTC.i_Venda_Canal = VTC.i_Venda_Canal 
                                               AND IVTC.i_Inform = Inf.id
        WHERE Inf.id = ? AND (VTC.Situacao = 0 OR IVTC.v_Valor IS NOT NULL)
        ORDER BY VTC.i_Venda_Canal";

$cur = odbc_prepare($db, $qry);
odbc_execute($cur, [$idInform]);


$count1 = 0;
$count2 = 0;
$total1 = 0;
$total2 = 0;
$total3 = 0;
$total4 = 0;
$i = 0;

$tipovenda = '';
while (odbc_fetch_row($cur)) {

	if (odbc_result($cur, 'Tipo_Venda') != $tipovenda) {
		$tipovenda = odbc_result($cur, 'Tipo_Venda');
		$i++;
	}

	if ($i == 1) {
		$total1 += odbc_result($cur, 'v_Valor');

		$total2 = 100;
		$count1++;
	} else if ($i == 2) {
		$total3 += odbc_result($cur, 'v_Valor');
		$total4 = 100;
		$count2++;
	}


}

$i = 0;
$x = 0;
$tipovenda = "";
while (odbc_fetch_row($cur)) {
	if (odbc_result($cur, 'Tipo_Venda') != $tipovenda) {
		$tipovenda = odbc_result($cur, 'Tipo_Venda');
		$x = 0;
		if ($i == 1) {

			$html .= '
															    <tr>
																 <td><strong>Total</strong></td>
																 <td style="text-align:right"><strong>' . number_format($total1, 2, ',', '.') . '</strong></td>
																 <td style="text-align:right"><strong>' . number_format($total2, 2, ',', '.') . '</strong></td>
																</tr>
														              
														      </table></div>';

		}

		$html .= ' <div class="rounded1" style="width:325px; float: ' . ($i > 1 ? 'right' : 'left') . '; margin:0 20px 0 0;"> 										     
											                    <table width="100%" border="0" cellspacing="2" style="width:450px;">';


		if ($i == 0) {
			$html .= '<tr>
															   <td width="50%"><strong>' . odbc_result($cur, "Tipo_Venda") . '</strong></td>
															   <td width="25%" style="text-align:right"><strong>Valor ' . $extMoeda . '</strong></td>
															   <td width="25%" style="text-align:right"><strong>% </strong></td></tr>					  
															 ';

		} else {
			$html .= '<tr>
															      <td width="50%"><strong>' . odbc_result($cur, 'Tipo_Venda') . '</strong></td>
															      <td width="25%" style="text-align:right"><strong>Valor ' . $extMoeda . '</strong></td>
															      <td width="25%" style="text-align:right"><strong>% </strong></td>	</tr>				  
															 ';
		}


		$i++;
	}

	if ($i == 1) {
		$html .= '<tr>
													    <td>' . odbc_result($cur, 'Descricao') . '</td>
													    <td style="text-align:right">' . number_format(odbc_result($cur, 'v_Valor'), 2, ',', '.') . '</td>
													    <td style="text-align:right">' . number_format((odbc_result($cur, 'v_Valor') / ($total1 != 0 ? $total1 : 1) * 100), 2, ',', '.') . '</td>                   
												       </tr>';
	} else if ($i == 2) {
		$html .= '<tr>
														<td>' . odbc_result($cur, 'Descricao') . '</td>
														<td style="text-align:right">' . number_format(odbc_result($cur, 'v_Valor'), 2, ',', '.') . '</td>
														<td style="text-align:right">' . number_format((odbc_result($cur, 'v_Valor') / ($total1 != 0 ? $total1 : 1) * 100), 2, ',', '.') . '</td>                   
												       </tr>';
	}

	$x++;
}

if ($i > 1) {

	$html .= '  <tr>
																 <td><strong>Total</strong></td>
																 <td style="text-align:right"><strong>' . number_format($total3, 2, ',', '.') . '</strong></td>
																 <td style="text-align:right"><strong>' . number_format($total4, 2, ',', '.') . '</strong></td>
															</tr>										
										           </table></div>';
}

odbc_free_result($cur);

$qry = "SELECT 
            VFLC.i_Venda_Faixa,
            CASE
                WHEN VFLC.v_Faixa_Inicial = 0 THEN 
                    'At&eacute ' + CAST(M.i_Moeda AS VARCHAR) + 'X ' + REPLACE(CAST(VFLC.v_Faixa_Final AS VARCHAR), '.', ',')
                WHEN VFLC.v_Faixa_Final = 0 THEN 
                    'Acima de ' + CAST(M.i_Moeda AS VARCHAR) + 'X ' + REPLACE(CAST(VFLC.v_Faixa_Inicial AS VARCHAR), '.', ',')
                ELSE 
                    'De ' + CAST(M.i_Moeda AS VARCHAR) + 'X ' + REPLACE(CAST(VFLC.v_Faixa_Inicial AS VARCHAR), '.', ',') + ' at&eacute ' + CAST(M.i_Moeda AS VARCHAR) + 'X ' + REPLACE(CAST(VFLC.v_Faixa_Final AS VARCHAR), '.', ',')
            END AS Nome_Campo,
            ISNULL(IVFLC.n_Clientes, 0) AS n_Clientes,
            VFLC.v_Faixa_Inicial,
            VFLC.v_Faixa_Final,
            ISNULL(IVFLC.v_Valor, 0) AS v_Valor,
            M.i_Moeda
        FROM 
            Venda_Faixa_Limite_Credito VFLC
        INNER JOIN Moeda M ON M.i_Moeda = ?
        LEFT JOIN Inform_Venda_Faixa_Limite_Credito IVFLC ON IVFLC.i_Venda_Faixa = VFLC.i_Venda_Faixa
            AND IVFLC.i_Inform = ?
        ORDER BY 
            VFLC.i_Venda_Faixa";

$cur = odbc_prepare($db, $qry);
odbc_execute($cur, [$nMoeda, $idInform]);


$html .= '<br><pagebreak />
									         <strong>1.6 DISTRIBUIÇÃO DE VENDAS A PRAZO POR FAIXA DE LIMITE DE CRÉDITO</strong>
										
										    <div class="rounded"> 
										     
											  <table width="100%" border="0" align="center" cellspacing="2"> 
										        <tr>
												   <td>Faixa de Valor</td>
												   <td  style="text-align:right"><strong>Clientes</strong></td>
												   <td  style="text-align:right"><strong>% Cliente</strong></td>
												   <td  style="text-align:right"><strong>Cont/Receber ' . $extMoeda . '</strong></td>
												   <td  style="text-align:right"><strong>% Volume</strong></td>
										       </tr>';

$count = 0;
while (odbc_fetch_row($cur)) {
	$count++;
}

$x = 1;
while (odbc_fetch_row($cur)) {
	$total1 += odbc_result($cur, 'n_Clientes');
	$total3 += odbc_result($cur, 'v_Valor');
	$x++;
}
$menor = 0;
$i = 0;
while (odbc_fetch_row($cur)) {


	$html .= '<tr>
															
																<td>';

	if ($i == 0) {
		$html .= 'At&eacute; ' . $extMoeda . ' ' . number_format(odbc_result($cur, 'v_Faixa_Final'), 2, ',', '.');
	} else if (($i + 1) < $count) {
		$html .= 'De ' . $extMoeda . ' ' . number_format(odbc_result($cur, 'v_Faixa_Inicial'), 2, ',', '.') . ' at&eacute; ' . $extMoeda . ' ' . number_format(odbc_result($cur, 'v_Faixa_Final'), 2, ',', '.');
	} else {
		$html .= 'Acima de ' . $extMoeda . ' ' . number_format((odbc_result($cur, 'v_Faixa_Inicial') + 1), 2, ',', '.');
	}

	$html .= '</td>
																    <td align="right" style="text-align:right">' . odbc_result($cur, 'n_Clientes') . '</td>
																    <td align="right" style="text-align:right">' . number_format((odbc_result($cur, 'n_Clientes') / ($total1 != 0 ? $total1 : 1)) * 100, 2, ',', '.') . '%' . '</td>
																    <td align="right" style="text-align:right">' . number_format(odbc_result($cur, 'v_Valor'), 2, ',', '.') . '</td>
																    <td align="right" style="text-align:right">' . number_format((odbc_result($cur, 'v_Valor') / ($total3 != 0 ? $total3 : 1)) * 100, 2, ',', '.') . '%' . '</td>
															      </tr>';

	$i++;
	// $total1 += odbc_result($cur,'n_Clientes');
	$total2 += (odbc_result($cur, 'n_Clientes') / ($total1 != 0 ? $total1 : 1)) * 100;
	// $total3 += odbc_result($cur,'v_Valor');
	$total4 += (odbc_result($cur, 'v_Valor') / ($total3 != 0 ? $total3 : 1)) * 100;
}

$html .= '
											<tr>
											   <td colspan="4"></td>
											</tr>
											   <tr>
												 <td scope="col">Total</td>
												 <td scope="col" style="text-align:right"><strong>' . $total1 . '</strong></td>
												 <td scope="col" style="text-align:right"><strong>' . number_format($total2, 2, ',', '.') . '%</strong></td>
												 <td scope="col" style="text-align:right"><strong>' . number_format($total3, 2, ',', '.') . '</strong></td>
												 <td scope="col" style="text-align:right"><strong>' . number_format($total4, 2, ',', '.') . '%</strong></td>            
												</tr>
										  
										</table>
										</div>
										
									<br>';


$html .= '<pagebreak />
								         <br>
										   <strong>1.7 DISTRIBUIÇÃO DE VENDAS A PRAZO POR PAÍS</strong>';
odbc_free_result($cur);

$qryx = "SELECT idInform, valExp, c.name, buyers, v.id 
         FROM VolumeSeg v 
         JOIN Country c ON (idCountry = c.id) 
         WHERE idInform = ? 
         ORDER BY name";

$curr = odbc_prepare($db, $qryx);
odbc_execute($curr, [$idInform]);

$ii = 0;
$total = 0;
$totComp = 0;
//print	 $qry; 
$html .= '<div class="rounded"> 
									           <table width="100%" border="0"  align="center" cellspacing="2">      
											  <tr>
												<td>&nbsp;</td>
												<td>Valor a ser Faturado ' . $extMoeda . '</td>
												<td>Pa&iacute;s</td>
												<td align="right">N&uacute;mero de Compradores </td>
												<td>&nbsp;</td>
											  </tr>
										  ';



while (odbc_fetch_row($curr)) {
	$total = $total + (odbc_result($curr, 'valExp'));
	$totComp = $totComp + (odbc_result($curr, 4));
	$ii++;

	$html .= '<tr>
												<td>&nbsp;</td>
												<td>' . number_format(odbc_result($curr, 2), 2, ",", ".") . '</td>
												<td>' . odbc_result($curr, 3) . '</td>
												<td align="right">' . odbc_result($curr, 4) . '</td>
												<td></td>
											  </tr>';
}
odbc_free_result($curr);
if ($ii == 0) {
	$html .= '<tr>
												  <td colspan="5">Nenhuma entrada cadastrada</td>
												</tr>';
} else {
	$html .= '<tr>
													<td><strong>Total</strong></td>
													<td>' . number_format($total, 2, ",", ".") . '</td>
													<td>&nbsp;</td>
													<td align="right">&nbsp;' . $totComp . '</td>
													<td>&nbsp;</td>
												  </tr>';
}

$html .= '
										   </table></div>
										   
										   
										   
					                <br><pagebreak />
									     <strong>1.8 RELAÇÃO DE CLIENTES PARA ANÁLISE PRELIMINAR – Buyer Study</strong>
									';


$qry = "SELECT imp.name, address, city, c.name, tel, prevExp12, numShip12,
									periodicity, przPag, imp.id, cep, fax, contact, relation, seasonal, 
									ISNULL(imp.divulgaNome, 0) AS divulgaNome, emailContato, cnpj
							 FROM Importer imp 
							 JOIN Country c ON (idCountry = c.id)
							 WHERE idInform = ? 
							   AND state <> ? 
							   AND state <> ? 
							   AND imp.id NOT IN (SELECT DISTINCT idImporter FROM ImporterRem)
							 ORDER BY imp.id";

$cur = odbc_prepare($db, $qry);
odbc_execute($cur, [$idInform, 7, 9]);


$i = $soma = 0;
$html .= '<div class="rounded"> 
                                            <table width="100%" border="0" align="center" cellspacing="2">                                             
											 <tr>
											  <td colspan="1">Buyers Inclu&iacute;dos</td>
											  <td colspan="1">&nbsp;</td>
											  <td colspan="1">&nbsp;</td>
											  <td colspan="1">&nbsp;</td>
											 </tr>
								  ';

while (odbc_fetch_row($cur)) {
	$soma += odbc_result($cur, 6);


	$i++;

	$html .= '<tr>		   
												    <td colspan="1"><strong>Raz&atilde;o Social:</strong></td>
													<td colspan="3">' . odbc_result($cur, 10) . ' ' . odbc_result($cur, 1) . '</td>
												</tr>
												<tr><td  colspan="1">	 
														 <strong>Registro fiscal do importador:</strong></td><td  colspan="3">
														 ' . odbc_result($cur, "cnpj") . '
												</td></tr>
												<tr><td colspan="1">
													 <strong>Endere&ccedil;o:</strong></td><td colspan="3">
													 ' . odbc_result($cur, 2) . '
												</td></tr>
											 
												<tr><td colspan="1"> 
													<strong>Cidade:</strong></td><td colspan="3">
													' . odbc_result($cur, 3) . '
												</td></tr>
												<tr><td colspan="1"> 
												   <strong>Pa&iacute;s:</strong></td><td colspan="3">
												   ' . odbc_result($cur, 4) . '
												 </td></tr>
												 
												 
												 <tr><td colspan="1"> 
													<strong>CEP:</strong></td><td colspan="3">
													' . odbc_result($cur, 11) . '
												 </td></tr>
												 
												 <tr><td colspan="1"> 
													 <strong>Tel:</strong></td><td colspan="3">
													 ' . odbc_result($cur, 5) . '
												 </td></tr>
												 
												 <tr><td colspan="1"> 
													<strong>FAX:</strong></td><td colspan="3">
													' . odbc_result($cur, 12) . '
												 </td></tr>
												 
												 <tr><td colspan="1"> 
													<strong>Contato:</strong></td><td colspan="3">
													' . odbc_result($cur, 13) . '
												 </td></tr>
												 
												 <tr><td colspan="1"> 
													<strong>E-mail:</strong></td><td colspan="3">
													' . odbc_result($cur, "emailContato") . '
												 </td></tr>
												 
												 <tr><td colspan="1"> 
													<strong>Rela&ccedil;&atilde;o Comercial:</strong></td><td colspan="3">
													 desde o ano de ' . odbc_result($cur, 14) . '
												 </td></tr>
												 <tr><td colspan="1"> 
													<strong>Vendas Sazonais:</strong></td><td colspan="3">
													' . (odbc_result($cur, 15) ? "Sim" : "Não") . '
												 </td></tr>
												 
												  <tr><td colspan="1"> 
													 <strong>Divulga nome ao importador:</strong></td><td colspan="3">
													 ' . (odbc_result($cur, "divulgaNome") == 1 ? "Sim" : "Não") . '
												  </td></tr>
												</td>                               
											  </tr>
											  
											  <tr>											 
												<Td align="right">Prev. Vol. Vendas</Td>
												<Td align="right">N&uacute;m. de Emb/Ano</Td>
												<Td align="right">Period. de Emb.</Td>
												<Td align="right">Prazo de Pagto.</Td>
												  
											  </tr>
												   
											  <tr>
												<TD align="right"><strong>' . number_format(odbc_result($cur, 6), 2, ",", ".") . '</strong></TD>
												<TD align="right"><strong>' . odbc_result($cur, 7) . '</strong></TD>
												<TD align="right"><strong>' . odbc_result($cur, 8) . '</strong></TD>
												<TD align="right"><strong>' . odbc_result($cur, 9) . '</strong></TD>
											
											  </TR>';

}

if ($i == 0) {
	$html .= '<TR>
                                                   <TD align="center" colspan=4>Nenhum buyer cadastrado</TD>
                                      </TR>';
}

$html .= '<BR><BR> 
                                
                                </TABLE></div>';




$html .= '<pagebreak />
									   
									   
									   <strong>1.9 DÍVIDAS VENCIDAS</strong>';
odbc_free_result($cur);
$qry = "SELECT Inf.id, DV.i_Dividas_Vencidas,
               CASE
                   WHEN DV.d_Periodo_Inicial = 0 THEN 'At&eacute; ' + CAST(DV.d_Periodo_Final AS VARCHAR) + ' dias'
                   WHEN DV.d_Periodo_Final = 0 THEN 'Acima de ' + CAST(DV.d_Periodo_Inicial AS VARCHAR) + ' dias'
                   ELSE 'De ' + CAST(DV.d_Periodo_Inicial AS VARCHAR) + ' at&eacute; ' + CAST(DV.d_Periodo_Final AS VARCHAR) + ' dias'
               END AS Descricao,
               ISNULL(IDV.v_Valor, 0) AS v_Valor
        FROM Inform Inf
        CROSS JOIN Dividas_Vencidas DV
        LEFT JOIN Inform_Dividas_Vencidas IDV 
               ON IDV.i_Dividas_Vencidas = DV.i_Dividas_Vencidas 
               AND IDV.i_Inform = Inf.id
        WHERE Inf.id = ? 
          AND DV.Situacao = 0
        ORDER BY DV.d_Periodo_Inicial";


$count1 = 0;
$count2 = 0;
$valida = 0;
$i = 0;

$cur = odbc_prepare($db, $qry);
odbc_execute($cur, [$idInform]);

while (odbc_fetch_row($cur)) {
	if ($valida != $tipovenda) {
		$tipovenda = $valida;

		$i++;
	}


	$total3 += odbc_result($cur, 'v_Valor');
	$total4 = 100;
	$count2++;



}

$i = 0;
$x = 0;
$tipovenda = "";
$valida = 3;
while (odbc_fetch_row($cur)) {
	if ($valida != $tipovenda) {
		$tipovenda = $valida;
		if ($i == 1) {
			$html .= ' </table></div>';

		}
		$html .= ' <div class="rounded1" style="width:325px; float: ' . ($i > 1 ? 'right' : 'left') . '; margin:0 20px 0 0;"> 										     
											                         <table width="100%" border="0" cellspacing="2" style="width:450px;">';


		if ($i < 3) {
			$html .= ' <tr>
																   <td width="50%">&nbsp;</td>
																   <td width="25%" style="text-align:right"><strong>Valor ' . ($extMoeda) . '</strong></td>
																   <td width="25%" style="text-align:right"><strong>% </strong></td>						  
																 </tr>';

		} else {
			$html .= ' <tr>
																   <td width="50%">&nbsp;</td>
																   <td width="25%" style="text-align:right"><strong>Valor ' . ($extMoeda) . '</strong></td>
																   <td width="25%" style="text-align:right"><strong>% </strong></td>						  
																 </tr>';

		}


		$i++;
	}

	if ($i == 1) {
		$html .= '<tr>
														<td>' . odbc_result($cur, 'Descricao') . '</td>
														<td>' . number_format(odbc_result($cur, 'v_Valor'), 2, ',', '.') . '</td>
														<td>' . number_format((odbc_result($cur, 'v_Valor') / ($total3 != 0 ? $total3 : 1) * 100), 2, ',', '.') . '</td>                   
												   </tr>';
	} else if ($i == 2) {
		$html .= '<tr>
														<td>' . odbc_result($cur, 'Descricao') . '</td>
														<td>' . number_format(odbc_result($cur, 'v_Valor'), 2, ',', '.') . '</td>
														<td>' . number_format((odbc_result($cur, 'v_Valor') / ($total3 != 0 ? $total3 : 1) * 100), 2, ',', '.') . '</td>                   
												   </tr>';
	}

	if ($x == 2) {
		$valida++;
	}

	$x++;

}

if ($i > 1) {

	$html .= '<tr>
														     <td><strong>Total</strong></td>
														     <td style="text-align:right"><strong>' . number_format($total3, 2, ',', '.') . '</strong></td>
														     <td style="text-align:right"><strong>' . number_format($total4, 2, ',', '.') . '</strong></td>
														   </tr>											
											              </table></div>';
}
$html .= '';








$html .= '
									<BR>
									<BR><pagebreak />
								    <strong>1.10 HISTÓRICO DE PERDAS</strong>';



$query = "SELECT * FROM Lost WHERE idInform = ?";
$cur = odbc_prepare($db, $query);
odbc_execute($cur, [$idInform]);
if (odbc_fetch_row($cur)) {
	$declaracao = odbc_result($cur, "declara_sem_perda");
	if ($declaracao) {
		$html .= '<strong>Declaro que não houve perda</strong>';
	}

	$html .= '<div style="clear:both">&nbsp;</div>
														
														  <div class="rounded"> 
														  <table width="100%" border="0" align="center" cellspacing="2"> 
																								
															 <tr>
																<td>&nbsp;</td>
																<td align="right">Ano Corrente</td>
																<td align="right">Ano Passado</td>
																<td align="right">Ano Retrasado</td>
															  </tr>
															
															  <tr>
																<td>Soma Total das Perdas ' . $extMoeda . '</td>
																<td align="right">' . number_format(odbc_result($cur, "val1") ?? 0, 2, ",", ".") . '</td>
																<td align="right">' . number_format(odbc_result($cur, "val2") ?? 0, 2, ",", ".") . '</td>
																<td align="right">' . number_format(odbc_result($cur, "val3") ?? 0, 2, ",", ".") . '</td>
															  </tr>
															  <tr>
																<td>N&uacute;mero de Perdas</td>
																<td align="right">' . odbc_result($cur, "num1") . '</td>
																<td align="right">' . odbc_result($cur, "num2") . '</td>
																<td align="right">' . odbc_result($cur, "num3") . '</td>
															  </tr>
															
															  <tr>
																<td colspan="4">
																   &nbsp;
																</td></tr>			
															   <tr>
																<td colspan="4">
																   <strong>Detalhamento das Maiores Perdas</strong>
															   </td></tr>
														
															  <tr>
																<td>Ano</td>
																<td>Pa&iacute;s</td>
																<td>Raz&atilde;o Social do Inadimplente</td>
																<td>Valor Final da Perda - ' . $extMoeda . '</td>
															  </tr>
														 
															  <tr>
																<td rowspan="3">Corrente</td>
																<td>' . odbc_result($cur, "country1") . '</td>
																<td>' . odbc_result($cur, "name1") . '</td>
																<td align="right">' . number_format(odbc_result($cur, "lost1") ?? 0, 2, ",", ".") . '</td>
															  </tr>
															  <tr>
																<td>' . odbc_result($cur, "country2") . '</td>
																<td>' . odbc_result($cur, "name2") . '</td>
																<td align="right">' . number_format(odbc_result($cur, "lost2") ?? 0, 2, ",", ".") . '</td>
															  </tr>
															  <tr>
																<td>' . odbc_result($cur, "country3") . '</td>
																<td>' . odbc_result($cur, "name3") . '</td>
																<td align="right">' . number_format(odbc_result($cur, "lost3") ?? 0, 2, ",", ".") . '</td>
															  </tr>
															  <tr>
																<td rowspan="3">Passado</TD>
																<td>' . odbc_result($cur, "country4") . '</td>
																<td>' . odbc_result($cur, "name4") . '</td>
																<td align="right">' . number_format(odbc_result($cur, "lost4") ?? 0, 2, ",", ".") . '</td>
															  </tr>
															  <tr>
																<td>' . odbc_result($cur, "country5") . '</td>
																<td>' . odbc_result($cur, "name5") . '</TD>
																<td align="right">' . number_format(odbc_result($cur, "lost5") ?? 0, 2, ",", ".") . '</td>
															  </tr>
															  <tr>
																<td>' . odbc_result($cur, "country6") . '</td>
																<td>' . odbc_result($cur, "name6") . '</td>
																<td align="right">' . number_format(odbc_result($cur, "lost6") ?? 0, 2, ",", ".") . '</td>
															  </tr>
															  <tr>
																<td rowspan="3">Retrasado</td>
																<td>' . odbc_result($cur, "country7") . '</td>
																<td>' . odbc_result($cur, "name7") . '</td>
																<td align="right">' . number_format(odbc_result($cur, "lost7") ?? 0, 2, ",", ".") . '</TD>
															  </tr>
															  <tr>
																<td>' . odbc_result($cur, "country8") . '</td>
																<td>' . odbc_result($cur, "name8") . '</td>
																<td align="right">' . number_format(odbc_result($cur, "lost8") ?? 0, 2, ",", ".") . '</td>
															  </tr>
															  <tr>
																<td>' . odbc_result($cur, "country9") . '</td>
																<td>' . odbc_result($cur, "name9") . '</td>
																<td align="right">' . number_format(odbc_result($cur, "lost9") ?? 0, 2, ",", ".") . '</td>
															  </tr>
															
														</table>
														</div>';

}
odbc_free_result($cur);
$qry = "SELECT 
            DPE.i_Perda_Efetiva,
            M.Sigla AS SiglaMoeda,
            CASE
                WHEN DPE.v_Faixa_Inicial = 0 THEN 'At&eacute ' + CAST(M.Sigla AS VARCHAR) + ' ' + REPLACE(CAST(DPE.v_Faixa_Final AS VARCHAR), '.', ',')
                WHEN DPE.v_Faixa_Final = 0 THEN 'Acima de ' + CAST(M.Sigla AS VARCHAR) + ' ' + REPLACE(CAST(DPE.v_Faixa_Inicial AS VARCHAR), '.', ',')
                ELSE 'De ' + CAST(M.Sigla AS VARCHAR) + ' ' + REPLACE(CAST(DPE.v_Faixa_Inicial AS VARCHAR), '.', ',') + ' at&eacute ' + CAST(M.Sigla AS VARCHAR) + ' ' + REPLACE(CAST(DPE.v_Faixa_Final AS VARCHAR), '.', ',')
            END AS Nome_Campo,
            DPE.v_Faixa_Inicial,
            DPE.v_Faixa_Final,
            Ano1.Ano1,
            ISNULL(IDPE1.n_Clientes, 0) AS Clientes1,
            ISNULL(IDPE1.v_Valor, 0) AS Valor1,
            Ano2.Ano2,
            ISNULL(IDPE2.n_Clientes, 0) AS Clientes2,
            ISNULL(IDPE2.v_Valor, 0) AS Valor2,
            Ano3.Ano3,
            ISNULL(IDPE3.n_Clientes, 0) AS Clientes3,
            ISNULL(IDPE3.v_Valor, 0) AS Valor3,
            Ano4.Ano4,
            ISNULL(IDPE4.n_Clientes, 0) AS Clientes4,
            ISNULL(IDPE4.v_Valor, 0) AS Valor4
        FROM 
            Detalhamento_Perda_Efetiva DPE
        INNER JOIN Moeda M ON M.i_Moeda = ?
        CROSS JOIN (SELECT YEAR(GETDATE()) AS Ano1) Ano1
        LEFT JOIN Inform_Detalhamento_Perda_Efetiva IDPE1 ON 
            IDPE1.i_Perda_Efetiva = DPE.i_Perda_Efetiva 
            AND IDPE1.Ano = Ano1.Ano1
            AND IDPE1.i_Inform = ?
        CROSS JOIN (SELECT YEAR(GETDATE()) - 1 AS Ano2) Ano2
        LEFT JOIN Inform_Detalhamento_Perda_Efetiva IDPE2 ON 
            IDPE2.i_Perda_Efetiva = DPE.i_Perda_Efetiva 
            AND IDPE2.Ano = Ano2.Ano2
            AND IDPE2.i_Inform = ?
        CROSS JOIN (SELECT YEAR(GETDATE()) - 2 AS Ano3) Ano3
        LEFT JOIN Inform_Detalhamento_Perda_Efetiva IDPE3 ON 
            IDPE3.i_Perda_Efetiva = DPE.i_Perda_Efetiva 
            AND IDPE3.Ano = Ano3.Ano3
            AND IDPE3.i_Inform = ?
        CROSS JOIN (SELECT YEAR(GETDATE()) - 3 AS Ano4) Ano4
        LEFT JOIN Inform_Detalhamento_Perda_Efetiva IDPE4 ON 
            IDPE4.i_Perda_Efetiva = DPE.i_Perda_Efetiva 
            AND IDPE4.Ano = Ano4.Ano4
            AND IDPE4.i_Inform = ?
        ORDER BY 
            DPE.v_Faixa_Inicial";

$cur = odbc_prepare($db, $qry);
odbc_execute($cur, [$nMoeda, $idInform, $idInform, $idInform, $idInform]);



$html .= '<pagebreak /><strong>1.11 DETALHAMENTO DAS PERDAS EFETIVAS POR FAIXA DE VALOR</strong>
									         <div class="rounded"> 										     
											  <table width="100%" border="0" align="center" cellspacing="2"> ';

$count = 0;
while (odbc_fetch_row($cur)) {
	$count++;
}

$i = 0;
$total1 = 0;
$total2 = 0;
$total3 = 0;
$total5 = 0;
$total6 = 0;
$total7 = 0;
$total8 = 0;

while (odbc_fetch_row($cur)) {
	if ($i == 0) {
		$html .= '
														  <tr>
															<td width="36%">&nbsp;</td>
															<td width="16%" colspan="2" style="text-align:center"><strong>' . odbc_result($cur, 'Ano1') . '</strong>															
															</td>															
															<td width="16%" colspan="2" style="text-align:center"><strong>' . odbc_result($cur, 'Ano2') . '</strong>														  
															</td>														   
															<td width="16%" colspan="2" style="text-align:center"><strong>' . odbc_result($cur, 'Ano3') . '</strong>														  
															</td>														   
															<td width="16%" colspan="2" style="text-align:center"><strong>' . odbc_result($cur, 'Ano4') . '</strong>														 
															</td>														   
														  </tr>
														  <tr>
														   <td width="36%" ><strong>Faixa de Valor ' . odbc_result($cur, 'SiglaMoeda') . '</strong></td>
														   <td  style="text-align:center" width="8%"><strong>Clientes</strong></td>
														   <td  style="text-align:center" width="8%"><strong>Valor</strong></td>
														   <td  style="text-align:center" width="8%"><strong>Clientes</strong></td>
														   <td  style="text-align:center" width="8%"><strong>Valor</strong></td>
														   <td  style="text-align:center" width="8%"><strong>Clientes</strong></td>
														   <td  style="text-align:center" width="8%"><strong>Valor</strong></td>
														   <td  style="text-align:center" width="8%"><strong>Clientes</strong></td>
														   <td  style="text-align:center" width="8%"><strong>Valor</strong></td>
														   <tr>';


	}


	$html .= '
													 <tr>                        
														<td>';

	if ($i == 0) {
		$html .= 'At&eacute; ' . $extMoeda . ' ' . number_format(odbc_result($cur, 'v_Faixa_Final'), 2, ',', '.');
	} else if (($i + 1) < $count) {
		$html .= 'De ' . $extMoeda . ' ' . number_format(odbc_result($cur, 'v_Faixa_Inicial'), 2, ',', '.') . ' at&eacute; ' . $extMoeda . ' ' . number_format(odbc_result($cur, 'v_Faixa_Final'), 2, ',', '.');
	} else {
		$html .= 'Acima de ' . $extMoeda . ' ' . number_format((odbc_result($cur, 'v_Faixa_Inicial')), 2, ',', '.');
	}

	$html .= '
                                                        </td>
														<td style="text-align:right">' . number_format(odbc_result($cur, 'Clientes1'), 0, ',', '.') . '</td>
														<td style="text-align:right">' . number_format(odbc_result($cur, 'Valor1'), 2, ',', '.') . '</td>
														<td style="text-align:right">' . number_format(odbc_result($cur, 'Clientes2'), 0, ',', '.') . '</td>
														<td style="text-align:right">' . number_format(odbc_result($cur, 'Valor2'), 2, ',', '.') . '</td>
														<td style="text-align:right">' . number_format(odbc_result($cur, 'Clientes3'), 0, ',', '.') . '</td>
														<td style="text-align:right">' . number_format(odbc_result($cur, 'Valor3'), 2, ',', '.') . '</td>
														<td style="text-align:right">' . number_format(odbc_result($cur, 'Clientes4'), 0, ',', '.') . '</td>
														<td style="text-align:right">' . number_format(odbc_result($cur, 'Valor4'), 2, ',', '.') . '</td>
												   	</tr>';

	$i++;
	$total1 += odbc_result($cur, 'clientes1');
	$total2 += odbc_result($cur, 'valor1');
	$total3 += odbc_result($cur, 'clientes2');
	$total4 += odbc_result($cur, 'valor2');
	$total5 += odbc_result($cur, 'clientes3');
	$total6 += odbc_result($cur, 'valor3');
	$total7 += odbc_result($cur, 'clientes4');
	$total8 += odbc_result($cur, 'valor4');

}

$html .= '	  
									   <tr>
									      <td colapan="9"></td>
									   </tr>
									   <tr>
										 <td scope="col"><strong>Total</strong></td>
										 <td scope="col" style="text-align:right"><strong>' . number_format($total1, 0, ',', '.') . '</strong></td>
										 <td scope="col" style="text-align:right"><strong>' . number_format($total2, 2, ',', '.') . '</strong></td>
										 <td scope="col" style="text-align:right"><strong>' . number_format($total3, 0, ',', '.') . '</strong></td>
										 <td scope="col" style="text-align:right"><strong>' . number_format($total4, 2, ',', '.') . '</strong></td>            
										 <td scope="col" style="text-align:right"><strong>' . number_format($total5, 0, ',', '.') . '</strong></td>
										 <td scope="col" style="text-align:right"><strong>' . number_format($total6, 2, ',', '.') . '</strong></td>
										 <td scope="col" style="text-align:right"><strong>' . number_format($total7, 0, ',', '.') . '</strong></td>
										 <td scope="col" style="text-align:right"><strong>' . number_format($total8, 2, ',', '.') . '</strong></td>  
										</tr>
								  
								</table></div>';





$html .= '
									   <pagebreak />
									<strong>CONFIDENCIALIDADE</strong>
										<div id="cobtexto">
										   As informações prestadas neste questionário autorizam a elaboração de propostas de 
										   seguros e serão tratadas com confidencialidade. Não constituem qualquer compromisso de subscrição de apólice. 
										   Caso haja a contratação do seguro, o presente questionário será parte integrante da apólice.															
																									
										</div>		
										
										<div style="clear:both">&nbsp;</div>
										<div style="clear:both">&nbsp;</div>
										<div style="clear:both">&nbsp;</div>
										<strong>S&atilde;o Paulo </strong>' . dataconvert(date('d/m/Y')) . '';

$html .= ' 
					    
					</html>';


odbc_free_result($cur);
$html = mb_convert_encoding($html, 'UTF-8', 'ISO-8859-1');
$mpdf->allow_charset_conversion = true;
$mpdf->charset_in = 'UTF-8';
$mpdf->WriteHTML($html);

$mpdf->Output();



// echo utf8_decode($html);



//$mpdf->Output($pdfDir.$key."informe.pdf",F); 

// $url_pdf = $host.'src/download/'.$key."informe.pdf";

?>


<!--
 
</body>
</html>

-->