<?php
// header("Content-Type: text/html; charset=ISO-8859-1");
session_start();

require_once("../../dbOpen.php");
$idInform = $_REQUEST['idInform'];

$sql = "SELECT CAST(proposta AS TEXT) AS proposta FROM InformPdf WHERE idInform = ?";
$re = odbc_prepare($db, $sql);
odbc_execute($re, [$idInform]);
odbc_free_resource($re);
//print $sql;	
// echo odbc_result($re,'proposta'); 
//echo '<br>'.strlen(odbc_result($re,'proposta'));
?>


<?php

function formata_string($modo, $doc)
{
	if ($modo == "CPF") {
		if ($doc == "" || $doc == null)
			return "000.000.000-00";
		$doc = ereg_replace("[^0-9]", "", $doc);
		$doc = str_pad($doc, 11, 0, STR_PAD_LEFT);
		if (strlen($doc) == 11)
			return $doc[0] . $doc[1] . $doc[2] . "." . $doc[3] . $doc[4] . $doc[5] . "." . $doc[6] . $doc[7] . $doc[8] . "-" . $doc[9] . $doc[10];
		else
			return false;
	}

	if ($modo == "CNPJ") {
		if ($doc == "" || $doc == null)
			return "00.000.000/00000-00";
		$doc = ereg_replace("[^0-9]", "", $doc);
		$doc = str_pad($doc, 14, 0, STR_PAD_LEFT);
		if (strlen($doc) == 14)
			return $doc[0] . $doc[1] . "." . $doc[2] . $doc[3] . $doc[4] . "." . $doc[5] . $doc[6] . $doc[7] . '/' . $doc[8] . $doc[9] . $doc[10] . $doc[11] . "-" . $doc[12] . $doc[13];
		else
			return false;
	}


	if ($modo == "IE") {
		if ($doc == "" || $doc == null)
			return "000.000.000.000";
		$doc = ereg_replace("[^0-9]", "", $doc);
		$doc = str_pad($doc, 12, 0, STR_PAD_LEFT);
		if (strlen($doc) == 12)
			return $doc[0] . $doc[1] . $doc[2] . "." . $doc[3] . $doc[4] . $doc[5] . "." . $doc[6] . $doc[7] . $doc[8] . "." . $doc[9] . $doc[10] . $doc[11];
		else
			return false;
	}

	if ($modo == "TEL") {
		if ($doc == "" || $doc == null)
			return "(00) 0000-0000";
		$doc = ereg_replace("[^0-9]", "", $doc);
		$doc = str_pad($doc, 10, 0, STR_PAD_LEFT);
		if (strlen($doc) == 10)
			return "(" . $doc[0] . $doc[1] . ") " . $doc[2] . $doc[3] . $doc[4] . $doc[5] . "-" . $doc[6] . $doc[7] . $doc[8] . $doc[9];
		else
			return false;
	}

	if ($modo == "CEP") {
		if ($doc == "" || $doc == null)
			return "00000-000";
		$doc = ereg_replace("[^0-9]", "", $doc);
		$doc = str_pad($doc, 8, 0, STR_PAD_LEFT);
		if (strlen($doc) == 8)
			return $doc[0] . $doc[1] . $doc[2] . $doc[3] . $doc[4] . '-' . $doc[5] . $doc[6] . $doc[7];
		else
			return false;
	}


}

$_SESSION['Configurar'] = '';

// Emitir pdf pelo PHP diretamente

include_once("../../../gerar_pdf/MPDF45/mpdf.php");

$sqlquery = "SELECT E.*, P.Nome AS Produto, SP.Descricao AS SubProduto, SP.c_SUSEP, Inf.i_Gerente  
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
    WHERE
        Inf.id = ?";

$res = odbc_prepare($db, $sqlquery);
odbc_execute($res, [$idInform]);

$dados = odbc_fetch_array($res);

$i_Gerente = $dados['i_Gerente'];

$retorno_rodape = $dados['Endereco'] . ' - ' .
	$dados['Complemento'] . ' - ' .
	'CEP ' . formata_string('CEP', $dados['CEP']) . ' - ' .
	$dados['Cidade'] . ', ' .
	$dados['Estado'] . ' ' .
	'Tel.: ' . $dados['Telefone'] . '  ' .
	'Fax: ' . $dados['Fax'] . '  ' .
	'Home Page: ' . $dados['HomePage'];

$disclame_retorno = $dados['Nome'] . ' CNPJ: ' . formata_string('CNPJ', $dados['CNPJ']) . ', SUSEP no.: ' . $dados['c_SUSEP'];

$mpdf = new mPDF('win-1252', 'A4', '', '', 20, 15, 48, 25, 30, 30);
$html = ob_get_clean();
$mpdf->useOnlyCoreFonts = true;    // false is default
//$mpdf->SetProtection(array('print'));
$mpdf->SetTitle("Proposta");
$mpdf->SetAuthor($dados['Nome']);
$mpdf->SetWatermarkText(""); // fundo marca dágua
$mpdf->showWatermarkText = true;
$mpdf->watermark_font = 'DejaVuSansCondensed';
$mpdf->watermarkTextAlpha = 0.1;
$mpdf->SetDisplayMode('fullpage');

// Endereço do logotipo
$logo = '../../images/logo_pdf.jpg';
$logo_mini = '../../images/logo_mini.jpg';

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
			
							
							
							#cobtexto  {font-family: Arial, Helvetica, sans-serif; font-size:12pt; text-align:justify;}
							#sublinhado {font-family: Arial, Helvetica, sans-serif; font-size:12pt; text-align:justify; font-weight:bold; text-decoration:underline;}
							#disclame  {font-family: Arial, Helvetica, sans-serif; font-size:7pt; text-align:right;}
							
							#img1{
								width: 660px;
								height: 65px;
			
							}';



for ($i = 2; $i <= 100; $i++) {

	$html .= '
												#img' . $i . '{
													width: 660px;
													height: 65px;
													
												}';



}


$html .= '
							</style>
							
			</head>';

odbc_free_result($res).
$html = mb_convert_encoding($html . $_POST['textoCorpo'], 'UTF-8', 'ISO-8859-1');
$mpdf->allow_charset_conversion = true;
$mpdf->charset_in = 'UTF-8';
$mpdf->WriteHTML($html);

//print $html;



$mpdf->Output($_REQUEST['file_alterado'] . '.pdf', F);

$mpdf->Output();


?>