<?php  header( 'Content-type: application/msexcel');
header('Content-Disposition: attachment; filename="RelPagBonus.xls"');

require_once("../../dbOpen.php");


function TrataData($data, $tipo, $saida){

	#
	# Variavel $data é a String que contém a Data em qualquer formato
	# Variavel $tipo é que contém o tipo de formato data.
	# $tipo :
	#		1 - Brasil - No formato -> Dia/Mes/Ano ou DD/MM/YYYY
	#		2 - USA	 - No formato -> YYYY-Mes-Dia ou YYYY-MM-DD
	#
	# $saida :
	# 	    1 - Brasil
	# 	    2 - USA
	#
	# Obs
	# Esta função não funciona com timestemp no formato a seguir :
	# DD/MM/YYYY H:M:S.MS ou YYYY-MM-DD H:M:S:MS
	# Pode configurar o formato da Data

    $data = explode(" ", $data);

	if ( $tipo == 1) {
		list($dia, $mes, $ano) = explode("[/-]", $data[0]);
	}elseif ( $tipo == 2 ) {
		list($ano, $mes, $dia) = explode("[-/]", $data[0]);
	}else{
		$msg = "Erro - Formato de data não existe.";
	}

	if ($saida == 1) {
		return $dia."/".$mes."/".$ano;
	}elseif ($saida == 2){
		return $ano."-".$mes."-".$dia;
	}else{
		return 0;
	}
}




echo "<style type=\"text/css\">";
echo ".titulo {";
echo "           font-family: 'verdana';";
echo "           font-weight: bold;";
echo "           font-size : 16px;";
echo "         }";
echo ".subTitulo {";
echo "             font-family: 'verdana';";
echo "             font-weight: bold;";
echo "             font-size : 12px;";
echo "            }";
echo "</style>";
echo "</HEAD>";
echo "<BODY>";


$inicio = $_REQUEST["inicio"];
$fim = $_REQUEST["fim"];
$tds = $_REQUEST["tds"];



if ($tds == "1") { //Lista todos os segurados

$strSQL = "SELECT name, n_Apolice, startValidity, endValidity,
           DATEADD ( MONTH ,7, endValidity ) as d_Pag_Bonus,
           mModulos = CASE WHEN mModulos = '1' THEN 'F9.02 - Bônus por Ausência de sinistro'ELSE pLucro + ' - Participação nos lucros' END,
           percentual = CASE WHEN mModulos = '1' THEN perBonus ELSE perPart0 + '/' + perPart1 END
           FROM Inform
           WHERE state in (10, 11) and Ga = 1 and mModulos in (1, 2)
           order by d_Pag_Bonus, name asc";
}else{

$params = [$inicio, $fim];
$strSQL = "SELECT name, n_Apolice, startValidity, endValidity,
           DATEADD (MONTH, 7, endValidity) as d_Pag_Bonus,
           CASE WHEN mModulos = '1' THEN 'F9.02 - Bônus por Ausência de sinistro'
                ELSE pLucro + ' - Participação nos lucros'
           END as mModulos,
           CASE WHEN mModulos = '1' THEN perBonus
                ELSE perPart0 + '/' + perPart1
           END as percentual
           FROM Inform
           WHERE state = 11 AND Ga = 1 AND mModulos IN (1, 2)
           AND DATEADD (MONTH, 7, endValidity) BETWEEN ? AND ?
           ORDER BY d_Pag_Bonus, name ASC";

$stmt = odbc_prepare($db, $strSQL);
if (odbc_execute($stmt, $params)) {
    $rs = $stmt;
} else {
    $rs = false;
}

odbc_free_result($stmt);


$strTabela = "";
while (odbc_fetch_row($rs)) {

      $strTabela .= "<tr>";
      $strTabela .= " <td class=\"texto\" align=\"left\">".ucfirst(strtolower(odbc_result($rs, "name")))."</td>";
      $strTabela .= " <td class=\"texto\" align=\"center\">".odbc_result($rs, "n_Apolice")."</td>";
      $strTabela .= " <td class=\"texto\" align=\"center\">".TrataData(odbc_result($rs, "startValidity"), 2, 1)." ".TrataData(odbc_result($rs, "endValidity"), 2, 1)."</td>";
      $strTabela .= " <td class=\"texto\" align=\"center\">".odbc_result($rs, "mModulos")."</td>";
      $strTabela .= " <td class=\"texto\" align=\"center\">".odbc_result($rs, "percentual")."</td>";
      $strTabela .= " <td class=\"texto\" align=\"center\">".TrataData(odbc_result($rs, "d_Pag_Bonus"), 2, 1)."</td>";
      $strTabela .= " </tr>";

}



echo "<table align=\"center\" width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\">";
echo "          <tr>";
echo "              <td colspan=\"6\" align=\"center\" class=\"titulo\">Relatório de Pagamento de Bônus</td>";
echo "          </tr>";
echo "          <tr>";
echo "              <td colspan=\"6\" align=\"center\">&nbsp;</td>";
echo "          </tr>";
echo "          <tr bgcolor=\"#aaccff\">";
echo "             <td align=\"center\" class=\"subTitulo\">Segurado</td>";
echo "             <td align=\"center\" class=\"subTitulo\">Nº Apólice</td>";
echo "             <td align=\"center\" class=\"subTitulo\">Vigência</td>";
echo "             <td align=\"center\" class=\"subTitulo\">Tipo Bônus</td>";
echo "             <td align=\"center\" class=\"subTitulo\">% do Bonus</td>";
echo "             <td align=\"center\" class=\"subTitulo\">Dt. Pag. Bônus</td>";
echo "          </tr>";
echo $strTabela;
echo "          <tr>";
echo "              <td colspan=\"6\" align=\"center\">&nbsp;</td>";
echo "          </tr>";
echo "          <tr>";
echo "              <td colspan=\"6\" align=\"left\">";
echo "              </td>";
echo "          </tr>";
echo "</table>";


//echo "<script>window.close();</script>";

