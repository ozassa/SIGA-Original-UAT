<?php

function retira_acentos($texto) {
	$array1 = array( "á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç" ,
					"Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç" );
	$array2 = array( "a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c" ,
					"A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C" );
	return str_replace( $array1, $array2, $texto);
}

require_once("../../../dbOpen.php");
require_once("../../../pdfConf.php");
include_once("../../../../gerar_pdf/MPDF45/mpdf.php");

	// verifica post
if(isset($_REQUEST['idInform'])) {
	$idInform  = $_REQUEST['idInform'];
}
if($_REQUEST['mes'] != ''){
	$mes = $_REQUEST['mes'];
}else{
	$mes = date("n");
}

if($_REQUEST['ano']){
	$ano = $_REQUEST['ano'];
}else{
	$ano = date("Y");
}

// fim verifica post
//$rsquery = "select imp.idImporter,imp.importador,imp.creditoSolicitado,imp.creditoConcedido,imp.txAnalise,imp.txMonitor,imp.total,imp.motivo,c.name, c.code, 
//			cast(imp.codPais as varchar) + imp.codBuyer  As CRS
//			from resFatAnaliseMonitorImport as imp 
//			Left join Importer as imp2 on imp.idImporter = imp2.id
//			Left Join Country as c on c.code = imp.codPais
//			where imp.idInform = $idInform and imp.ano=$ano and imp.mes=$mes order by imp.importador";
			
$rsquery = "SELECT UPPER(Inf.name) AS Segurado, 
                   CAST(FAMI.codPais AS VARCHAR) + FAMI.codBuyer AS CRS, 
                   AMC.CNPJ AS CNPJ, 
                   FAMI.importador AS Comprador, 
                   C.name AS Pais, 
                   FAMI.creditoSolicitado AS Credit_Solic, 
                   FAMI.creditoConcedido AS Credit_Conc, 
                   FAMI.total AS Valor_Total
            FROM Inform Inf
            INNER JOIN resFatAnaliseMonitorImport FAMI ON FAMI.idInform = Inf.id
            LEFT JOIN Country C ON C.code = FAMI.codPais
            LEFT JOIN (
                SELECT MAX(id) AS id, CodContrat, CodPais, CodBuyer 
                FROM AnaliseMonitorCapri 
                GROUP BY CodContrat, CodPais, CodBuyer
            ) AMCC ON AMCC.CodContrat = Inf.contrat
                   AND AMCC.CodPais = FAMI.codPais
                   AND AMCC.CodBuyer = FAMI.codBuyer
            LEFT JOIN AnaliseMonitorCapri AMC ON AMC.id = AMCC.id
            WHERE Inf.id = ? AND FAMI.ano = ? AND FAMI.mes = ?
            ORDER BY FAMI.importador";

$stmt = odbc_prepare($db, $rsquery);
odbc_execute($stmt, [$idInform, $ano, $mes]);

$cur = $stmt;

odbc_free_result($stmt);

$html = '';
$html .= '<table width="100%" style="border:solid 1px #000;" cellspacing="0" cellpadding="3">
				<tr>
					<td width="7%" style="border:solid 1px #000; text-align:center; font-size:15px;"><strong>CRS</strong></td>
					<td width="35%" style="border:solid 1px #000; text-align:left; font-size:15px;"><strong>Comprador</strong></td>
					<td width="10%" style="border:solid 1px #000; text-align:left; font-size:15px;"><strong>CNPJ</strong></td>
					<td width="25%" style="border:solid 1px #000; text-align:left; font-size:15px;"><strong>Pa&iacute;s</strong></td>
					<td width="10%" style="border:solid 1px #000; text-align:left; font-size:15px;"><strong>Cr&eacute;dito Solicitado</strong></td>
					<td width="10%" style="border:solid 1px #000; text-align:left; font-size:15px;"><strong>Cr&eacute;dito Concedido</strong></td>
					<td width="10%" style="border:solid 1px #000; text-align:center; font-size:15px;"><strong>Valor de An&aacute;lise e Monitoramento</strong></td>
				</tr>';

$totalCobrado = 0;

while (odbc_fetch_row($cur)) {
	$nomeSegurado 	= odbc_result($cur, "Segurado");
	$ci_coface   	= odbc_result($cur, "CRS");
	$CNPJ 			= odbc_result($cur, "CNPJ");
	$countryName 	= retira_acentos(odbc_result($cur,"Pais"));
	$importador 	= retira_acentos(odbc_result($cur,"Comprador"));
	$creditSolic 	= odbc_result($cur,"Credit_Solic");
	$credit 		= odbc_result($cur,"Credit_Conc");
	$total 			= odbc_result($cur,"Valor_Total");
	
	//$analyse = odbc_result($cur,5);
	//$monitor = odbc_result($cur,6);
	//$mot = odbc_result($cur,8);
	//$codPais   		= odbc_result($cur, "code");
	
	$totalCobrado +=$total; 
	//$totA +=$analyse;
	//$totM +=$monitor;
	//$totAn = $monitor + $analyse;

	$html .= '<tr>
	<td style="border:solid 1px #000; font-size:15px; text-align:center;">'. $ci_coface .'</td>
	<td style="border:solid 1px #000; font-size:15px; text-align:left;">'. $importador .'</td>
	<td style="border:solid 1px #000; font-size:15px; text-align:left;">'. $CNPJ.'</td>
	<td style="border:solid 1px #000; font-size:15px; text-align:left;">'. $countryName.'</td>
	<td style="border:solid 1px #000; font-size:15px; text-align:center;">'.number_format($creditSolic, 2, ',', '.').'</td>
	<td style="border:solid 1px #000; font-size:15px; text-align:center;">'.number_format($credit, 2, ',', '.').'</td>
	<td style="border:solid 1px #000; font-size:15px; text-align:center;">'.number_format($total, 2, ',', '.').'</td>
	</tr>';

} // while

	$html .= '
	<tr>
	<td  style="border:solid 1px #000; font-size:15px;" colspan="6"><strong>Total por segurado:</strong></td>
	<td  style="border:solid 1px #000; font-size:15px; text-align:center">'. number_format($totalCobrado, 2, ',', '.').'</td>
	</tr> </table>';

	$nome_arquivo = $ano.$mes."_".str_replace(" ", "_", retira_acentos($nomeSegurado));

	header("Content-Type: application/vnd.ms-excel; charset=iso-8859-1");
	header("Content-Disposition: attachment; filename=".$nome_arquivo.".xls");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	echo utf8_encode($html);
	?>