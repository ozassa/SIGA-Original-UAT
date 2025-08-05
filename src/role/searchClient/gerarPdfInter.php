<?php

include_once("../../../gerar_pdf/MPDF45/mpdf.php");

$mpdf=new mPDF('win-1252','A4','','',20,15,10,25,5,10); 
$html = ob_get_clean();
$mpdf->useOnlyCoreFonts = true;    // false is default
//$mpdf->SetProtection(array('print'));
$mpdf->SetTitle("Teste");
$mpdf->SetAuthor($nomeEmp);
$mpdf->SetWatermarkText(""); // fundo marca dágua
$mpdf->showWatermarkText = true;
$mpdf->watermark_font = 'DejaVuSansCondensed';
$mpdf->watermarkTextAlpha = 0.1;
$mpdf->SetDisplayMode('fullpage');

$pdf_conteudo = $_POST["conteudo_impressao"];

$html = '
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>teste</title>
	<style>
		thead th {
			font: bold 13px Arial, "Trebuchet MS", Verdana, Tahoma, Helvetica;
			border: 1px solid #888888;
			text-align: left;
			background: #999999;
			color: #FFFFFF;
			padding: 8px;
		}
		caption {
			text-align: left;
			font: normal 22px Arial, "Trebuchet MS", Verdana, Tahoma, Helvetica;
			background: transparent;
			padding: 6px 4px 8px 0px;
			color: #777777;
		}
		table {
			border-collapse: collapse;
			width: 100%;
			border: 1px solid #cccccc;
			font: normal 12px Arial, "Trebuchet MS", Verdana, Tahoma, Helvetica;
			color: #777777;
			background: #eeeeee;
		}

		tbody td {
			vertical-align: top;
			text-align: left;
			background: #FFFFFF;
		}

		td, th {
			border: 1px dotted #cccccc;
			padding: 8px;
			color: #777777;
		}

		p {
			font-family: Arial, "Trebuchet MS", Verdana, Tahoma, Helvetica;
			font-size: 12px;
			color: #777777;
			line-height: 18px;
			margin-bottom: 15px;
		}

		h1 {
			font-family: Arial, "Trebuchet MS", Verdana, Tahoma, Helvetica;
			font-size: 30px;
			color: #777777;
			line-height: 100%;
			font-weight: normal;
			margin-bottom: 10px;
		}


	</style>
</head>
<body>
	'.$pdf_conteudo.'
</body>
</html>';

// echo $html;

$html = utf8_encode($html);
$mpdf->allow_charset_conversion=true;
$mpdf->charset_in='UTF-8';
$mpdf->WriteHTML($html);
$mpdf->Output();


?>


