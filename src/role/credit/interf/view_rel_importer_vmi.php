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
</HEAD>
<BODY>


<?php  $strSQLInf = "SELECT * FROM Inform WHERE id = $idInform";
$rs1 = odbc_exec($db, $strSQLInf);
odbc_fetch_row($rs1);
$segurado = odbc_result($rs1, "name");

if (odbc_result($rs1, "currency") == 2 ) {
   $currency = "US$";
}else{
   $currency = "£";
}

$startValidity = odbc_result($rs1, "startValidity");
$endValidity   = odbc_result($rs1, "endValidity");
$n_Apolice     = odbc_result($rs1, "n_Apolice");


$strSQL = "select name, country, solicitado, concedido  from (
           select  distinct imp.name, co.name as country,
           solicitado = isnull(imp.limCredit, 0),
           concedido = (ch.credit + isnull(ch.creditTemp, 0)),
           hc_credit = ((ch.credit + isnull(ch.creditTemp, 0)) * (inf.percCoverage/100)),
           hcx_vmi = inf.limPagIndeniz * ( CASE WHEN inf.warantyInterest = 1 THEN inf.prMin + (inf.prMin * 0.04) ELSE inf.prMin END),
           case when ( ((ch.credit + isnull(ch.creditTemp, 0) ) * (inf.percCoverage/100)) > inf.limPagIndeniz * ( CASE WHEN inf.warantyInterest = 1 THEN inf.prMin + (inf.prMin * 0.04) ELSE inf.prMin END)) then 'vmi' else '' end as vmi


           from Inform inf
           INNER JOIN Importer imp ON (imp.idInform = inf.id)
           INNER JOIN ChangeCredit ch ON (imp.id = ch.idImporter)
           INNER JOIN Country co ON (imp.idCountry = co.id)

           WHERE inf.state = 10 AND
           ch.id = (select max(cc.id) from ChangeCredit cc where cc.idImporter=imp.id) and
           imp.state = 6 AND imp.idInform = $idInform

           GROUP BY imp.name, ch.credit, ch.creditTemp, inf.percCoverage, inf.limPagIndeniz, inf.warantyInterest, inf.prMin,
           co.name, imp.limCredit
           ) as t

           where vmi = 'vmi'
           and hcx_vmi > 0

           GROUP  BY  name, country, solicitado, concedido
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
      $strTabela .= " <td  align=\"left\" class=\"textoBold\">&nbsp;<a href=\"#\" >".ucfirst(strtolower(odbc_result($rs, "name")))."</a></td>";
      $strTabela .= " <td  align=\"center\" class=\"textoBold\">".odbc_result($rs, "country")."</td>";
      $strTabela .= " <td  align=\"center\" class=\"textoBlod\">".round((odbc_result($rs, "solicitado") /1000))."</td>";
      $strTabela .= " <td  align=\"center\" class=\"textoBlod\">".(odbc_result($rs, "concedido")/1000)."</td>";
      $strTabela .= " </tr>";

}

?>
<br/><br/><br/><br/>
<table align="center" width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>Segurado</td>
            <td><?php echo $segurado;?></td>
        </tr>
        <tr>
             <td> Nº Apólice</td>
             <td><?php echo $n_Apolice;?></td>
        </tr>
        <tr>
             <td>Vigência</td>
             <td><?php echo  TrataData(odbc_result($rs1, "startValidity"), 2, 1)." a ".TrataData(odbc_result($rs1, "endValidity"), 2, 1);?></td>
        </tr>
</table>
<br/>
<table align="center" width="100%" border="0" cellspacing="1" cellpadding="1">

          <tr>
              <td colspan="4" align="center" class="titulo"></td>
          </tr>
          <tr>
              <td colspan="4" align="center">&nbsp;</td>
          </tr>
          <tr bgcolor="#aaccff">
             <td align="center" class="textBold">Importador</td>
             <td align="center" class="textBold">País</td>
             <td align="center" class="textBold">Limite de Crédito<br/> Solicitado <?php echo $currency;?> (Mil)</td>
             <td align="center" class="textBold">Limite de Crédito<br/> Aprovado <?php echo $currency;?> (Mil)</td>
          </tr>
          <tr>
              <td colspan="4" align="center">&nbsp;</td>
          </tr>
          <?php echo $strTabela;?>
          <tr>
              <td colspan="4" align="center">&nbsp;</td>
          </tr>
          <tr>
              <td colspan="4" align="left">&nbsp;</td>
          </tr>

</table>
</BODY>
</HTML>
