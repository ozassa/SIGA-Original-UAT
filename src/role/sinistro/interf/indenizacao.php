<script language="javascript">
   function muda_task(b,idDet){
     loc = "<?php echo $root; ?>role/sinistro/Sinistro.php?comm=pagInd&idDetails="+idDet+"&idInform=<?php echo $idInform; ?>&idNotification=<?php echo $idNotification; ?>&idSinistro=<?php echo $idSinistro;?>&idImporter=<?php echo $idImporter;?>";
     if(b){
       location = loc + "&val=0";
     }else{
       location = loc + "&val=1";
     }
   }
</script>

<?php $query = "SELECT inf.name, imp.name, inf.limPagIndeniz, inf.prMin, inf.warantyInterest, inf.percCoverage, inf.txRise, imp.credit, inf.prMTotal, imp.c_Coface_Imp FROM Inform inf JOIN Importer imp ON (imp.idInform = inf.id) WHERE imp.id = $idImporter";
    $cur = odbc_exec($db,$query);
    $nameExp = odbc_result($cur, 1);
    $nameImp = odbc_result($cur, 2);
    $limPag = odbc_result($cur, 3);
    $jurosOk = odbc_result($cur, 5);
    $percCo = odbc_result($cur, 6);
    $txRise = odbc_result($cur, 7);
    $credit = odbc_result($cur, 8);
    $premio_minimo = odbc_result($cur, 9);
    $c_Coface_Imp = odbc_result($cur, 10);

