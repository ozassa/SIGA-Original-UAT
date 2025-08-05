<?php  //define a visualizacao no formato XLS
$excel = $_REQUEST['excel'];



if ($excel=="yes") {
  //define o tipo do documento
  header('Content-type: application/msexcel');
  header('Content-Disposition: attachment; filename="view_apolices_cedidas_BP.xls"');
}

//define a classe para abertura do banco de dados
require_once("../../dbOpen.php");

echo "Relatório de Apólices cedidas para Bancos Parceiros<br>";
echo "Data:".date("d-m-Y H:i:s")."<br>";

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
echo "Total de Apólices Cedidas para o bancos parceiros:".$total;

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
      $strTabela .= " <td  align=\"center\" width='25%'>".odbc_result($rs, "Banco")."</td>";
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
echo "</table>";
?>




<table align="center" width="100%" border="0" cellspacing="1" cellpadding="1">

          <tr>
              <td colspan="3" align="center" class="titulo"></td>
          </tr>
          <tr>
              <td colspan="3" align="center">&nbsp;</td>
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



