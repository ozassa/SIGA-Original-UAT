<?php  //require("../../dbOpen.php");
$autorizacao = $_REQUEST['params'];

//define a visualizacao no formato XLS
$excel = $_REQUEST['excel'];

/*
echo "<pre>";
print_r($_SERVER);
echo "</pre>";
*/


if ($excel=="yes") {

  //define o tipo do documento
  header('Content-type: application/msexcel');
  header('Content-Disposition: attachment; filename="apolices_cedidas_BP.xls"');
}


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




?>
<HTML>
<HEAD>
 <TITLE></TITLE>


<script language="javascript">
  function imprimir() {
    //frm_view_rel_apolice.icones.style.display = \'none\';
    window.print();
    //frm_view_rel_apolice.icones.style.display = \'block\';
  }
  function gerar_excel() 
  {
    frm_view_rel_apolice.submit(true);
  }
</script>

</HEAD>
<BODY>


<?php  echo "Data:".date("d-m-Y H:i:s")."<br>";
//criado por Wagner 02/09/2008
////esta query serve para poder pegar o valor total das apolices cedidas para o banco parceiros
$sqlTotalApolice="SELECT     COUNT(DISTINCT i.n_Apolice) AS EXPR1
FROM         Inform i INNER JOIN
                      CDParc bb ON i.id = bb.idInform INNER JOIN
                      Agencia a ON bb.idAgencia = a.id INNER JOIN
                      Banco b ON a.idBanco = b.id
WHERE     (b.tipo = 2) AND (bb.status = 2) AND (i.state = 10)";
$rsTotalApolice = odbc_exec($db, $sqlTotalApolice);

$total = odbc_result($rsTotalApolice, 1);

//imprime na pagina o total das apolices para o banco parceiros
echo "Total de Apólices Cedidas para o bancos parceiros:".$total."<br><br>";

//esta query traz os dados de apolices , exportador e banco para ser uma mostragem descritiva do total de apolices para o banco parceiros
$strSQL = "SELECT DISTINCT i.n_Apolice AS Apolice, i.name AS Exportador, b.name AS Banco
FROM         Inform i INNER JOIN
                      CDParc bb ON i.id = bb.idInform INNER JOIN
                      Agencia a ON bb.idAgencia = a.id INNER JOIN
                      Banco b ON a.idBanco = b.id
WHERE     (b.tipo = 2) AND (bb.status = 2) AND (i.state = 10)
ORDER BY Exportador";


$rs = odbc_exec($db, $strSQL);

$strTabela = "";
$i = 0;
$cont = 1;
$bancos = array();
while (odbc_fetch_row($rs)) {

      if ($i ==0) {
          $cor = "#CCCCCF";
          $i = 1;
      }else{
          $cor = "#FFFFFF";
          $i = 0;
      }



	  array_push($bancos,odbc_result($rs, "Banco"));
	  
      $strTabela .= "<tr bgcolor=$cor>";
	  $strTabela .= " <td  align=\"left\" >&nbsp;".$cont++."</td>";
      $strTabela .= " <td  align=\"left\" class=\"textoBold\">&nbsp;".odbc_result($rs, "Apolice")."</td>";
      $strTabela .= " <td  align=\"left\" >".odbc_result($rs, "Exportador")."</td>";
      $strTabela .= " <td  align=\"left\" width='25%'>".odbc_result($rs, "Banco")."</td>";
      $strTabela .= " </tr>";
	  
	

}
/*
echo "<pre>";
//print_r($bancos);
//print_r(array_count_values($bancos));
echo "</pre>";
*/
//ESTA MOSTRA RESUMIDAMENTE CADA BANCO QUANTAS APOLICES TEM
$totalParaCadaBanco = array_count_values($bancos);
$cont=1;
echo "<table align='center' width='100%' border='0' cellspacing='1' cellpadding='1'>";
echo " <tr bgcolor='#aaccff'>
			 <td align='center' class='textBold'>Linha</td>
             <td align='center' class='textBold'>Banco</td>
             <td align='center' class='textBold'>Quantidade de Apólices</td>
          </tr>";

ksort($totalParaCadaBanco);		  
foreach($totalParaCadaBanco as $chave=>$valor)
{
      if ($i ==0) {
          $cor = "#CCCCCF";
          $i = 1;
      }else{
          $cor = "#FFFFFF";
          $i = 0;
      }
	  
echo "<tr bgcolor=$cor>";
	echo "<td align='left' class='textBold'>".$cont++."</td>";
	echo "<td align='left' class='textBold'>".$chave."</td>";
	echo "<td align='center' class='textBold'>".$valor."</td>";
echo "</tr>";
}
echo "</table><br>";

?>
<form name="frm_view_rel_apolice" action="view_apolices_cedidas_BP.php" method="post" target="_self">
  
  <INPUT type="hidden" name="excel"  value="yes">
<table align="center" width="100%" border="0" cellspacing="1" cellpadding="1">

          <tr>
              <td colspan="3" align="center" class="titulo"></td>
          </tr>

          <tr bgcolor="#aaccff">
			 <td align="center" class="textBold">Linha</td>
             <td align="center" class="textBold">Apólice</td>
             <td align="center" class="textBold">Exportador</td>
             <td align="center" class="textBold">Banco</td>
          </tr>

          <?php echo $strTabela;?>
          <tr>
              <td colspan="3" align="center">&nbsp;</td>
          </tr>
          <tr>
              <td colspan="3" align="left">&nbsp;</td>
          </tr>

</table>
</form>
<?php
if ($excel!="yes" && $total!=0) {
  echo '
          <a href="#" onclick="imprimir()" title="Imprimir"><img src="img/btn_printer.jpg" width="40" height="40" border="0"></a>
          <a href="#" onclick="gerar_excel()" title="Exportar para Excel"><img src="img/icone_xls.gif" width="32" height="32" border="0"></a>
       ';
}
?>
</BODY>
</HTML>
