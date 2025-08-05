<?php  //define a visualizacao no formato XLS
$excel = $_REQUEST['excel'];



if ($excel=="yes") {
  //define o tipo do documento
  header('Content-type: application/msexcel');
  header('Content-Disposition: attachment; filename="view_apolices_cedidas_BB.xls"');
}

//define a classe para abertura do banco de dados
require_once("../../dbOpen.php");
echo "Relatório de Apólices cedidas para o Banco do Brasil";
echo "<br>Data:".date("d-m-Y H:i:s")."<br>";
//criado por Wagner 02/09/2008
//esta query serve para poder pegar o valor total das apolices cedidas para o banco do brasil
$sqlTotalApolice="SELECT     COUNT(DISTINCT i.n_Apolice) AS EXPR1
FROM         Inform i INNER JOIN
                      CDBB bb ON i.id = bb.idInform INNER JOIN
                      Agencia a ON bb.idAgencia = a.id INNER JOIN
                      Banco b ON a.idBanco = b.id
WHERE     (b.tipo = 1) AND (bb.status = 2) AND (i.state = 10)";
$rsTotalApolice = odbc_exec($db, $sqlTotalApolice);

$total = odbc_result($rsTotalApolice, 1);

//imprime na pagina o total das apolices para o banco do brasil
echo "Total de Apólices Cedidas para o Banco do Brasil:".$total;


//esta query traz os dados de apolices , exportador e banco para ser uma mostragem descritiva do total de apolices para o banco do brasil
$strSQL = "SELECT DISTINCT i.n_Apolice AS Apolice, i.name AS Exportador, b.name AS Banco
FROM         Inform i INNER JOIN
                      CDBB bb ON i.id = bb.idInform INNER JOIN
                      Agencia a ON bb.idAgencia = a.id INNER JOIN
                      Banco b ON a.idBanco = b.id
WHERE     (b.tipo = 1) AND (bb.status = 2) AND (i.state = 10)
ORDER BY Exportador";


$rs = odbc_exec($db, $strSQL);

$strTabela = "";
$i = 0;
$cont = 1;
while (odbc_fetch_row($rs)) {

      if ($i ==0) {
          $cor = "#CCCCCF";
          $i = 1;
      }else{
          $cor = "#FFFFFF";
          $i = 0;
      }


      $strTabela .= "<tr bgcolor=$cor>";
	  $strTabela .= " <td  align=\"left\" >&nbsp;".$cont++."</td>";
      $strTabela .= " <td  align=\"left\" class=\"textoBold\">&nbsp;".odbc_result($rs, "Apolice")."</td>";
      $strTabela .= " <td  align=\"left\" >".odbc_result($rs, "Exportador")."</td>";
      $strTabela .= " <td  align=\"center\" width='25%'>".odbc_result($rs, "Banco")."</td>";
      $strTabela .= " </tr>";
	  
	

}

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



