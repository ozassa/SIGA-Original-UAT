<?php  require_once("../../dbOpen.php");



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


$inicio = TrataData($_POST["inicio"], 1, 2);
$fim = TrataData($_POST["fim"], 1, 2);
$tds = $_POST["tds"];


?>
<HTML>
<HEAD>
 <TITLE>Relatório de Pagamento de Bônus</TITLE>
<style type="text/css">
.titulo {
           font-family: "verdana";
           font-weight: bold;
           font-size : 16px;
         }
.subTitulo {
             font-family: "verdana";
             font-weight: bold;
             font-size : 12px;
            }

</style>

<script language="javascript">
 function Imprimir(){
   window.print();
 }

 function GerarExcel(){
   window.open('excelRelBonus.php?inicio=<?php echo $inicio?>&fim=<?php echo $fim?>&tds=<?php echo $tds?>', 'Relatório', 'scrollbars=no,status=no,width=50,height=50,left=20,top=10,resizable=no');
  // window.open('excelRelBonus.php?inicio=<?php echo $inicio?>&fim=<?php echo $fim?>&tds=<?php echo $tds?>', 'Relatório', '');
 }
</script>

</HEAD>
<BODY>

<object style="display:none"
  classid="clsid:1663ed61-23eb-11d2-b92f-008048fdd814"
  codebase="http://sbcerj08/siex/src/role/credit/scriptx/smsx.cab#Version=6,3,435,20">
</object>

<?php  if ($tds == "1") { //Lista todos os segurados

$strSQL = "SELECT name, n_Apolice, startValidity, endValidity,
           DATEADD ( MONTH ,7, endValidity ) as d_Pag_Bonus,
           mModulos = CASE WHEN mModulos = '1' THEN 'F9.02 - Bônus por Ausência de sinistro'ELSE pLucro + ' - Participação nos lucros' END,
           percentual = CASE WHEN mModulos = '1' THEN perBonus ELSE perPart0 + '/' + perPart1 END
           FROM Inform
           WHERE state in (10, 11) and Ga = 1 and mModulos in (1, 2)
           order by d_Pag_Bonus, name asc";

}else{

$strSQL = "SELECT name, n_Apolice, startValidity, endValidity,
           DATEADD ( MONTH ,7, endValidity ) as d_Pag_Bonus,
           CASE 
               WHEN mModulos = '1' THEN 'F9.02 - Bônus por Ausência de sinistro'
               ELSE CONCAT(pLucro, ' - Participação nos lucros') 
           END as mModulos,
           CASE 
               WHEN mModulos = '1' THEN perBonus
               ELSE CONCAT(perPart0, '/', perPart1)
           END as percentual
           FROM Inform
           WHERE state = ? and Ga = ? and mModulos in (?, ?)
           AND DATEADD ( MONTH ,7, endValidity ) BETWEEN ? AND ?
           ORDER BY d_Pag_Bonus, name ASC";

$stmt = odbc_prepare($db, $strSQL);

$params = [
    11,       // state
    1,        // Ga
    1,        // mModulos value 1
    2,        // mModulos value 2
    $inicio,  // start date
    $fim      // end date
];

odbc_execute($stmt, $params);
$rs = $stmt;
odbc_free_result($stmt);

$strTabela = "";
while (odbc_fetch_row($rs)) {

      $strTabela .= "<tr>";
      $strTabela .= " <td  align=\"left\">".ucfirst(strtolower(odbc_result($rs, "name")))."</td>";
      $strTabela .= " <td  align=\"center\">".odbc_result($rs, "n_Apolice")."</td>";
      $strTabela .= " <td  align=\"center\">".TrataData(odbc_result($rs, "startValidity"), 2, 1)." ".TrataData(odbc_result($rs, "endValidity"), 2, 1)."</td>";
      $strTabela .= " <td  align=\"center\">".odbc_result($rs, "mModulos")."</td>";
      $strTabela .= " <td  align=\"center\">".odbc_result($rs, "percentual")."</td>";
      $strTabela .= " <td  align=\"center\">".TrataData(odbc_result($rs, "d_Pag_Bonus"), 2, 1)."</td>";
      $strTabela .= " </tr>";

}

/*
echo "Data Inicio => " . $_POST["inicio"];
echo "<br>";
echo "Data Fim => " . $_POST["fim"];
*/

?>
<table align="center" width="100%" border="0" cellspacing="1" cellpadding="1">

          <tr>
              <td colspan="6" align="center" class="titulo">Relatório de Pagamento de Bônus</td>
          </tr>
          <tr>
              <td colspan="6" align="center">&nbsp;</td>
          </tr>
          <tr bgcolor="#aaccff">
             <td align="center" class="subTitulo">Segurado</td>
             <td align="center" class="subTitulo">Nº Apólice</td>
             <td align="center" class="subTitulo">Vigência</td>
             <td align="center" class="subTitulo">Tipo Bônus</td>
             <td align="center" class="subTitulo">% do Bonus</td>
             <td align="center" class="subTitulo">Dt. Pag. Bônus</td>
          </tr>
          <?php echo $strTabela?>
          <tr>
              <td colspan="6" align="center">&nbsp;</td>
          </tr>
          <tr>
              <td colspan="6" align="left">
                  <a href="#" onClick="Imprimir()" title="Imprimir"><img src="img/btn_printer.jpg" width="40" height="40" border="0"></a>
                  <a href="#" onClick="GerarExcel()" title="Exportar para Excel"><img src="img/icone_xls.gif" width="32" height="32" border="0"></a>
              </td>
          </tr>

</table>
</BODY>
</HTML>
