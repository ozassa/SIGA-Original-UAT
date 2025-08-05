<?php  //require_once("../../dbOpen.php");



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

 function GerarExcel(){
   window.open('excelRelBonus.php?inicio=<?php echo $inicio;?>&fim=<?php echo $fim;?>&tds=<?php echo $tds;?>', 'Relatório', 'scrollbars=no,status=no,width=50,height=50,left=20,top=10,resizable=no');
 }
</script>

</HEAD>
<BODY>


<?php  $strSQL = "select  id, name, n_Apolice, startValidity, endValidity from (
          select  distinct inf.id, inf.name, inf.n_Apolice,inf.startValidity, inf.endValidity,

          hc_credit = ( (ch.credit + isnull(ch.creditTemp, 0)) * (inf.percCoverage/100)),
          hcx_vmi = inf.limPagIndeniz * ( CASE WHEN inf.warantyInterest = 1 THEN inf.prMin + (inf.prMin * 0.04) ELSE inf.prMin END),
          case when ( ((ch.credit + isnull(ch.creditTemp, 0)) * (inf.percCoverage/100)) > inf.limPagIndeniz * ( CASE WHEN inf.warantyInterest = 1 THEN inf.prMin + (inf.prMin * 0.04) ELSE inf.prMin END)) then 'vmi' else '' end as vmi

          from Inform inf
          INNER JOIN Importer imp ON (imp.idInform = inf.id)
          INNER JOIN ChangeCredit ch ON (imp.id = ch.idImporter)

          WHERE inf.state = 10 AND
          ch.id = (select max(cc.id) from ChangeCredit cc where cc.idImporter=imp.id) and

          imp.state = 6

          GROUP BY inf.id, inf.name, ch.credit, ch.creditTemp, inf.percCoverage, inf.limPagIndeniz, inf.warantyInterest, inf.prMin,
          inf.n_Apolice, inf.startValidity, inf.endValidity
          
          ) as t
          
          where vmi = 'vmi'
          and hcx_vmi > 0

          GROUP  BY id, name, n_Apolice, startValidity, endValidity
          ORDER BY name";


$rs = odbc_exec($db, $strSQL);

$strTabela = "";
$i = 0;
while (odbc_fetch_row($rs)) {

      if ($i ==0) {
          $cor = "#CCCCCF";
          $i = 1;
      }else{
          $cor = "#FFFFFF";
          $i = 0;
      }


      $strTabela .= "<tr bgcolor=$cor>";
      $strTabela .= " <td  align=\"left\" class=\"textoBold\">&nbsp;<a href=\"../credit/credit.php?comm=view_rel_importer_vmi&idInform=".odbc_result($rs, "id")."\" >".ucfirst(strtolower(odbc_result($rs, "name")))."</a></td>";
      $strTabela .= " <td  align=\"center\" class=\"textoBold\">".odbc_result($rs, "n_Apolice")."</td>";
      $strTabela .= " <td  align=\"center\" class=\"textoBlod\">".TrataData(odbc_result($rs, "startValidity"), 2, 1)." a ".TrataData(odbc_result($rs, "endValidity"), 2, 1)."</td>";
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
             <td align="center" class="textBold">Segurado</td>
             <td align="center" class="textBold">Nº Apólice</td>
             <td align="center" class="textBold">Vigência</td>
          </tr>
          <tr>
              <td colspan="3" align="center">&nbsp;</td>
          </tr>
          <?php echo $strTabela;?>
          <tr>
              <td colspan="3" align="center">&nbsp;</td>
          </tr>
          <tr>
              <td colspan="3" align="left">&nbsp;</td>
          </tr>

</table>
</BODY>
</HTML>