//----------------------------------------------------------------------------------------------------
// pega o numero do sinistro do SisSeg 
$cur = odbc_exec($db,
		 "SELECT Inform.i_Seg, Inform.idAnt
                  FROM Inform, Importer Where Inform.id=$idInform AND Importer.id=$idImporter");
$i_Seg = odbc_result($cur, 1);
$idAnt = odbc_result($cur, 2);
if($idAnt && ! $i_Seg){
  $i_Seg = odbc_result(odbc_exec($db, "select i_Seg from Inform where id=$idAnt"), 1);
}
$x = odbc_exec($dbSisSeg, "select max(n_Apolice) from Apolice where i_Seg=$i_Seg");
$n_Apolice = odbc_result($x, 1);
$x = odbc_exec($dbSisSeg, "select n_Sucursal, n_Ramo from Apolice where n_Apolice=$n_Apolice");
$n_Sucursal = odbc_result($x, 1);
$n_Ramo = odbc_result($x, 2);
$n_Imp = 0;
$ci_coface = odbc_result(odbc_exec($db, "select c_Coface_Imp from Importer where id=$idImporter"), 1);
if($ci_coface){
  $n_Imp = odbc_result(odbc_exec($dbSisSeg,
				 "select n_Imp from Importador where c_Coface_Imp='$ci_coface' and i_Seg=$i_Seg"),
		       1);
}
$x = odbc_exec($dbSisSeg,
	       "select n_Sinistro from Aviso_Sinistro
                where n_Apolice=$n_Apolice and i_Seg=$i_Seg
                and n_Sucursal=$n_Sucursal and n_Ramo=$n_Ramo and n_Imp=$n_Imp");
$n_Sinistro = odbc_result($x, 1);
//----------------------------------------------------------------------------------------------------

  $cur=odbc_exec($db, "SELECT id, status, date, value, juro, indenizacao, nSinistro, limCredit FROM Sinistro WHERE id = $idSinistro");

//$numSinistro = odbc_result($cur, 'nSinistro');
  $numSinistro = $n_Sinistro;
  $status = odbc_result($cur, 'status');
  $date = odbc_result($cur, 'date');
  $sinist = odbc_result($cur, 'value');
  $juros = odbc_result($cur, 'juro');
  $inden = odbc_result($cur, 'indenizacao');
  $nSinistro = odbc_result($cur, 'nSinistro');
  $limCredit = odbc_result($cur, 'limCredit');

  $cur=odbc_exec($db,  "SELECT SUM(r.value)
FROM
  Inform inf1
  JOIN Insured ins ON (inf1.idInsured = ins.id)
  JOIN Inform inf2 ON (inf2.idInsured = ins.id)
  JOIN Importer imp ON (imp.idInform = inf2.id)
  JOIN Sinistro s ON (s.idImporter = imp.id)
  JOIN Ressarcimento r ON (r.idSinistro = s.id)
WHERE
  inf1.id = $idInform");

  $ressarcimento = odbc_result($cur, 1);

  if ($inden){
    $valorVMI = (($premio_minimo * $limPag) - $inden + $ressarcimento);       // valor indenizavel
  } else {
    $valorVMI = (($premio_minimo * $limPag) + $ressarcimento);       // valor indenizavel
  }

$cur = odbc_exec($db,"SELECT valueAbt, valueCob FROM SinistroDetails WHERE idSinistro = $idSinistro");
$i = 0;
$valueTotal = 0;
$valueCobTotal = 0;
while (odbc_fetch_row($cur)) {
  $i++;
  $valueAbt = odbc_result($cur, 'valueAbt');
  $valueTotal = $valueTotal + $valueAbt;
  $valueCob1 = odbc_result($cur, 'valueCob');
  $valueCobTotal = $valueCobTotal + $valueCob1;
}

$cur = odbc_exec($db,"SELECT value FROM Recuperacao WHERE idSinistro = $idSinistro");
$i = 0;
$totalRecup = 0;
while (odbc_fetch_row($cur)) {
  $i++;
  $valueR = odbc_result($cur, 'value');
  $totalRecup = $totalRecup + $valueR;
}

$valorPago = $inden + $totalRecup;
$perdas = $valueTotal + $juros - $totalRecup;
$valorAvisado = $valueTotal - $totalRecup;
//$valorAvisado = $valueCobTotal - $totalRecup;

$q = "SELECT max (cc.credit)
FROM
  Inform inf1
  JOIN Insured ins ON (inf1.idInsured = ins.id)
  JOIN Inform inf2 ON (inf2.idInsured = ins.id)
  JOIN Importer imp ON (imp.idInform = inf2.id)
  JOIN ChangeCredit cc ON (cc.idImporter = imp.id)
WHERE
  inf1.id = $idInform AND
  imp.c_Coface_Imp = '$c_Coface_Imp'";
$cur = odbc_exec($db, $q);
$limiteC = odbc_result($cur, 1);

?>

<form action="../sinistro/Sinistro.php" method="post">
<input type="hidden" name="comm">
<input type="hidden" name="idInform" value="<?php echo $idInform;?>">
<input type="hidden" name="idImporter" value="<?php echo $idImporter;?>">
<input type="hidden" name="idSinistro" value="<?php echo $idSinistro;?>">
<input type="hidden" name="idDetails" value="<?php echo $idDetails;?>">
<input type="hidden" name="idNotification" value="<?php echo $idNotification;?>">

<table border="0" cellSpacing=0 cellpadding="3" width="98%" align="center">
  <TR>
    <td colspan=2>&nbsp;</td>
  </TR>
<?php $query = "SELECT inf.name, imp.name FROM Inform inf JOIN Importer imp ON (imp.idInform = inf.id) WHERE imp.id = $idImporter";
    $cur = odbc_exec($db,$query);
?>
  <TR>
    <td width="30%">Exportador: </td>
    <td><span class="texto"><?php echo $nameExp;?></span></td>
  </TR>
  <TR>
    <td>Importador: </td>
    <td><span class="texto"><?php echo $nameImp;?></span></td>
  </TR>
  <TR>
    <td>Número do Sinistro: </td>
    <td><span class="texto"><?php echo $numSinistro; ?></span></td>
  </TR>
  <TR>
    <td>Status: </td>
    <td><span class="texto">
<?php switch($status){
            case 2 : echo "Aviso"; break;
            case 3 : echo "Sinistro"; break;
            case 4 : echo "Suspenso"; break;
            case 5 : echo "Cancelado"; break;
            case 6 : echo "Não Aceito"; break;
            case 7 : echo "Recuperado"; break;
            case 8 : echo "Inden. Aprovada"; break;
          }
?>
</span></td>
  </TR>
  <TR>
    <td>Valor Avisado: </td>
    <td><span class="texto"><?php echo number_format(($valorAvisado),2,",","."); ?></span></td>
  </TR>
  <TR>
    <td>Valor Coberto: </td>
    <td><span class="texto">
<?php if($valueCobTotal > $credit){ 
   echo number_format(($credit),2,",",".");
}else{ 
   echo number_format(($valueCobTotal),2,",",".");
}
?></span></td>
  </TR>
  <TR>
    <td>Valor do Sinistro: </td>
    <td><span class="texto">
<?php echo number_format(($valueCobTotal + $juros),2,",",".");
if($juros){
   $valorInd = (($valueCobTotal + $juros * 85) / 100);
}else{
   $valorInd = (($valueCobTotal * 85) / 100);
}
?> 
     </span></td>
  </TR>
  <TR>
    <td nowrap class="verm">Valor Indenizável: </td>
    <td colspan=3><span class="verm">
<?php if($juros){
   $valorInd = ((($valueCobTotal + $juros) * 85) / 100);
}else{
   $valorInd = (($valueCobTotal * 85) / 100);
}
if(($valorInd > $credit) || ($valorInd > $valorVMI)){ 
   if($valorVMI < $credit){
     $valorIndenizavel = $valorVMI;
     echo number_format(($valorVMI),2,",",".");
   }else{
     $valorIndenizavel = $credit;
     echo number_format(($credit),2,",",".");
   }
}else{
   $valorIndenizavel = $valorInd;
   echo number_format(($valorInd),2,",",".");
}
?></span>
<input type="hidden" name="valorInd" value="<?php echo $valorIndenizavel; ?>">
    </td>
  </TR>
  <TR>
    <td>VMI: </td>
    <td><span class="texto"><?php echo number_format(($valorVMI),2,",","."); ?></span></td>
    <input type="hidden" name="vmi" value="<?php echo $valorVMI; ?>">
  </TR>
<?php if($jurosOk == 1 ){?>
  <TR>
    <td>Valor dos Juros: </td>
    <td><span class="texto"><?php echo number_format(($juros),2,",","."); ?></span></td>
  </TR>
<?php }?>
  <TR>
    <td colspan=2>&nbsp;</td>
  </TR>
</table>

<table border="0" cellSpacing=0 cellpadding="3" width="98%" align="center">
  <TR>
    <td colspan=7>&nbsp;</td>
  </TR>
  <TR>
    <td colspan=7 class="bgCinza" align="center">Faturas Não Pagas</td>
  </TR>
  <TR>
    <td colspan=7>&nbsp;</td>
  </TR>
  <TR>
    <!--td class=bgAzul align=center>Indenizar</td-->
    <td class=bgAzul align=center>Nº Fatura</td>
    <td class=bgAzul align=center>Data de Embarque</td>
    <td class=bgAzul align=center>Data de Vencimento</td>
    <td class=bgAzul align=center>Valor da Fatura</td>
    <td class=bgAzul align=center>Valor Pago</td>
    <td class=bgAzul align=center>Valor Coberto</td>
    <td class=bgAzul align=center>Valor em Aberto</td>
  </TR>
<?php $query = "SELECT * FROM SinistroDetails WHERE idSinistro = $idSinistro ORDER BY dateEmb";
    $cur = odbc_exec($db,$query);
    $i = 0;
    $valueTotal = 0;
    $valueCobTot = 0;
    while (odbc_fetch_row($cur)) {
      $i++;
      $sel = 0;
      if (odbc_result($cur,10) == 1)
	$sel = 1;

      $idDetails = odbc_result($cur,1); 
      $dateEmb = odbc_result($cur,5); 
      $dateVenc = odbc_result($cur,6);
      $valuePag = odbc_result($cur, 7);
      $valueFat = odbc_result($cur, 8);
      $valueAbt = odbc_result($cur, 9);
      $valueTotal = $valueTotal + $valueAbt;
      $valueCoberto = odbc_result($cur, 11);
      $valueCobTot = $valueCobTot + $valueCoberto;

?>
  <TR <?php echo ((($i % 2) != 0) ? " bgcolor=\"#ffffff\"" : " bgcolor=\"#e9e9e9\"");?>>
    <!--td align=center>    <input type="hidden" name="idDetails" value="<?php echo $idDetails;?>">
<input type="checkbox" name="t<?php echo $i;?>" value="<?php echo $i;?>" <?php echo $sel == 1 ? 'checked ' : ''; ?> onClick="muda_task(<?php echo $sel; ?>,<?php echo $idDetails; ?>)"></td-->
    <td align=center><?php echo odbc_result($cur,4); ?></td>
    <td align=center><?php echo substr($dateEmb,8,2)."/".substr($dateEmb,5,2)."/".substr($dateEmb,2,2); ?></td>
    <td align=center><?php echo substr($dateVenc,8,2)."/".substr($dateVenc,5,2)."/".substr($dateVenc,2,2); ?></td>
    <td align=right><?php echo number_format($valueFat,2,",","."); ?> &nbsp;</td>
    <td align=right><?php echo number_format($valuePag,2,",","."); ?> &nbsp;</td>
    <td align=right><?php echo number_format($valueCoberto,2,",","."); ?> &nbsp;</td>
    <td align=right><?php echo number_format($valueAbt,2,",","."); ?> &nbsp;</td>
  </TR>
<?php } // while
  if ($i == 0) {

?>

  <TR class="bgCinza">
    <TD align="center" colspan=7 class="bgCinza">Nenhum Dado Cadastrado</TD>
  </TR>

<?php }
?>
  <TR>
    <td class=bgAzul align=right colspan=6>Total (em aberto):</td>
    <td align=right><?php echo number_format($valueTotal,2,",","."); ?> &nbsp;</td>
  </TR>
<?php $cor = odbc_exec($db, "SELECT value FROM Recuperacao WHERE idSinistro = $idSinistro");
      $i = 0;
      $valueT = 0;
      while (odbc_fetch_row($cor)) {
        $i++;
        $valueR = odbc_result($cor,1); 
        $valueT = $valueT + $valueR;
      }  
?>
  <TR>
    <td class=bgAzul align=right colspan=6>Total Recuperado:</td>
    <td align=right><?php echo number_format($valueT,2,",","."); ?> &nbsp;</td>
  </TR>
  <TR>
    <td colspan=7>&nbsp;</td>
  </TR>
<?php //$query = "SELECT valueCob FROM SinistroDetails WHERE idSinistro = $idSinistro AND selected = 1";
    //$cur = odbc_exec($db,$query);
    //$i = 0;
    //$valueTt = 0;
    //while (odbc_fetch_row($cur)) {
    //  $i++;
    //  $valueTt = $valueTt + odbc_result($cur,1);
    //}

    $query = "SELECT indenizacao FROM Sinistro WHERE idImporter = $idImporter";
    $ind = odbc_exec($db,$query);
    $i = 0;
    $totalInd = 0;
    while (odbc_fetch_row($ind)) {
      $i++;
      $totalInd = $totalInd + odbc_result($ind,1);
    }
    
    if($valorVMI < $credit){
      $valorP = ($valorVMI - $totalInd - $valueT); // indenização máxima - indenizações já pagas - recuperações
    }else{
      $valorP = ($credit - $totalInd - $valueT);   // indenização máxima - indenizações já pagas - recuperações
    }
    $valorH = (($valueTt * $percCo) / 100);        // porcentagem máxima q pode ser paga
    //$valorI = $valorH - $valueT;                 // valor q será indenizado
    
    //if ($valueTt < $valorIndenizavel){
?>
  <!--TR>
    <td colspan="7">Valor Indenizável: <?php echo number_format(($valorIndenizavel),2,",","."); ?> &nbsp;</td>
  </TR>
  <TR>
    <td colspan="8">Valores Selecionados: <?php echo number_format(($valueTt),2,",","."); ?> &nbsp;</td>
  </TR>
  <input type="hidden" name="valorInd" value="<?php echo $valueTt; ?>"-->
  <TR>
    <td colspan=7 align="center"><input class="servicos" type=button value="Indenizar o valor de <?php echo number_format(($valorIndenizavel),2,",",".");?>" onClick="this.form.comm.value='indenizar';this.form.submit()"></td>
  </TR>
<?php //} else {
?>
  <!--TR>
    <td colspan="8">Valor Indenizável:  <?php echo number_format(($valorIndenizavel),2,",","."); ?> &nbsp;</td>
  </TR>
  <TR>
    <td colspan="8">Valores Selecionados: <?php echo number_format(($valueTt),2,",","."); ?> &nbsp;</td>
  </TR>
  <TR>
    <td colspan=8 class="verm">Esse valor ultrapassou o valor do prêmio máximo</td>   
  </TR-->

<?php //}
?>
  <TR>
    <td colspan=7>&nbsp;</td>   
  </TR>
  <TR>
    <td colspan=7 align="center"><input type=button value="Voltar" onClick="this.form.comm.value='voltar';this.form.submit()" class="servicos">
</td>   
  </TR>
</table>

</form>

