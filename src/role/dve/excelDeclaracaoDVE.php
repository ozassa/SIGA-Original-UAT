<?php
header('Content-type: application/msexcel');
header('Content-Disposition: attachment; filename="declaracao_dve.xls"');

require_once("../../dbOpen.php");

$Id_Periodo = isset($_REQUEST['Id_Periodo']) ? $_REQUEST['Id_Periodo'] : false;

$sqlProc = "EXEC SPR_BB_Exporta_Declaracao_TOD ?";
$rsSqlProc = odbc_prepare($db, $sqlProc);
odbc_execute($rsSqlProc, [$Id_Periodo]);


$html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
						<head>
							<meta http-equiv=Content-Type content="text/html;charset=windows-1252">
							<meta name=ProgId content=Excel.Sheet>
							<meta name=Generator content="Microsoft Excel 11">
							<style>
								.texto {font-family: "arial"; font-size : 13px;}
							</style>
							<!--[if gte mso 9]>
							<xml>
								<x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>
								<x:Name>Sheet1</x:Name>
								<x:WorksheetOptions><x:Panes>
								</x:Panes></x:WorksheetOptions>
								</x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook>
							</xml>
							<![endif]-->
						</head>

						<body>

						<table width=\'100%\' cellspacing=\'1\' cellpadding=\'1\'>
							<tr>
								<td class=\'texto\' width=\'60\'>Country</td>
								<td class=\'texto\' width=\'500\'>Company name</td>
								<td class=\'texto\' width=\'120\'>EasyNumber</td>
								<td class=\'texto\' width=\'110\'>Coface reference</td>
								<td class=\'texto\' width=\'160\'>Legal id</td>
								<td class=\'texto\' width=\'160\'>Customer reference</td>
								<td class=\'texto\' width=\'60\'>Currency</td>
								<td class=\'texto\' width=\'60\'>Amount</td>
							</tr>';

if ($rsSqlProc) {
	while (odbc_fetch_row($rsSqlProc)) {
		$Amount = odbc_result($rsSqlProc, "Amount");
		$Amount = str_replace('.', '', $Amount);
		$Amount = str_replace(',', '.', $Amount);

		$html .= '<tr>';
		$html .= '	<td class=\'texto\' align=\'left\'>' . odbc_result($rsSqlProc, "Country") . '</td>';
		$html .= '	<td class=\'texto\' align=\'left\'>' . odbc_result($rsSqlProc, "Company_name") . '</td>';
		$html .= ' 	<td class=\'texto\' align=\'left\'>' . odbc_result($rsSqlProc, "EasyNumber") . '</td>';
		$html .= ' 	<td class=\'texto\' align=\'left\'>' . odbc_result($rsSqlProc, "Coface_reference") . '</td>';
		$html .= ' 	<td class=\'texto\' align=\'left\'>' . odbc_result($rsSqlProc, "Legal_id") . '</td>';
		$html .= ' 	<td class=\'texto\' align=\'left\'>' . odbc_result($rsSqlProc, "Customer_reference") . '</td>';
		$html .= ' 	<td class=\'texto\' align=\'left\'>' . odbc_result($rsSqlProc, "Currency") . '</td>';
		$html .= ' 	<td class=\'texto\' align=\'left\'>' . $Amount . '</td>';
		$html .= '</tr>';
	}
}

$html .= '
					    </body>
					  </table>';

echo $html;
?>